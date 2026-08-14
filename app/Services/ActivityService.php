<?php

namespace App\Services;

use App\Constants\ActivityConstant;
use App\Models\Activity;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OpportunityRepository;
use App\Utilities\FG;

class ActivityService {
    private LeadRepository $leadRepository;
    private OpportunityRepository $opportunityRepository;
    private CustomerRepository $customerRepository;

    public function __construct()
    {
        $this->leadRepository = new LeadRepository();
        $this->opportunityRepository = new OpportunityRepository();
        $this->customerRepository = new CustomerRepository();
    }

    public function create(array $input, int $company_id, ?int $user_id = null): Activity {
        $activityType = strtoupper(trim($input['activity_type']));

        if (!in_array($activityType, ActivityConstant::manualTypes(), true)) {
            throw new \Exception('El tipo de actividad no puede registrarse manualmente.');
        }

        $this->validateRelations($input, $company_id);
        if (empty($input['lead_id']) && empty($input['customer_id']) && empty($input['opportunity_id'])) {
            throw new \Exception('La actividad debe estar asociada a un prospecto, cliente u oportunidad.');
        }

        $activity = new Activity();
        $activity->company_id = $company_id;
        $activity->user_id = $user_id;
        $activity->lead_id = !empty($input['lead_id']) ? (int)$input['lead_id'] : null;
        $activity->customer_id = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $activity->opportunity_id = !empty($input['opportunity_id']) ? (int)$input['opportunity_id'] : null;
        $activity->activity_type = $activityType;
        $activity->title = trim($input['title']);
        $activity->description = !empty($input['description']) ? trim($input['description']) : null;
        $activity->activity_at = !empty($input['activity_at']) ? $input['activity_at'] : FG::getDateHour();
        $activity->status = 1;
        $activity->save();
        return $activity->fresh();
    }

    /**
     * Actividades creadas automáticamente
     * por otros Services.
     */
    public function createSystemActivity(int $company_id, ?int $user_id, string $activity_type, string $title, 
            ?string $description = null, ?int $lead_id = null, ?int $customer_id = null, ?int $opportunity_id = null): Activity {

        $activity_type = strtoupper(trim($activity_type));
        if (!in_array($activity_type, ActivityConstant::all(), true)) {
            throw new \Exception('El tipo de actividad no es válido.');
        }

        $activity = new Activity();
        $activity->company_id = $company_id;
        $activity->user_id = $user_id;
        $activity->lead_id = $lead_id;
        $activity->customer_id = $customer_id;
        $activity->opportunity_id = $opportunity_id;
        $activity->activity_type = $activity_type;
        $activity->title = $title;
        $activity->description = $description;
        $activity->activity_at = FG::getDateHour();
        $activity->status = 1;
        $activity->save();
        return $activity;
    }

    public function update(Activity $activity, array $input): Activity {
        if (!in_array($activity->activity_type, ActivityConstant::manualTypes(), true)) {
            throw new \Exception('Las actividades automáticas del sistema no pueden modificarse.');
        }

        if (array_key_exists('activity_type', $input)) {
            $type = strtoupper(trim($input['activity_type']));
            if (!in_array($type, ActivityConstant::manualTypes(), true)) {
                throw new \Exception('El tipo de actividad no es válido.');
            }
            $activity->activity_type = $type;
        }

        if (array_key_exists('title', $input)) {
            $title = trim($input['title']);
            if (empty($title)) {
                throw new \Exception('El título de la actividad es obligatorio.');
            }
            $activity->title = $title;
        }
        if (array_key_exists('description', $input)) {
            $activity->description = !empty($input['description']) ? trim($input['description']) : null;
        }
        if (array_key_exists('activity_at', $input)) {
            $activity->activity_at = !empty($input['activity_at']) ? $input['activity_at'] : $activity->activity_at;
        }
        if (array_key_exists('status', $input)) {
            $activity->status = (int)$input['status'];
        }
        $activity->save();
        return $activity->fresh();
    }

    private function validateRelations(array $input, int $company_id): void {
        if (!empty($input['lead_id'])) {
            $lead = $this->leadRepository->getById((int)$input['lead_id'], $company_id);
            if (!$lead) {
                throw new \Exception('El prospecto seleccionado no existe.');
            }
        }

        if (!empty($input['opportunity_id'])) {
            $opportunity = $this->opportunityRepository->getById( (int)$input['opportunity_id'],$company_id);
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
    }
}