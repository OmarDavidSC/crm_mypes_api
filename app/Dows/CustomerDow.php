<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerRepository;
use App\Services\CustomerContactService;
use App\Services\CustomerService;
use App\Utilities\FG;
use App\Validators\CustomerContactValidator;
use App\Validators\CustomerValidator;
use Illuminate\Database\Capsule\Manager as DB;

class CustomerDow {
    private CustomerRepository $customerRepository;
    private CustomerContactRepository $customerContactRepository;
    private CustomerService $customerService;
    private CustomerContactService $customerContactService;

    public function __construct() {
        $this->customerRepository = new CustomerRepository();
        $this->customerContactRepository = new CustomerContactRepository();
        $this->customerService = new CustomerService();
        $this->customerContactService = new CustomerContactService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $search = trim($input['search'] ?? '');
            if (!empty($search)) {
                $customers = $this->customerRepository->search($company_id,$search);
            } else {
                $customers = $this->customerRepository->getAllByCompany($company_id);
            }

            $data = $customers->map(function ($item) {
                    $primaryContact = $item->contacts?->firstWhere('is_primary',);

                    return [
                        'id' => $item->id,
                        'customer_type' => $item->customer_type,
                        'name' => $item->name,
                        'business_name' => $item->business_name,
                        'document_type' => $item->document_type,
                        'document_number' => $item->document_number,
                        'email' => $item->email,
                        'phone' => $item->phone,
                        'whatsapp' => $item->whatsapp,
                        'source' => $item->source,
                        'assigned_user' => $item->assignedUser ? [
                                    'id' => $item->assignedUser->id,
                                    'name' => $item->assignedUser->name,] : null,
                        'primary_contact' => $primaryContact ? [
                                    'id' => $primaryContact->id,
                                    'name' => $primaryContact->name,
                                    'position' => $primaryContact->position,
                                    'phone' => $primaryContact->phone,
                                    'whatsapp' => $primaryContact->whatsapp,] : null,
                        'status' => $item->status,
                        'created_at' => FG::formatDateTimeHuman($item->created_at),
                    ];
                });

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Clientes obtenidos correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request) {
        $response = FG::responseDefault();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $customer = $this->customerRepository->getByIdWithRelations($id,$company_id);

            if (!$customer) {
                $response['success'] = false;
                $response['message'] = 'El cliente no fue encontrado.';
                return $response;
            }

            $response['success'] = true;
            $response['data'] = $customer;
            $response['message'] = 'Cliente obtenido correctamente.';

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

            $errors = CustomerValidator::store($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $customer = $this->customerService->create($input, $company_id, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $customer;
            $response['message'] = 'Cliente registrado correctamente.';
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
            $id = (int)$request->getAttribute('id');
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $errors = CustomerValidator::update($input);

            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $customer = $this->customerRepository->getById($id, $company_id);
            if (!$customer) {
                $response['success'] = false;
                $response['message'] = 'El cliente no fue encontrado.';
                return $response;
            }

            $customer = $this->customerService->update($customer, $input);

            DB::commit();

            $response['success'] = true;
            $response['data'] = $customer;
            $response['message'] = 'Cliente actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function scontact($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $errors = CustomerContactValidator::store($input);

            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $customer = $this->customerRepository->getById((int)$input['customer_id'], $company_id);
            if (!$customer) {
                $response['success'] = false;
                $response['message'] = 'El cliente no fue encontrado.';
                return $response;
            }

            $contact = $this->customerContactService->create($customer,$input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $contact;
            $response['message'] = 'Contacto registrado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function ucontact($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $errors = CustomerContactValidator::update($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $contact = $this->customerContactRepository->getById($id, $company_id);
            if (!$contact) {
                $response['success'] = false;
                $response['message'] = 'El contacto no fue encontrado.';
                return $response;
            }

            $contact = $this->customerContactService->update($contact, $input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $contact;
            $response['message'] = 'Contacto actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function remove($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $customer = $this->customerRepository->getById($id, $company_id);
            if (!$customer) {
                $response['success'] = false;
                $response['message'] = 'El cliente no fue encontrado.';
                return $response;
            }

            if ($customer->opportunities()->exists() || $customer->quotations()->exists()) {
                $response['success'] = false;
                $response['message'] = 'El cliente tiene movimientos comerciales asociados y no puede eliminarse.';
                return $response;
            }

            $customer->delete();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Cliente eliminado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();

            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function dcontact($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id =  Application::getItem('company_id');

            $contact = $this->customerContactRepository->getById($id, $company_id);
            if (!$contact) {
                $response['success'] = false;
                $response['message'] = 'El contacto no fue encontrado.';
                return $response;
            }

            if ($contact->is_primary) {
                $response['success'] = false;
                $response['message'] = 'No se puede eliminar el contacto principal. Primero seleccione otro contacto principal.';
                return $response;
            }

            $contact->delete();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Contacto eliminado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}   