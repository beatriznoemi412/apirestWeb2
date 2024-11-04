<?php
// Activa la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './libs/router.php';
require_once './app/controllers/sale.api.controller.php'; 

$router = new Router();

// Definir las rutas

$router->addRoute('venta',                    'GET', 'SaleApiController',    'getAllSales');
$router->addRoute('venta/:id_venta',          'GET', 'SaleApiController',    'get');
$router->addRoute('venta',                    'POST','SaleApiController',   'addSale');
$router->addRoute('venta/:id_venta',          'PUT', 'SaleApiController',   'editSale');

$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);



