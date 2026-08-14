<?php

namespace App\Services;

use App\Constants\CustomerConstant;
use App\Models\Customers;
use App\Repositories\CustomerRepository;
use App\Utilities\FG;

class CustomerService {
    private CustomerRepository $customerRepository;

    public function __construct() {
        $this->customerRepository = new CustomerRepository();
    }

    public function create(array $input, int $company_id, ?int $user_id = null): Customers {
        $customerType = strtoupper(trim($input['customer_type']));
        $this->validateCustomerType($customerType);

        $documentType = null;
        if (!empty($input['document_type'])) {
            $documentType = strtoupper(trim($input['document_type']));
            $this->validateDocumentType($documentType);
        }

        $documentNumber = !empty($input['document_number']) ? trim($input['document_number']) : null;
        if ( $documentType === CustomerConstant::DOCUMENT_DNI && strlen($documentNumber ?? '') !== 8) {
            throw new \Exception('El DNI debe contener 8 dígitos.');
        }
        if ($documentType === CustomerConstant::DOCUMENT_RUC && strlen($documentNumber ?? '') !== 11) {
            throw new \Exception('El RUC debe contener 11 dígitos.');
        }

        $this->validateDuplicates($company_id, $documentNumber, $input['email'] ?? null, $input['phone'] ?? null, $input['whatsapp'] ?? null);

        $customer = new Customers();
        $customer->company_id = $company_id;
        $customer->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : $user_id;
        $customer->customer_type = $customerType;
        $customer->name = trim($input['name']);
        $customer->business_name = !empty($input['business_name']) ? trim($input['business_name']) : null;
        $customer->document_type = $documentType;
        $customer->document_number = $documentNumber;
        $customer->email = !empty($input['email']) ? strtolower(trim($input['email'])) : null;
        $customer->phone = !empty($input['phone']) ? trim($input['phone']) : null;
        $customer->whatsapp = !empty($input['whatsapp']) ? trim($input['whatsapp']) : null;
        $customer->address = !empty($input['address']) ? trim($input['address']) : null;
        $customer->source = !empty($input['source']) ? strtoupper(trim($input['source'])) : null;
        $customer->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        $customer->status = 1;
        $customer->save();
        return $customer->fresh();
    }

    public function update(Customers $customer, array $input): Customers {
        if (array_key_exists('customer_type', $input)) {
            $type = strtoupper(trim($input['customer_type']));
            $this->validateCustomerType($type);
            $customer->customer_type = $type;
        }

        if (array_key_exists('assigned_user_id', $input)) {
            $customer->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : null;
        }

        if (array_key_exists('name', $input)) {
            $name = trim($input['name']);
            if (empty($name)) {
                throw new \Exception('El nombre del cliente es obligatorio.');
            }
            $customer->name = $name;
        }

        if (array_key_exists('business_name', $input)) {
            $customer->business_name = !empty($input['business_name']) ? trim($input['business_name']) : null;
        }

        if (array_key_exists('document_type', $input)) {
            if (!empty($input['document_type'])) {
                $type = strtoupper(trim($input['document_type']));
                $this->validateDocumentType($type);
                $customer->document_type = $type;
            } else {
                $customer->document_type = null;
            }
        }

        if (array_key_exists('document_number', $input)) {
            $documentNumber = !empty($input['document_number']) ? trim($input['document_number']) : null;
            if ($documentNumber) {
                $existing = $this->customerRepository->getByDocument($documentNumber, $customer->company_id);
                if ($existing && $existing->id !== $customer->id) {
                    throw new \Exception('Ya existe otro cliente con este número de documento.');
                }
            }
            $customer->document_number = $documentNumber;
        }

        if (array_key_exists('email', $input)) {
            $email = !empty($input['email']) ? strtolower(trim($input['email'])) : null;
            if ($email) {
                $existing = $this->customerRepository->getByEmail($email, $customer->company_id);
                if ($existing && $existing->id !== $customer->id) {
                    throw new \Exception('Ya existe otro cliente con este correo electrónico.');
                }
            }
            $customer->email = $email;
        }

        if (array_key_exists('phone', $input)) {
            $customer->phone = !empty($input['phone']) ? trim($input['phone']) : null;
        }

        if (array_key_exists('whatsapp', $input)) {
            $customer->whatsapp = !empty($input['whatsapp']) ? trim($input['whatsapp']) : null;
        }

        if (array_key_exists('address', $input)) {
            $customer->address = !empty($input['address']) ? trim($input['address']) : null;
        }

        if (array_key_exists('source', $input)) {
            $customer->source = !empty($input['source']) ? strtoupper( trim($input['source'])) : null;
        }

        if (array_key_exists('notes', $input)) {
            $customer->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        }

        if (array_key_exists('status', $input)) {
            $customer->status = (int)$input['status'];
        }
        $customer->save();
        return $customer->fresh();
    }

    private function validateCustomerType(string $type): void {

        if (!in_array($type, CustomerConstant::types(), true)) {
            throw new \Exception('El tipo de cliente no es válido.');
        }
    }

    private function validateDocumentType(string $type): void {
        if (!in_array($type, CustomerConstant::documentTypes(), true)) {
            throw new \Exception('El tipo de documento no es válido.');
        }
    }

    private function validateDuplicates(int $company_id, ?string $document, ?string $email, ?string $phone, ?string $whatsapp): void {

        if (!empty($document)) {
            $existing = $this->customerRepository->getByDocument(trim($document), $company_id);
            if ($existing) {
                throw new \Exception('Ya existe un cliente con este número de documento.');
            }
        }

        if (!empty($email)) {
            $existing = $this->customerRepository->getByEmail(strtolower(trim($email)), $company_id);
            if ($existing) {
                throw new \Exception('Ya existe un cliente con este correo electrónico.');
            }
        }

        foreach ([$phone, $whatsapp] as $number) {
            if (empty($number)) {
                continue;
            }
            $existing = $this->customerRepository->getByPhone(trim($number), $company_id);
            if ($existing) {
                throw new \Exception('Ya existe un cliente con este teléfono o WhatsApp.');
            }
        }
    }
}