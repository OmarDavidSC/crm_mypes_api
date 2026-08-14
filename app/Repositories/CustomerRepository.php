<?php

namespace App\Repositories;

use App\Models\Customers;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Customers::query()
                ->with(['assignedUser', 'contacts'])
                ->where('company_id', $company_id)->where('status', $status)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getById(int $id, int $company_id): ?Customers {
        return Customers::query()
                ->with(['assignedUser', 'contacts'])
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByIdWithRelations(int $id, int $company_id): ?Customers {
        return Customers::query()
                ->with(['assignedUser', 'contacts', 'opportunities', 'activities', 'tasks', 'quotations'])
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByDocument(string $document_number, int $company_id): ?Customers {
        return Customers::query()
                ->where('company_id', $company_id)->where('document_number', $document_number)->whereNull('deleted_at')
                ->first();
    }

    public function getByEmail(string $email, int $company_id): ?Customers {
        return Customers::query()
                ->where('company_id', $company_id)->where('email', $email)->whereNull('deleted_at')->first();
    }

    public function getByPhone(string $phone, int $company_id): ?Customers {
        return Customers::query()
                ->where('company_id', $company_id)
                ->where(function ($query) use ($phone) {
                    $query->where('phone', $phone)
                        ->orWhere('whatsapp', $phone);
                })->whereNull('deleted_at')->first();
    }

    public function search(int $company_id, string $search): Collection {
        return Customers::query()
                ->where('company_id', $company_id)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('business_name', 'LIKE', "%{$search}%")
                            ->orWhere('document_number', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%")
                            ->orWhere('whatsapp', 'LIKE', "%{$search}%"
                            );
                })->orderBy('name', 'ASC')->get();
    }
}
