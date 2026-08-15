<?php

namespace App\Services;

use App\Constants\ActivityConstant;
use App\Constants\QuotationConstant;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationStatusHistory;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\ProductServiceRepository;
use App\Repositories\QuotationRepository;
use App\Utilities\FG;
use Exception;

class QuotationService {
    private QuotationRepository $quotationRepository;
    private CustomerRepository $customerRepository;
    private LeadRepository $leadRepository;
    private OpportunityRepository $opportunityRepository;
    private ProductServiceRepository $productServiceRepository;

    private ActivityService $activityService;

    public function __construct() {
        $this->quotationRepository = new QuotationRepository();
        $this->customerRepository = new CustomerRepository();
        $this->leadRepository = new LeadRepository();
        $this->opportunityRepository = new OpportunityRepository();
        $this->productServiceRepository = new ProductServiceRepository();
        $this->activityService = new ActivityService();
    }

    public function create(array $input, int $company_id, int $user_id): Quotation {
        $this->validateRelations($input, $company_id);

        $currency = strtoupper(trim($input['currency'] ?? QuotationConstant::CURRENCY_PEN));
        if (!in_array($currency, QuotationConstant::currencies(), true)) {
            throw new \Exception('La moneda seleccionada no es válida.');
        }

        if (!empty($input['expiration_date']) && strtotime($input['expiration_date']) < strtotime($input['quotation_date'])) {
            throw new \Exception('La fecha de vencimiento no puede ser anterior a la fecha de cotización.');
        }

        $items = $this->parseItems($input['items']);
        if (empty($items)) {
            throw new \Exception('Debe agregar al menos un producto o servicio.');
        }

        $calculatedItems = $this->calculateItems($items, $company_id);
        $itemsSubtotal = round(array_sum(array_column($calculatedItems, 'total')), 2);
        $generalDiscount = round((float)($input['discount'] ?? 0), 2);
        if ($generalDiscount < 0) {
            throw new \Exception('El descuento general no puede ser negativo.');
        }

        if ($generalDiscount > $itemsSubtotal) {
            throw new \Exception('El descuento general no puede superar el importe de la cotización.');
        }

        $itemsTax = round(array_sum(array_column($calculatedItems, 'tax')), 2);
        $itemsBaseSubtotal = round(array_sum(array_column($calculatedItems, 'subtotal')), 2);
        $total = round($itemsSubtotal - $generalDiscount, 2);

        $quotation = new Quotation();
        $quotation->company_id = $company_id;
        $quotation->opportunity_id = !empty($input['opportunity_id']) ? (int)$input['opportunity_id'] : null;
        $quotation->customer_id = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $quotation->lead_id = !empty($input['lead_id']) ? (int)$input['lead_id'] : null;
        $quotation->created_by = $user_id;
        $quotation->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : $user_id;
        $quotation->quotation_number = $this->generateQuotationNumber($company_id);
        $quotation->quotation_date = $input['quotation_date'];
        $quotation->expiration_date = !empty($input['expiration_date']) ? $input['expiration_date'] : null;
        $quotation->currency = $currency;
        $quotation->subtotal = $itemsBaseSubtotal;
        $quotation->discount = $generalDiscount;
        $quotation->tax = $itemsTax;
        $quotation->total = $total;
        $quotation->quotation_status = QuotationConstant::STATUS_DRAFT;
        $quotation->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        $quotation->terms_conditions = !empty($input['terms_conditions']) ? trim($input['terms_conditions']) : null;
        $quotation->sent_at = null;
        $quotation->accepted_at = null;
        $quotation->rejected_at = null;
        $quotation->status = 1;
        $quotation->save();

        foreach ($calculatedItems as $item) {
            $detail = new QuotationItem();
            $detail->quotation_id = $quotation->id;
            $detail->product_service_id = $item['product_service_id'];
            $detail->description = $item['description'];
            $detail->quantity = $item['quantity'];
            $detail->unit_price = $item['unit_price'];
            $detail->discount = $item['discount'];
            $detail->tax_percentage = $item['tax_percentage'];
            $detail->subtotal = $item['subtotal'];
            $detail->tax = $item['tax'];
            $detail->total = $item['total'];
            $detail->save();
        }

        $this->registerStatusHistory($quotation, $user_id, null, QuotationConstant::STATUS_DRAFT, 'Cotización creada.');
        $this->activityService->createSystemActivity(
                    $company_id,
                    $user_id,
                    ActivityConstant::TYPE_QUOTATION_CREATED,
                    'Cotización creada',
                    "Se creó la cotización {$quotation->quotation_number}.",
                    $quotation->lead_id,
                    $quotation->customer_id,
                    $quotation->opportunity_id
            );
        return $quotation->fresh(['items.productService']);
    }

