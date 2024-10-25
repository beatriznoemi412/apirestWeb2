<?php

class UserModel {
    private $db;

    public function __construct() {
        // Usar las constantes definidas en config.php para la conexión
        $this->db = new PDO('mysql:host=localhost;dbname=db_tareas;charset=utf8', 'root', '');
    }

    public function getUserByUsername($usuario) {    
        $query = $this->db->prepare("SELECT * FROM vendedores WHERE usuario = ?");
        $query->execute([$usuario]);;//no ejecuta codigo malicioso
    
        $user = $query->fetch(PDO::FETCH_OBJ);
    
        return $user;
    }
}
