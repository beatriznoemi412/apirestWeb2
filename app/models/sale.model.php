<?php

class SaleModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=desarrolloinmobiliario;charset=utf8', 'root', '');
    }
    public function getSales($sortField, $sortOrder, $filters, $limit, $offset, $params) {
        // Construir la consulta SQL
        $sql = "SELECT * FROM venta";
    
        // Agregar filtros si existen
        if (!empty($filters)) {
            $sql .= " WHERE " . implode(" AND ", $filters);
        }
    
        // Agregar ordenamiento y limit
        $sql .= " ORDER BY $sortField $sortOrder LIMIT :limit OFFSET :offset";
    
        $stmt = $this->db->prepare($sql);
        
        // Vincular parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Vincular límite y offset
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
        $stmt->execute();
    
        // Retornar resultados como objetos
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    public function getSale($id) {  
        
            // Prepara y ejecuta la consulta
            $query = $this->db->prepare('SELECT * FROM venta WHERE id_venta = ?');
            $query->execute([$id]);   
    
            // Obtiene el resultado
            $sale = $query->fetch(PDO::FETCH_OBJ);
        
            return $sale;
    }
     // Inserta una nueva venta (POST)
     public function insertSale($inmueble, $date, $price, $id_vendedor, $image) {
        $query = $this->db->prepare('INSERT INTO venta (inmueble, fecha_venta, precio, id_vendedor, foto_url) VALUES (?, ?, ?, ?, ?)');
        $query -> execute([$inmueble, $date, $price, $id_vendedor, $image]);

        $id = $this->db->lastInsertId();
    
        return $id;

    }
    public function countSales($filters) {
       
            $sql = "SELECT COUNT(*) FROM venta WHERE 1=1"; 
        
            // Añade filtros
            foreach ($filters as $key => $value) {
                if (in_array($key, ['min_price', 'max_price', 'id_vendedor', 'start_date', 'end_date'])) {
                    $sql .= " AND $key = :$key"; 
                }
            }
        
            $query = $this->db->prepare($sql);
            
            // Vincula los valores de los filtros
            foreach ($filters as $key => $value) {
                if (in_array($key, ['min_price', 'max_price', 'id_vendedor', 'start_date', 'end_date'])) {
                    $query->bindValue(":$key", $value);
                }
            }
        
            $query->execute();
            
            return $query->fetchColumn();
        }
    public function updateSale($id, $inmueble, $date, $price, $id_vendedor, $image) {    
        $query = $this->db->prepare('UPDATE venta SET inmueble = ?, fecha_venta = ?, precio = ?, id_vendedor = ?, url_foto = ? WHERE id_venta = ?');
        $query->execute([$inmueble, $date, $price, $id_vendedor, $image, $id]);
    }
} 