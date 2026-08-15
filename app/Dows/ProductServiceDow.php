<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\ProductServiceRepository;
use App\Services\ProductServiceService;
use App\Utilities\FG;
use App\Validators\ProductServiceValidator;
use Illuminate\Database\Capsule\Manager as DB;

class ProductServiceDow {
    private ProductServiceRepository $productServiceRepository;
    private ProductServiceService $productServiceService;

    public function __construct() {
        $this->productServiceRepository = new ProductServiceRepository();
        $this->productServiceService = new ProductServiceService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $search = trim($input['search'] ?? '');
            $type = !empty($input['type']) ? strtoupper(trim($input['type'])) : null;

            if (!empty($search) || !empty($type)) {
                $items = $this->productServiceRepository->search($company_id, $search, $type);

            } else {
                $items = $this->productServiceRepository->getAllByCompany($company_id);
            }

            $data = $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'code' => $item->code,
                        'name' => $item->name,
                        'description' => $item->description,
                        'price' => (float)$item->price,
                        'tax_percentage' => (float)$item->tax_percentage,
                        'status' => $item->status,
                        'created_at' =>  FG::formatDateTimeHuman($item->created_at),
                    ];
                });

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Productos y servicios obtenidos correctamente.';
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

            $item = $this->productServiceRepository->getById($id, $company_id);
            if(!$item) {
                $response['success'] = false;
                $response['message'] = 'El producto o servicio no fue encontrado.';
                return $response;
            }
            
            $response['success'] = true;
            $response['data'] = [
                'id' => $item->id,
                'type' => $item->type,
                'code' => $item->code,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'tax_percentage' => (float) $item->tax_percentage,
                'status' => $item->status
            ];
            $response['message'] = 'Producto o Servicio obtenido correctamente.';
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
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $errors = ProductServiceValidator::store($input);
            if(!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $item = $this->productServiceService->create($input, $company_id);
            DB::commit();
            
            $response['success'] = true;
            $response['data'] = $item;
            $response['message'] = 'Producto o Servicio registrado correctamente.';
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

            $errors = ProductServiceValidator::store($input);
            if(!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $item = $this->productServiceRepository->getById($id, $company_id);
            if(!$item) {
                $response['success'] = false;
                $response['message'] = 'El producto o servicio no fue encontrado.';
                return $response;
            }

            $item = $this->productServiceService->update($item, $input);
            DB::commit();
            
            $response['success'] = true;
            $response['data'] = $item;
            $response['message'] = 'Producto o Servicio actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

     public function options($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $items = $this->productServiceRepository->getActiveForQuotation($company_id);

            $data = $items->map(function ($item) {
                    return [
                        'id' => $item->id, 
                        'type' => $item->type,
                        'code' => $item->name,
                        'description' => $item->description,
                        'price' => (float) $item->price,
                        'tax_percentage' => (float) $item->tax_percentage
                    ];
            });

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Productos y servicios disponibles obtenidos correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function remove($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $item = $this->productServiceRepository->getById($id, $company_id);
            if(!$item) {
                $response['success'] = false;
                $response['message'] = 'El producto o servicio no fue encontrado';
                return $response;
            }

            if($item->quotationItems()->whereNull('deleted_at')->exists()) {
                $response['success'] = false;
                $response['message'] = 'El producto o servicio ya fue utilizado en una cotización. Puede desactivarlo, pero no eliminarlo';
                return $response;
            }

            $item->delete();
            DB::commit();
            
            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Producto o servicio eliminado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
