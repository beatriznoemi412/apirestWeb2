<?php
require_once './app/models/seller.model.php';
require_once './app/views/json.view.php';

class SellerApiController {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new SellerModel();
        $this->view = new JSONView();
    }

    public function getAll($req, $res) {
        $sellers = $this->model->getSellers(); 
        return $this->view->response($sellers, 200);
    }

    public function get($req, $res) {
        // obtengo el id del vendedor desde la ruta
        $id = $req->params->id_vendedor;

        // obtengo el vendedor de la DB
        $seller = $this->model->getSeller($id);

        // verifico si se encontró el vendedor
        if (!$seller) {
            return $this->view->response(['error' => 'Vendedor no encontrado'], 404);
        }

        // mando el vendedor a la vista
        return $this->view->response($seller);
    }
}
