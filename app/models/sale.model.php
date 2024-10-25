<?php

class SaleModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=desarrolloinmobiliario;charset=utf8', 'root', '');
    }


    public function getSales($sortField, $sortOrder) {
        // Validar que los parámetros sean correctos
        $validSortFields = ['inmueble', 'fecha_venta', 'precio', 'id_vendedor'];
        $validSortOrders = ['asc', 'desc'];
    
        if (!in_array($sortField, $validSortFields) || !in_array($sortOrder, $validSortOrders)) {
            throw new Exception('Parámetros de ordenamiento no válidos.');
        }
    
        // Preparar la consulta SQL con ordenamiento y paginación
        $query = $this->db->prepare("SELECT * FROM venta ORDER BY $sortField $sortOrder");
    
        // Ejecutar la consulta
        $query->execute();
    
        // Obtener los datos en un arreglo de objetos
        $sales = $query->fetchAll(PDO::FETCH_OBJ); 
    
        // Comprobar si se encontraron resultados
        if (empty($sales)) {
            return []; // Retornar arreglo vacío si no hay ventas
        }
    
        return $sales; // Retornar las ventas ordenadas y paginadas
    }
    

    public function getSale($id) {    
        
            // Prepara y ejecuta la consulta
            $query = $this->db->prepare('SELECT * FROM venta WHERE id_venta = ?');
            $query->execute([$id]);   
    
            // Obtiene el resultado
            $sale = $query->fetch(PDO::FETCH_OBJ);
        
            return $sale;

    }
    // Elimina una venta por ID (DELETE)
    public function removeSale($id) {
        $query = $this->db->prepare('DELETE FROM sales WHERE id = ?');
        return $query->execute([$id]);
    }
     // Inserta una nueva venta (POST)
     public function insertSale($inmueble, $date, $price, $id_vendedor, $image) {
        $query = $this->db->prepare('INSERT INTO sales (inmueble, date, price, id_vendedor, image) VALUES (?, ?, ?, ?, ?)');
        $success = $query->execute([$inmueble, $date, $price, $id_vendedor, $image]);

        // Devuelve el ID de la venta insertada o false en caso de error
        return $success ? $this->db->lastInsertId() : false;
    }

    // Actualizar una venta existente (PUT)
    public function updateSale($id, $inmueble, $date, $price, $id_vendedor, $image) {
        $query = $this->db->prepare('UPDATE sales SET inmueble = ?, date = ?, price = ?, id_vendedor = ?, image = ? WHERE id = ?');
        return $query->execute([$inmueble, $date, $price, $id_vendedor, $image, $id]);
    }
}
    

   /* 

    public function updateSale($id, $inmueble, $date, $price, $id_vendedor, $image) {
        try {
            
            $query = $this->db->prepare('UPDATE venta SET inmueble = ?, fecha_venta = ?, precio = ?, id_vendedor = ?, foto_url = ? WHERE id_venta = ?');
            
            // Ejecuta la consulta con los parámetros
            return $query->execute([$inmueble, $date, $price, $id_vendedor, $image, $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage()); // Log del error
            return false; // Retorna false en caso de error
        }
    }
}*/