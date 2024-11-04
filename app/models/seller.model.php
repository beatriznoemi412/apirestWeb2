<?php
class SellerModel {
    private $db;

    public function __construct() {
       $this->db = new PDO('mysql:host=localhost;dbname=desarrolloinmobiliario;charset=utf8', 'root', '');
      
    }
 
    public function getSellers() {
        //  Ejecuto la consulta
        $query = $this->db->prepare('SELECT * FROM vendedores');
        $query->execute();
    
        // Obtengo los datos en un arreglo de objetos
        $sellers = $query->fetchAll(PDO::FETCH_OBJ); 
        
         // Compruebo si se encontraron resultados
        if (empty($sellers)) {
            return []; // Retornar arreglo vacio si no hay vendedores
        }

        return $sellers;
    }
   
    public function getSeller($id) {    
            
            // Prepara y ejecuta la consulta
            $query = $this->db->prepare('SELECT * FROM vendedores WHERE id_vendedor = ?');
            $query->execute([$id]);   
    
            // Obtiene el resultado
            $seller = $query->fetch(PDO::FETCH_OBJ);
        
            return $seller;
    }
}