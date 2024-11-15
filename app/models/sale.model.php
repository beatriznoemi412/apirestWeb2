<?php

class SaleModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;dbname=desarrolloinmobiliario;charset=utf8', 'root', '');
    }
    public function getSales($userSortField, $userSortOrder, $filters, $limit, $offset, $params)
    {
        // Construye la consulta SQL
        $sql = "SELECT * FROM venta";

        // Agrega filtros si existen
        if (!empty($filters)) {
            $sql .= " WHERE " . implode(" AND ", $filters); //.= agrega esta nueva parte al final de la cadena existente en $sql. implode convierte array en cadena texto
        }

        // Agrega ordenamiento y limit
        $sql .= " ORDER BY $userSortField $userSortOrder LIMIT :limit OFFSET :offset";

        $query = $this->db->prepare($sql);

        // Vincula parámetros
        foreach ($params as $key => $value) {
            $query->bindValue($key, $value); //En PHP, bindValue es un método que se utiliza en consultas preparadas para asociar un valor específico a un parámetro de la consulta SQL antes de ejecutarla
        }

        // Vincula límite y offset
        $query->bindValue(':limit', $limit, PDO::PARAM_INT); //:limit y :offset son marcadores de posición para parámetros en una consulta SQL. El símbolo : indica que limit y offset son parámetros que se enlazarán (o vincularán) a valores específicos más adelante en el código.
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);

        $query->execute();

        // Retorna resultados como objetos
        return $query->fetchAll(PDO::FETCH_OBJ);
    }


    public function getSale($id)
    {

        // Prepara y ejecuta la consulta
        $query = $this->db->prepare('SELECT * FROM venta WHERE id_venta = ?');
        $query->execute([$id]);

        // Obtiene el resultado
        $sale = $query->fetch(PDO::FETCH_OBJ);

        return $sale;
    }

    // Método para verificar si una venta ya existe
    public function saleExists($inmueble, $date, $price, $id_vendedor, $image) {
        $sql = "SELECT COUNT(*) FROM venta WHERE inmueble = ? AND fecha_venta = ? AND precio = ? AND id_vendedor = ? AND foto_url = ?";
        $query = $this->db->prepare($sql);
        $query->execute([$inmueble, $date, $price, $id_vendedor, $image]);
        return $query->fetchColumn() > 0; // Retorna true si existe
    }
    // Inserta una nueva venta (POST)
    public function insertSale($inmueble, $date, $price, $id_vendedor, $image)
    {
        $query = $this->db->prepare('INSERT INTO venta (inmueble, fecha_venta, precio, id_vendedor, foto_url) VALUES (?, ?, ?, ?, ?)');
        $query->execute([$inmueble, $date, $price, $id_vendedor, $image]);

        $id = $this->db->lastInsertId();

        return $id;
    }
    public function countSales($filters)
    {

        $sql = "SELECT COUNT(*) FROM venta WHERE 1=1";

        // Añade filtros
        foreach ($filters as $key => $value) {
            if (in_array($key, ['min_price', 'max_price', 'id_vendedor', 'start_date', 'end_date'])) {
                $sql .= " AND $key = :$key";
            }
        }

        $query = $this->db->prepare($sql);

        // Vincula los valores de los filtros
        foreach ($filters as $key => $value) {//recorre nuevamente el array $filters y vincula cada filtro con su marcador de posición en la consulta SQL. Usa el método bindValue() de PDO, que asocia el valor de cada filtro a su respectivo marcador de posición (:$key).
            if (in_array($key, ['min_price', 'max_price', 'id_vendedor', 'start_date', 'end_date'])) {
                $query->bindValue(":$key", $value);
            }
        }

        $query->execute();
        return $query->fetchColumn();
    }
    public function updateSale($id, $inmueble, $date, $price, $id_vendedor, $image)
    {
        $query = $this->db->prepare('UPDATE venta SET inmueble = ?, fecha_venta = ?, precio = ?, id_vendedor = ?, foto_url = ? WHERE id_venta = ?');
        $query->execute([$inmueble, $date, $price, $id_vendedor, $image, $id]);
    }
    public function removeSale($id){
        $query= $this->db->prepare('DELETE FROM venta WHERE id_venta = ?');
        $query->execute([$id]);
    }
}
