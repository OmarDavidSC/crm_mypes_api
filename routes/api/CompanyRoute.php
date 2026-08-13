<?php

$map->attach('companies.', '/company', function ($map) {

    $map->post('update', '/{id}/update', [
        'Controller' => 'App\Controllers\CompanyController',
        'Action' => 'update'
    ]);
});
