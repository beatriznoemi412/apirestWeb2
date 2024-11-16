<?php
require_once './app/models/seller.model.php';
require_once './app/views/json.view.php';
require_once './app/models/sale.model.php';

class SaleApiController
{
    private $model;
    private $view;
    private $sellerModel;
    private $page;
    private $limit;

    public function __construct($page = 1, $limit = 3)
    {
        $this->model = new SaleModel();
        $this->sellerModel = new SellerModel();
        $this->view = new JSONView();

        // Inicializamos las propiedades de clase para paginación
        $this->page = $page;
        $this->limit = $limit;
    }
    //api/venta
    public function getAllSales($req, $res)
    {
        // Parámetros permitidos que acepta la API. Se colocan manualmente como una forma de proteger la API
        $allowedParams = ['sortField', 'sortOrder', 'page', 'limit', 'min_price', 'max_price', 'id_vendedor', 'start_date', 'end_date', 'resource', 'id_venta'];

        // Obtener parámetros de la solicitud
        $queryParams = array_keys($_GET);

        // Verificar si hay parámetros no permitidos
        foreach ($queryParams as $param) {
            if (!in_array($param, $allowedParams)) {
                return $this->view->response('Parámetro no permitido: ' . $param, 400);
            }
        }
        // Ordenamiento
        $sortFields = ['precio', 'fecha_venta', 'id_vendedor'];
    
        $userSortField = isset($_GET['sortField']) ? $_GET['sortField'] : null;

        // Validar que sortField sea válido
        if ($userSortField && !in_array($userSortField, $sortFields)) {
            return $this->view->response('Campo de ordenamiento no permitido: ' . $userSortField, 400);
        }

        // Usar el campo de ordenamiento por defecto si no se proporciona
        $userSortField = $userSortField ?: 'precio';
        // Obtiene el parámetro de orden del usuario (asc o desc) usando $_GET y usa 'asc' por defecto
        $userSortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'desc' ? 'desc' : 'asc';

        // Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : $this->page;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $this->limit;

        // Validación de la página
        if ($page < 1) {
            $page = 1; // Fuerza a la primera página si se proporciona un valor menor
        }

        // Validación del límite
        if ($limit < 1) {
            $limit = 10; // Establece un límite predeterminado
        }

        // Cálculo del offset
        $offset = ($page - 1) * $limit;

        // Filtros
        $filters = [];
        $params = [];

        // Filtra por precio
        if (isset($_GET['min_price'])) {
            $minPrice = filter_var($_GET['min_price'], FILTER_VALIDATE_FLOAT); //filter_var verifica y sanitiza
            if ($minPrice !== false) {
                $filters[] = "precio >= :min_price"; // :min_price marcador de posicion que será usado después
                $params[':min_price'] = $minPrice; // Agrega parámetro
            }
        }

        if (isset($_GET['max_price'])) {
            $maxPrice = filter_var($_GET['max_price'], FILTER_VALIDATE_FLOAT);
            if ($maxPrice !== false) {
                $filters[] = "precio <= :max_price"; // Usa la columna "precio"
                $params[':max_price'] = $maxPrice; // Agrega parámetro
            }
        }

        if (isset($_GET['id_vendedor'])) {
            $idVendedor = filter_var($_GET['id_vendedor'], FILTER_VALIDATE_INT);
            if ($idVendedor === false) {
                return $this->view->response('ID de vendedor inválido. Debe ser un número entero.', 400);
            }
            $filters[] = "id_vendedor = :id_vendedor";
            $params[':id_vendedor'] = $idVendedor;
        }


        // Valida start_date
        if (isset($_GET['start_date'])) {
            $startDate = $_GET['start_date'];
            if (!$this->isValidDate($startDate)) {
                return $this->view->response('Fecha de inicio no válida. Debe estar en formato YYYY-MM-DD y ser una fecha real.', 400);
            }
            $filters[] = "fecha_venta >= :start_date";
            $params[':start_date'] = $startDate;
        }

        // Valida end_date
        if (isset($_GET['end_date'])) {
            $endDate = $_GET['end_date'];
            if (!$this->isValidDate($endDate)) {
                return $this->view->response('Fecha de fin no válida. Debe estar en formato YYYY-MM-DD y ser una fecha real.', 400);
            }

            // Valida que end_date no sea anterior a start_date
            if (isset($startDate) && $startDate > $endDate) {
                return $this->view->response('La fecha de fin no puede ser anterior a la fecha de inicio.', 400);
            }

            $filters[] = "fecha_venta <= :end_date"; //:end_date es un marcador de posición que se utilizará en una consulta SQL. Los marcadores de posición se usan para sustituir valores en una consulta de manera segura y eficiente.
            $params[':end_date'] = $endDate;
        }
        // Obtiene ventas con filtros y paginación
        try {
            $sales = $this->model->getSales($userSortField, $userSortOrder, $filters, $limit, $offset, $params);
            $totalSales = $this->model->countSales($filters, $params);
            if ($totalSales < 0) {
                $totalSales = 0; // En caso de que haya un error
            }

            // Para cada venta, obtiene el vendedor
            foreach ($sales as &$sale) {
                $seller = $this->sellerModel->getSeller($sale->id_vendedor);
                $sale->id_vendedor = $seller ?  $seller->id_vendedor  : 'Desconocido';
            }

            $response = [
                'ventas' => $sales,
                'pagina' => $page,
                'limite' => $limit,
                'total_ventas' => $totalSales,
                'total_paginas' => ceil($totalSales / $limit),
            ];
            $res->setStatusCode(200);
            $res->setBody($response);
            return $res->send();
        } catch (Exception $e) {
            $res->setStatusCode(500);
            $res->setBody(['error' => $e->getMessage()]);
            return $res->send();
        }
    }



