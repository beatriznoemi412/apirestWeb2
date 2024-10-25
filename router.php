<?php
// Activar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './libs/router.php';
require_once './app/controllers/seller.api.controller.php';
require_once './app/controllers/sale.api.controller.php'; 

$router = new Router();

// Definir las rutas
$router->addRoute('vendedores', 'GET', 'SellerApiController', 'getAll');
$router->addRoute('vendedores/:id_vendedor', 'GET', 'SellerApiController', 'get');

$router->addRoute('venta',                   'GET', 'SaleApiController',    'getAllSales');
$router->addRoute('venta/:id_venta',         'GET', 'SaleApiController',    'get');
$router->addRoute('venta/:id'  ,            'DELETE','SaleApiController',   'delete');
$router->addRoute('venta'  ,                'POST',  'SaleApiController',   'addSale');
$router->addRoute('venta/:id'  ,            'PUT',   'SaleApiController',   'editSale');


// Obtener la URL solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);

// Verificar que el 'path' esté definido antes de usar 'trim'
if (isset($parsedUrl['path'])) {
    $resource = trim($parsedUrl['path'], '/'); // Limpia la URL y elimina barras
} else {
    $resource = ''; // Valor predeterminado si 'path' no está presente
}

// Ajusta el resource para que solo contenga 'vendedores' y 'vendedores/:id_vendedor'
$basePath = 'web2-ApiRest/api'; 
$resource = str_replace("$basePath/", '', $resource);

// Pasar el recurso y el método de solicitud al enrutador
$router->route($resource, $_SERVER['REQUEST_METHOD']);