    public function changeStatus(Quotation $quotation, string $newStatus, int $user_id, ?string $notes = null): Quotation {
        $newStatus = strtoupper(trim($newStatus));
        if (!in_array($newStatus, QuotationConstant::statuses(), true)) {
            throw new \Exception('El estado de la cotización no es válido.');
        }

        $previousStatus = $quotation->quotation_status;
        if ($previousStatus === $newStatus) {
            throw new \Exception('La cotización ya se encuentra en este estado.');
        }

        if (in_array($previousStatus, QuotationConstant::finalStatuses(), true)) {
            throw new \Exception('Una cotización finalizada no puede cambiar de estado.');
        }

        $this->validateStatusTransition($previousStatus, $newStatus);
        $quotation->quotation_status = $newStatus;

        if ($newStatus === QuotationConstant::STATUS_SENT) {
            $quotation->sent_at = FG::getDateHour();
        }
        if ($newStatus === QuotationConstant::STATUS_ACCEPTED) {
            $quotation->accepted_at = FG::getDateHour();
            $quotation->rejected_at = null;
        }

        if ($newStatus === QuotationConstant::STATUS_REJECTED) {
            $quotation->rejected_at = FG::getDateHour();
            $quotation->accepted_at = null;
        }
        $quotation->save();
        $this->registerStatusHistory($quotation, $user_id, $previousStatus, $newStatus, $notes);

        $activityType = match ($newStatus) {
                    QuotationConstant::STATUS_SENT => ActivityConstant::TYPE_QUOTATION_SENT,
                    QuotationConstant::STATUS_VIEWED => ActivityConstant::TYPE_QUOTATION_VIEWED,
                    QuotationConstant::STATUS_ACCEPTED => ActivityConstant::TYPE_QUOTATION_ACCEPTED,
                    QuotationConstant::STATUS_REJECTED => ActivityConstant::TYPE_QUOTATION_REJECTED,
                    QuotationConstant::STATUS_EXPIRED => ActivityConstant::TYPE_QUOTATION_EXPIRED,
                    QuotationConstant::STATUS_CANCELLED => ActivityConstant::TYPE_QUOTATION_CANCELLED,
                    default => ActivityConstant::TYPE_SYSTEM,
            };

        $this->activityService->createSystemActivity(
                    $quotation->company_id,
                    $user_id,
                    $activityType,
                    'Estado de cotización actualizado',
                    "La cotización {$quotation->quotation_number} cambió de {$previousStatus} a {$newStatus}.",
                    $quotation->lead_id,
                    $quotation->customer_id,
                    $quotation->opportunity_id
            );
        return $quotation->fresh(['items.productService', 'statusHistory.user']);
    }

