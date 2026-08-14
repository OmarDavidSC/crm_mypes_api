<?php

namespace App\Services;

use App\Constants\LeadConstant;
use App\Models\Lead;
use App\Repositories\LeadRepository;

class LeadService {

    private LeadRepository $leadRepository;

    public function __construct()
    {
        $this->leadRepository = new LeadRepository();
    }

    public function create(array $input, int $company_id, ?int $user_id = null): Lead {
        $this->validateSource($input['source'] ?? null);
        $this->validateDuplicatedLead($company_id, $input['email'] ?? null, $input['phone'] ?? null, $input['whatsapp'] ?? null);

        $lead = new Lead();
        $lead->company_id = $company_id;
        $lead->assigned_user_id = !empty($input['assigned_user_id'])? (int)$input['assigned_user_id'] : $user_id;
        $lead->name = trim($input['name']);
        $lead->business_name = !empty($input['business_name'])? trim($input['business_name']) : null;
        $lead->email = !empty($input['email'])? strtolower(trim($input['email'])) : null;
        $lead->phone = !empty($input['phone'])? trim($input['phone']) : null;
        $lead->whatsapp = !empty($input['whatsapp'])? trim($input['whatsapp']) : null;
        $lead->source = !empty($input['source']) ? strtoupper(trim($input['source'])) : LeadConstant::SOURCE_MANUAL;
        $lead->interest = !empty($input['interest']) ? trim($input['interest']) : null;
        $lead->estimated_value = isset($input['estimated_value']) ? round((float)$input['estimated_value'], 2) : null;
        $lead->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        $lead->lead_status = LeadConstant::STATUS_NEW;
        $lead->converted = 0;
        $lead->converted_customer_id = null;
        $lead->converted_at = null;
        $lead->status = 1;
        $lead->save();
        return $lead;
    }

    public function update(Lead $lead, array $input): Lead {
        if($lead->converted) {
            throw new \Exception('No se puede modificar un prospecto convertido.');
        }
        if(array_key_exists('source', $input)) {
            $this->validateSource($input['source']);
        }
        if (array_key_exists('assigned_user_id', $input)) {
            $lead->assigned_user_id =
                !empty($input['assigned_user_id'])
                    ? (int)$input['assigned_user_id']
                    : null;
        }
        if (array_key_exists('name', $input)) {
            $lead->name = trim($input['name']);
        }
        if (array_key_exists('business_name', $input)) {
            $lead->business_name = !empty($input['business_name']) ? trim($input['business_name']) : null;
        }
        if (array_key_exists('email', $input)) {
            $lead->email = !empty($input['email']) ? strtolower(trim($input['email'])) : null;
        }
        if (array_key_exists('phone', $input)) {
            $lead->phone = !empty($input['phone']) ? trim($input['phone']) : null;
        }
        if (array_key_exists('whatsapp', $input)) {
            $lead->whatsapp = !empty($input['whatsapp']) ? trim($input['whatsapp']) : null;
        }
        if (array_key_exists('source', $input)) {
            $lead->source = !empty($input['source']) ? strtoupper(trim($input['source'])) : LeadConstant::SOURCE_MANUAL;
        }
        if (array_key_exists('interest', $input)) {
            $lead->interest = !empty($input['interest']) ? trim($input['interest']) : null;
        }
        if (array_key_exists('estimated_value', $input)) {
            $lead->estimated_value = $input['estimated_value'] !== '' ? round((float)$input['estimated_value'], 2) : null;
        }
        if (array_key_exists('notes', $input)) {
            $lead->notes = !empty($input['notes']) ? trim($input['notes']) : null;
        }

        $lead->save();
        return $lead->fresh();
    }

    public function changeStatus(Lead $lead, string $lead_status): Lead {
        if ($lead->converted) {
            throw new \Exception('El prospecto ya fue convertido.');
        }

        $lead_status = strtoupper(trim($lead_status));

        if (!in_array($lead_status, LeadConstant::statuses(), true)) {
            throw new \Exception('El estado del prospecto no es válido.');
        }
        if ($lead_status === LeadConstant::STATUS_CONVERTED) {
            throw new \Exception('Para convertir un prospecto debe utilizar el proceso de conversión.');
        }
        $lead->lead_status = $lead_status;
        $lead->save();
        return $lead->fresh();
    }

    private function validateSource(?string $source): void {
        if(empty($source)) {
            return;
        }

        $source = strtoupper(trim($source));
        if(!in_array($source, LeadConstant::sources(), true)) {
            throw new \Exception("El origin del prospecto no es válido.");
        }
    }

    private function validateDuplicatedLead(int $company_id, ?string $email, ?string $phone, ?string $whatsapp) {
        if(!empty($email)){
            $existingLead = $this->leadRepository->getByEmail(strtolower(trim($email)), $company_id);
            if($existingLead){
                throw new \Exception("Ya existe un prospecto registrado con este correo electronico.");
            }
        }

        if(!empty($phone)){
            $existingLead = $this->leadRepository->getByPhone(trim($phone), $company_id);
            if($existingLead){
                throw new \Exception("Ya existe un prospecto registrado con este teléfono.");
            }
        }

        if(!empty($whatsapp)){
            $existingLead = $this->leadRepository->getByPhone(trim($whatsapp), $company_id);
            if($existingLead){
                throw new \Exception("Ya existe un prospecto registrado con este WhatsApp.");
            }
        }
    }
}