<?php

   class Response {
       public $user = null;
       private $statusCode = 200;
       private $body = [];
       
       // Método para establecer el código de estado
       public function setStatusCode($code) {
           $this->statusCode = $code;
       }
       
       // Método para agregar datos al cuerpo de la respuesta
       public function setBody($data) {
           $this->body = $data;
       }
   
       // Método para enviar la respuesta
       public function send() {
           // Establecer el código de estado HTTP
           http_response_code($this->statusCode);
           
           // Establecer el encabezado de tipo de contenido
           header('Content-Type: application/json');
           
           // Enviar la respuesta como JSON
           echo json_encode($this->body);
           exit; // Finalizar la ejecución para evitar más salida
       }
   }