    public function updateDraft(Quotation $quotation, array $input): Quotation {
        if($quotation->quotation_status !== QuotationConstant::STATUS_DRAFT) {
            throw new \Exception('Solo las cotizaciones en borrador pueden modificarse.');
        }

        //fechas
        $quotationDate = !empty($input['quotation_date']) ? $input['quotation_date'] : $quotation->quotation_date; 
        $expirationDate = array_key_exists('expiration_date', $input) 
                            ? (!empty($input['expiration_date']) ? $input['expiration_date'] : null)
                            : $quotation->expiration_date;
                            
        if(!empty($expirationDate) && strtotime($expirationDate) < strtotime($quotationDate)) {
            throw new Exception('La fecha de vencimiento no puede ser anterior a la fecha de cotización.');
        }

        //moneda
        if(array_key_exists('currency', $input)) {
            $currency = strtoupper(trim($input['currency']));
            if(!in_array($currency, QuotationConstant::currencies(), true)) {
                throw new Exception('La moneda seleccionada no es válida.');
            }
            $quotation->currency = $currency;
        }

        //usuario asignado
        if(array_key_exists('assigned_user_id', $input)) {
            $quotation->assigned_user_id = !empty($input['assigned_user_id']) ? (int) $input['assigned_user_id'] : null;
        }
        $quotation->quotation_date = $quotationDate;
        $quotation->expiration_date = $expirationDate;

        if(array_key_exists('notes', $input)) {
            $quotation->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        }
        if(array_key_exists('terms_conditions', $input)) {
            $quotation->terms_conditions = !empty($input['terms_conditions']) ? trim($input['terms_conditions']) : null;
        }

        //si no hay items modificaciones solo la cabecera
        if(!array_key_exists('items', $input)) {
            $quotation->save();
            return $quotation->fresh(['items.productService', 'statusHistory.user']);
        }

        //si llegan items recalculamos todo de nuevo
        $items = $this->parseItems($input['items']);
        if(empty($items)) {
            throw new Exception('La cotización debe de contener al menos un producto o servicio.');
        }

        $calculatedItems = $this->calculateItems($input['items'], $quotation->company_id);
        $itemsSubtotal = round(array_sum(array_column($calculatedItems, 'total')));
        $generalDiscount = array_key_exists('discount', $input) ? round((float) $input['discount'], 2)
                                                            : round((float) $quotation->discount, 2);

        if($generalDiscount > $itemsSubtotal) {
            throw new Exception('El descuento general no puede superar el importe de la cotización.');
        }

        $itemsTax = round(array_sum(array_column($calculatedItems, 'tax')));
        $itemsBaseSubtotal = round(array_sum(array_column($calculatedItems, 'subtotal')));
        $total = round($itemsSubtotal - $generalDiscount, 2);

        $quotation->subtotal = $itemsBaseSubtotal;
        $quotation->tax = $itemsTax;
        $quotation->discount = $generalDiscount;
        $quotation->total = $total;
        $quotation->save();

        //eliminamos los items anteriores logicamente
        foreach ($quotation->items as $oldItem) {
            $oldItem->delete();
        }

        foreach ($calculatedItems as $item) {
            $detail = new QuotationItem();
            $detail->quotation_id = $quotation->id;
            $detail->product_service_id = $item['product_service_id'];
            $detail->description = $item['description'];
            $detail->quantity = $item['quantity'];
            $detail->unit_price = $item['unit_price'];
            $detail->discount = $item['discount'];
            $detail->tax_percentage = $item['tax_percentage'];
            $detail->subtotal = $item['subtotal'];
            $detail->tax = $item['tax'];
            $detail->total = $item['total'];
            $detail->save();
        }
        return $quotation->fresh(['items.productService', 'statusHistory.user']);
    }

