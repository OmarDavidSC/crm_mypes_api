<?php

namespace App\Dows;

use App\Constants\QuotationConstant;
use App\Middlewares\Application;
use App\Repositories\QuotationRepository;
use App\Services\QuotationService;
use App\Utilities\FG;
use App\Validators\QuotationValidator;
use Illuminate\Database\Capsule\Manager as DB;

class QuotationDow {
    private QuotationRepository $quotationRepository;
    private QuotationService $quotationService;

    public function __construct() {
        $this->quotationRepository = new QuotationRepository();
        $this->quotationService =  new QuotationService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $company_id = Application::getItem('company_id');
            $quotations = $this->quotationRepository->getAllByCompany($company_id);

            $data = $quotations->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quotation_number' => $item->quotation_number,
                    'quotation_date' => $item->quotation_date,
                    'expiration_date' => $item->expiration_date,
                    'currency' => $item->currency,
                    'subtotal' => (float)$item->subtotal,
                    'discount' => (float)$item->discount,
                    'tax' => (float)$item->tax,
                    'total' => (float)$item->total,
                    'quotation_status' => $item->quotation_status,
                    'customer' => $item->customer ? [
                                'id' => $item->customer->id,
                                'name' => $item->customer->name,
                                'business_name' => $item->customer->business_name,] : null,
                    'lead' => $item->lead ? [
                                'id' => $item->lead->id,
                                'name' => $item->lead->name,] : null,
                    'opportunity' => $item->opportunity ? [
                                'id' => $item->opportunity->id,
                                'title' => $item->opportunity->title,] : null,
                    'assigned_user' => $item->assignedUser ? [
                                'id' => $item->assignedUser->id,
                                'name' => $item->assignedUser->name,] : null,
                    'created_at' => $item->created_at,
                ];
            });
          
            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Cotizaciones obtenidas correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request) {
        $response = FG::responseDefault();
        try {
            $id = (int)  $request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $quotation = $this->quotationRepository->getById($id, $company_id);
            if(!$quotation) {
                $response['success'] = false;
                $response['message'] = 'La cotización no fue encontrada.';
                return $response;
            }

            $response['success'] = true;
            $response['data'] = $quotation;
            $response['message'] = 'Cotización obtenida correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function store($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = QuotationValidator::store($input);
            if(!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $quotation = $this->quotationService->create($input, $company_id,$user_id);
            DB::commit();         

            $response['success'] = true;
            $response['data'] = $quotation;
            $response['message'] = 'Cotización registrada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function update($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $id = (int)  $request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = QuotationValidator::changeStatus($input);
            if(!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $quotation = $this->quotationRepository->getById($id, $company_id);
            if(!$quotation) {
                $response['success'] = false;
                $response['message'] = 'La cotización no fue encontrada.';
                return $response;
            }

            $quotation = $this->quotationService->updateDraft($quotation, $input);
            DB::commit();  

            $response['success'] = true;
            $response['data'] = $quotation;
            $response['message'] = 'Cotización actualizada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function chagestatus($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $id = (int)  $request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = QuotationValidator::changeStatus($input);
            if(!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $quotation = $this->quotationRepository->getById($id, $company_id);
            if(!$quotation) {
                $response['success'] = false;
                $response['message'] = 'La cotización no fue encontrada.';
                return $response;
            }

            $quotation = $this->quotationService->changeStatus(
                            $quotation, $input['quotation_status'], 
                            $user_id, $input['notes'] ?? null
                        );
            DB::commit();  

            $response['success'] = true;
            $response['data'] = $quotation;
            $response['message'] = 'Estado de la cotización actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function remove($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)  $request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $quotation = $this->quotationRepository->getById($id, $company_id);
            if(!$quotation) {
                $response['success'] = false;
                $response['message'] = 'La cotización no fue encontrada.';
                return $response;
            }

            if($quotation->quotation_status !== QuotationConstant::STATUS_DRAFT) {
                $response['success'] = false;
                $response['message'] = 'Solo las cotizaciones en borrador pueden eliminarse.';
                return $response;
            }

            foreach ($quotation->items() as $item) {
                $item->delete();
            }

            $quotation->delete();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Cotización eliminada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