    private function isValidDate($date)
    {
        // Valida con una expresión regular que el formato sea estrictamente YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }

    //api/venta/:id (GET)
    public function get($req, $res)
    {
        $id = $req->params->id_venta;

        // Validar que id_venta no esté vacío y sea un número entero positivo
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return $this->view->response('El ID de venta es inválido. Debe ser un número entero positivo.', 400);
        }
        $sale = $this->model->getSale($id);

        if (!$sale) {
            return $this->view->response('Venta no encontrada', 404);
        }

        return $this->view->response($sale, 200);
    }
    //api/venta (POST)
    public function addSale($req, $res)
    {   //valido datos
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($req->body->inmueble) || empty($req->body->fecha_venta) || empty($req->body->precio) || empty($req->body->id_vendedor) || empty($req->body->foto_url)) {
                return $this->view->response('Todos los campos son obligatorios.', 400);
            }
            //obtengo datos

            $inmueble = $req->body->inmueble;
            $date = $req->body->fecha_venta;
            $price = $req->body->precio;
            $id_vendedor = $req->body->id_vendedor;
            $image = $req->body->foto_url;

            // Validar que los campos obligatorios no estén vacíos
            if (empty($inmueble)) {
                return $this->view->response('El campo "inmueble" es obligatorio.', 400);
            }

            if (empty($date)) {
                return $this->view->response('El campo "fecha_venta" es obligatorio.', 400);
            }

            if (empty($price)) {
                return $this->view->response('El campo "precio" es obligatorio.', 400);
            }

            if (empty($id_vendedor)) {
                return $this->view->response('El campo "id_vendedor" es obligatorio.', 400);
            }

            if (empty($image)) {
                return $this->view->response('El campo "foto_url" es obligatorio.', 400);
            }

            // Validar que el precio sea un número positivo
            if (!is_numeric($price) || $price <= 0) {
                return $this->view->response('El precio debe ser un número positivo.', 400);
            }

            // Validar que el ID del vendedor sea un número positivo
            if (!is_numeric($id_vendedor) || $id_vendedor <= 0) {
                return $this->view->response('El ID del vendedor no es válido.', 400);
            }

            if (!filter_var($image, FILTER_VALIDATE_URL)) { //filter_var($url, FILTER_VALIDATE_URL) valida si la variable $url contiene una URL bien estructurada.filter_var es una función predefinida en PHP. Se utiliza principalmente para validar y sanitizar datos
                return $this->view->response('La URL de la imagen no es válida.', 400);
            }
            // Verifica si el vendedor existe
            if (!$this->sellerModel->getSeller($id_vendedor)) {
                return $this->view->response('No hay vendedores disponibles con ese ID.', 404);
            }
            // Verifica si ya existe una venta con los mismos parámetros
            if ($this->model->saleExists($inmueble, $date, $price, $id_vendedor, $image)) {
                return $this->view->response('La venta ya existe.', 409); // Conflicto al crear recurso
            }
            //inserto datos
            $id = $this->model->insertSale($inmueble, $date, $price, $id_vendedor, $image);

            if (!$id) {
                return $this->view->response("Error al insertar tarea", 500);
            }
        } else {
            $sellers = $this->sellerModel->getSellers();
            if (empty($sellers)) {
                return $this->view->response('No hay vendedores disponibles.', 404);
            }
        }
        //devuelve recurso insertado-buena practica-.
        $sale = $this->model->getSale($id);
        return $this->view->response($sale, 201);
    }

    //api/venta/:id (PUT)
    public function editSale($req, $res)
    {
        $id = $req->params->id_venta;
        // verifico que exista
        $sale = $this->model->getSale($id);

        if (!$sale) {
            return $this->view->response("La venta con el id=$id no existe", 404);
        }

        // Validación de campos vacíos
        if (empty($req->body->inmueble) || empty($req->body->fecha_venta) || empty($req->body->precio) || empty($req->body->id_vendedor) || empty($req->body->foto_url)) {
            return $this->view->response('Todos los campos son obligatorios.', 400);
        }

        $inmueble = $req->body->inmueble;
        $date = $req->body->fecha_venta;
        $price = $req->body->precio;
        $id_vendedor = $req->body->id_vendedor;
        $image = $req->body->foto_url;

        // Actualiza la venta
        $updated = $this->model->updateSale($id, $inmueble, $date, $price, $id_vendedor, $image);

        // Verificar si la actualización fue exitosa
        if ($updated === false) {
            return $this->view->response("Hubo un problema al actualizar la venta. Intente nuevamente.", 500);
        }
        // obtengo la venta modificada y la devuelvo en la respuesta
        $sale = $this->model->getSale($id);
        $this->view->response($sale, 200);
    }
    public function deleteSale($req, $res)
    {
        $id = $req->params->id_venta;
        $sale = $this->model->getSale($id);
        if (!$sale) {
            return $this->view->response("El producto con el id=$id no existe", 404);
        }
        $saleDeleted = $this->model->removeSale($id);
        $this->view->response($saleDeleted, 200);
    }
}
