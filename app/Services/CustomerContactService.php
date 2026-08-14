<?php

namespace App\Services;

use App\Models\CustomerContact;
use App\Models\Customers;
use App\Repositories\CustomerContactRepository;
use App\Utilities\FG;

class CustomerContactService {
    private CustomerContactRepository $customerContactRepository;

    public function __construct()
    {
        $this->customerContactRepository = new CustomerContactRepository();
    }

    public function create(Customers $customer, array $input): CustomerContact {
        $isPrimary = (int)($input['is_primary'] ?? 0);

        $contacts = $this->customerContactRepository->getByCustomer($customer->id, $customer->company_id);
        if ($contacts->isEmpty()) {
            $isPrimary = 1;
        }

        if ($isPrimary === 1) {
            $this->removeCurrentPrimary($customer->id,$customer->company_id);
        }

        $contact = new CustomerContact();
        $contact->company_id = $customer->company_id;
        $contact->customer_id = $customer->id;
        $contact->name = trim($input['name']);
        $contact->position = !empty($input['position']) ? trim($input['position']) : null;
        $contact->email = !empty($input['email']) ? strtolower(trim($input['email'])) : null;
        $contact->phone = !empty($input['phone']) ? trim($input['phone']) : null;
        $contact->whatsapp = !empty($input['whatsapp']) ? trim($input['whatsapp']) : null;
        $contact->is_primary = $isPrimary;
        $contact->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        $contact->status = 1;
        $contact->save();
        return $contact->fresh();
    }

    public function update(CustomerContact $contact, array $input): CustomerContact {
        if (array_key_exists('name', $input)) {
            $name = trim($input['name']);
            if (empty($name)) {
                throw new \Exception('El nombre del contacto es obligatorio.');
            }
            $contact->name = $name;
        }

        if (array_key_exists('position', $input)) {
            $contact->position = !empty($input['position']) ? trim($input['position']) : null;
        }

        if (array_key_exists('email', $input)) {
            $contact->email = !empty($input['email']) ? strtolower(trim($input['email'])) : null;
        }

        if (array_key_exists('phone', $input)) {
            $contact->phone = !empty($input['phone']) ? trim($input['phone']) : null;
        }

        if (array_key_exists('whatsapp', $input)) {
            $contact->whatsapp = !empty($input['whatsapp']) ? trim($input['whatsapp']) : null;
        }

        if (isset($input['is_primary']) && (int)$input['is_primary'] === 1) {
            $this->removeCurrentPrimary($contact->customer_id, $contact->company_id);
            $contact->is_primary = 1;
        }

        if (array_key_exists('notes', $input)) {
            $contact->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        }

        if (array_key_exists('status', $input)) {
            $contact->status = (int)$input['status'];
        }
        $contact->save();
        return $contact->fresh();
    }

    private function removeCurrentPrimary(int $customer_id, int $company_id): void {
        $primary = $this->customerContactRepository->getPrimary($customer_id,$company_id);
        if (!$primary) {
            return;
        }
        $primary->is_primary = 0;
        $primary->save();
    }
}