    private function parseItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }
        if (!is_string($items)) {
            throw new \Exception('El detalle de la cotización no es válido.' );
        }
        
        $decoded = json_decode($items, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new \Exception('El detalle de la cotización no contiene un JSON válido.');
        }
        return $decoded;
    }

    private function calculateItems(array $items, int $company_id): array {
        $result = [];
        foreach ($items as $index => $item) {
            if (empty($item['product_service_id'])) {
                throw new \Exception('Seleccione un producto o servicio en el ítem ' . ($index + 1) . '.');
            }
            $productService = $this->productServiceRepository->getById((int)$item['product_service_id'], $company_id);
            if (!$productService || (int)$productService->status !== 1) {
                throw new \Exception('El producto o servicio del ítem ' . ($index + 1) . ' no se encuentra disponible.');
            }
            $quantity = round((float)($item['quantity'] ?? 0), 2);
            if ($quantity <= 0) {
                throw new \Exception("La cantidad de {$productService->name} debe ser mayor a cero.");
            }
            $unitPrice = isset($item['unit_price']) ? round((float)$item['unit_price'], 2) : round((float)$productService->price, 2);
            if ($unitPrice < 0) {
                throw new \Exception("El precio de {$productService->name} no puede ser negativo.");
            }
            $discount = round((float)($item['discount'] ?? 0), 2);
            if ($discount < 0) {
                throw new \Exception('El descuento no puede ser negativo.');
            }
            $grossAmount = round($quantity * $unitPrice, 2);
            if ($discount > $grossAmount) {
                throw new \Exception("El descuento de {$productService->name} no puede superar su importe.");
            }

            $subtotal = round($grossAmount - $discount, 2);
            $taxPercentage = round((float)$productService->tax_percentage, 2);
            $tax = round($subtotal * ($taxPercentage / 100), 2);
            $total = round($subtotal + $tax, 2);

            $result[] = [
                'product_service_id' => $productService->id,
                'description' => !empty($item['description']) ? trim($item['description']) : $productService->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'tax_percentage' => $taxPercentage,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ];
        }
        return $result;
    }

    private function validateRelations(array $input, int $company_id): void {
        if (empty($input['opportunity_id']) && empty($input['customer_id']) && empty($input['lead_id'])) {
            throw new \Exception('La cotización debe estar asociada a una oportunidad, cliente o prospecto.');
        }
        if (!empty($input['opportunity_id'])) {
            $opportunity = $this->opportunityRepository->getById((int)$input['opportunity_id'], $company_id);
            if (!$opportunity) {
                throw new \Exception('La oportunidad seleccionada no existe.');
            }
        }
        if (!empty($input['customer_id'])) {
            $customer = $this->customerRepository->getById((int)$input['customer_id'], $company_id);
            if (!$customer) {
                throw new \Exception('El cliente seleccionado no existe.');
            }
        }
        if (!empty($input['lead_id'])) {
            $lead = $this->leadRepository->getById((int)$input['lead_id'], $company_id);
            if (!$lead) {
                throw new \Exception('El prospecto seleccionado no existe.');
            }
        }
    }

    private function generateQuotationNumber(int $company_id): string {
       $lastQuotation = $this->quotationRepository->getLastQuotationByCompany($company_id);

        if (!$lastQuotation) {
            $next = 1;
        } else {
            $number = str_replace('COT-', '', $lastQuotation->quotation_number);
            $next = ((int)$number) + 1;
        }
        return 'COT-' .str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }

    private function registerStatusHistory(Quotation $quotation, int $user_id, ?string $previousStatus, string $newStatus, ?string $notes = null): void {
        $history = new QuotationStatusHistory();
        $history->company_id = $quotation->company_id;
        $history->quotation_id = $quotation->id;
        $history->user_id = $user_id;
        $history->previous_status = $previousStatus;
        $history->new_status = $newStatus;
        $history->notes = $notes;
        $history->changed_at = FG::getDateHour();
        $history->save();
    }

    private function validateStatusTransition(string $current, string $new): void {
        $allowed = [
            QuotationConstant::STATUS_DRAFT => [ 
                    QuotationConstant::STATUS_SENT, 
                    QuotationConstant::STATUS_CANCELLED, 
                ],
            QuotationConstant::STATUS_SENT => [
                    QuotationConstant::STATUS_VIEWED,
                    QuotationConstant::STATUS_ACCEPTED,
                    QuotationConstant::STATUS_REJECTED,
                    QuotationConstant::STATUS_EXPIRED,
                    QuotationConstant::STATUS_CANCELLED,
            ],
            QuotationConstant::STATUS_VIEWED => [
                    QuotationConstant::STATUS_ACCEPTED,
                    QuotationConstant::STATUS_REJECTED,
                    QuotationConstant::STATUS_EXPIRED,
                    QuotationConstant::STATUS_CANCELLED,
            ],
        ];

        if (!isset($allowed[$current]) || !in_array($new, $allowed[$current], true)) {
            throw new \Exception("No se puede cambiar la cotización de {$current} a {$new}.");
        }
    }
}