
<?php
require_once './app/models/seller.model.php';
require_once './app/views/json.view.php';
require_once './app/models/sale.model.php';

class SaleApiController {
    private $model;
    private $view;
    private $sellerModel;

    public function __construct() {
        $this->model = new SaleModel();
        $this->sellerModel = new SellerModel();
        $this->view = new JSONView();
    }
    

    public function getAllSales($req, $res) {
        // Establece el campo de ordenamiento y el orden por defecto
        $sortField = 'precio'; // Siempre ordenar por precio
        $sortOrder = 'asc'; // Orden ascendente
    
       // Log de los parámetros de ordenamiento
        error_log("Parámetros de ordenamiento: ");
        error_log("sortField: $sortField");
        error_log("sortOrder: $sortOrder");
        // Obtiene la lista de ventas ordenada por precio ascendente
        $sales = $this->model->getSales($sortField, $sortOrder, -1, 0); // Usar -1 para obtener todas las ventas
    
        // Prepara la respuesta
        $response = [
            'sales' => $sales,
        ];
    
        // Establece la respuesta y la envía
        $res->setBody($response);
        return $res->send(); // Envía la respuesta
    }    
    
    // Obtiene una venta por ID (GET)
    public function get($req, $res) {
     // obtengo el id del vendedor desde la ruta
     $id = $req->params->id_venta;

     // obtengo el vendedor de la DB
     $sale = $this->model->getSale($id);

     // verifico si se encontró el vendedor
     if (!$sale) {
         return $this->view->response(['error' => 'Venta no encontrada'], 404);
     }

     return $this->view->response($sale);
    }
    //apiRest/venta (DELETE)
    public function delete($req, $res) {
        // Obtiene el ID de la venta desde la ruta
        $id = $req->params->id_venta;
    
        // Verifica si la venta existe
        $sale = $this->model->getSale($id);
        if (!$sale) {
            return $this->view->response(['error' => 'Venta no encontrada.'], 404);
        }
    
        // Elimina la venta de la base de datos
        if ($this->model->removeSale($id)) {
            return $this->view->response(['message' => 'Venta eliminada con éxito.'], 200);
        } else {
            return $this->view->response(['error' => 'Hubo un problema al eliminar la venta.'], 500);
        }
    }
    // apiRest/venta (POST)
    public function addSale($req, $res) {

    
        // Verifica si el formulario fue enviado correctamente
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida que los campos requeridos estén completos
            if (empty($req->body->inmueble) || empty($req->body->date) || empty($req->body->price) || empty($req->body->id_vendedor) || empty($req->body->image)) {
                return $this->view->response('Todos los campos son obligatorios.', 400);
            }

            // Obtiene los datos del cuerpo de la solicitud
            $inmueble = $req->body->inmueble;  
            $date = $req->body->date;
            $price = $req->body->price;
            $id_vendedor = $req->body->id_vendedor;
            $image = $req->body->image;

            // Valida que la URL de la imagen sea válida
            if (!filter_var($image, FILTER_VALIDATE_URL)) {
                return $this->view->response('La URL de la imagen no es válida.', 400);
            }

            // Inserta la venta en la base de datos
            $id = $this->model->insertSale($inmueble, $date, $price, $id_vendedor, $image);

            // Verifica si la inserción fue exitosa
            if (!$id) {
                return $this->view->response("Error al insertar tarea", 500);
            }
     
        } else {
            // Llama al método para obtener la lista de vendedores
            $sellers = $this->sellerModel->getSellers(); // Obtener la lista de vendedores

            // Compruebo si se obtuvieron vendedores
            if (empty($sellers)) {
                return $this->view->response('No hay vendedores disponibles.', 404);
            }

        }
        // buena práctica es devolver el recurso insertado
        $sale = $this->model->getSale($id);
        return $this->view->response($sale, 201);
    }
}
    
