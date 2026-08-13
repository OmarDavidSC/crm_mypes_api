<?php

namespace App\Dows;

use App\Middlewares\Application;
use Illuminate\Database\Capsule\Manager as DB;
use App\Utilities\FG;
use App\Models\Area;
use App\Models\Company;
use App\Models\User;

class CompanyDow
{

    public function index($request)
    {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();


            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'exito';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function update($request)
    {
        $response = FG::responseDefault();
        try {
            $id = $request->getAttribute('id');
            $input = $request->getParsedBody();
            $files = $request->getUploadedFiles();

            $company = Company::find($id);
            if (!$company) {
                $response['success'] = false;
                $response['message'] = "Los datos de la compañia no fueron encontrados.";
                return $response;
            }

            $company->name = $input['name'] ?? $company->name;

            if (isset($input['terms_conditions'])) {
                $company->terms_conditions = $input['terms_conditions'];
            }

            if (isset($input['privacy_policies'])) {
                $company->privacy_policies = $input['privacy_policies'];
            }

            if (isset($files['favicon'])) {
                $uploadResponse = StorageDow::upload('profile', $files, 'localhost', 'favicon', $id);
                if ($uploadResponse['success']) {
                    $company->favicon_id = $uploadResponse['data']->id;
                } else {
                    throw new \Exception("Error al subir la Imagen: " . $uploadResponse['message']);
                }
            }

            if (isset($files['logo'])) {
                $uploadResponse = StorageDow::upload('profile', $files, 'localhost', 'logo', $id);
                if ($uploadResponse['success']) {
                    $company->logo_id = $uploadResponse['data']->id;
                } else {
                    throw new \Exception("Error al subir la Imagen: " . $uploadResponse['message']);
                }
            }

            $company->save();

            $response['success'] = true;
            $response['data'] = $company;
            $response['message'] = "Datos de la compañia actualizados correctamente.";
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
