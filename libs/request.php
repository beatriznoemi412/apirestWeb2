<?php
        class Request {
            public $params; // Parámetros de la URL
            public $body;   // Cuerpo de la solicitud
            public $query;  // Parámetros de consulta
            public $resource; // Recurso solicitado
        
            public function __construct() {
                $this->params = new stdClass(); // Inicializa params como un objeto vacío
                $this->body = $this->getBody(); // Captura el cuerpo de la solicitud
                $this->query = $_GET; // Captura los parámetros de consulta
                $this->resource = ''; // Inicializa el recurso como cadena vacía
            }
        
            // Captura el cuerpo de la solicitud json
            private function getBody() {
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
                if (strpos($contentType, 'application/json') !== false) {
                    return json_decode(file_get_contents('php://input'));
                }
        
                return $_POST; // Si no es JSON, intenta obtener los datos como POST
            }
        
            // Método para establecer el recurso
            public function setResource($resource) {
                $this->resource = $resource;
            }
        }
        
