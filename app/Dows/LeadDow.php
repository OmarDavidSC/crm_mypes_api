<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\LeadRepository;
use App\Services\LeadService;
use App\Utilities\FG;
use App\Validators\LeadValidator;
use Illuminate\Database\Capsule\Manager as DB;

class LeadDow {
    private LeadRepository $leadRepository;
    private LeadService $leadService;

    public function __construct() {
        $this->leadRepository = new LeadRepository();
        $this->leadService = new LeadService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $leads = $this->leadRepository->getAllByCompany($company_id);

            $data = $leads->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'assigned_user_id' => $lead->assigned_user_id,
                    'name' => $lead->name,
                    'business_name' => $lead->business_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'whatsapp' => $lead->whatsapp,
                    'source' => $lead->source,
                    'interest' => $lead->interest,
                    'estimated_value' => (float)$lead->estimated_value,
                    'lead_status' => $lead->lead_status,
                    'converted' => (bool)$lead->converted,
                    'status' => $lead->status,
                    'created_at' => FG::formatDateTimeHuman($lead->created_at),
                ];
            });

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Prospectos obtenidos correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $lead = $this->leadRepository->getById($id, $company_id);
            if(!$lead) {
                $response['success'] = false;
                $response['message'] = 'El prospecto no fue encontrado.';
                return $response;
            }

            $response['success'] = true;
            $response['data'] = [
                'id' => $lead->id,
                'assigned_user_id' => $lead->assigned_user_id,
                'name' => $lead->name,
                'business_name' => $lead->business_name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'whatsapp' => $lead->whatsapp,
                'source' => $lead->source,
                'interest' => $lead->interest,
                'estimated_value' => (float)$lead->estimated_value,
                'notes' => $lead->notes,
                'lead_status' => $lead->lead_status,
                'converted' => (bool)$lead->converted,
                'converted_customer_id' => $lead->converted_customer_id,
                'converted_at' => $lead->converted_at,
                'status' => $lead->status,
            ];
            $response['message'] = 'Prospecto obtenido correctamente.';
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

            $erros = LeadValidator::store($input);
            if(!empty($erros)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $erros);
                return $response;
            }

            $lead = $this->leadService->create($input, $company_id, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $lead;
            $response['message'] = 'Prospecto registrado correctamente.';
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
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $erros = LeadValidator::update($input);
            if(!empty($erros)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $erros);
                return $response;
            }

            $lead = $this->leadRepository->getById($id, $company_id);
            if(!$lead) {
                $response['success'] = false;
                $response['message'] = 'El prospecto no fue encontrado.';
                return $response;
            }

            $lead = $this->leadService->update($lead,$input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $lead;
            $response['message'] = 'Prospecto actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function cstatus($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $erros = LeadValidator::changeStatus($input);
            if(!empty($erros)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $erros);
                return $response;
            }

            $lead = $this->leadRepository->getById($id, $company_id);
            if(!$lead) {
                $response['success'] = false;
                $response['message'] = 'El prospecto no fue encontrado.';
                return $response;
            }

            $lead = $this->leadService->changeStatus($lead,$input['lead_status']);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $lead;
            $response['message'] = 'Estado actualizado correctamente.';
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
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $lead = $this->leadRepository->getById($id, $company_id);
            if(!$lead) {
                $response['success'] = false;
                $response['message'] = 'El prospecto no fue encontrado.';
                return $response;
            }

            if(!$lead->converted) {
                $response['success'] = false;
                $response['message'] = 'No se puede eliminar un prospecto ya convertido.';
                return $response;
            }

            $lead->deleted_at = FG::getDateHour();
            $lead->save();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Prospecto eliminado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
