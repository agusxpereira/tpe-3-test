<?php
class ModeloBase{
    protected $db;

    public function __construct() {
        require_once 'config.php';

            // Conectar al servidor de base de datos
            $this->db = new PDO("mysql:host=" . MYSQL_HOST . ";charset=utf8", MYSQL_USER, MYSQL_PASS);

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Crear la base de datos si no existe
            $this->db->exec("CREATE DATABASE IF NOT EXISTS " . MYSQL_DB);

            // Seleccionar la nueva base de datos
            $this->db->exec("USE " . MYSQL_DB);

            $this->_deploy();
            
  
    }

    private function _deploy()
    {

        $query = $this->db->query('SHOW TABLES');

        $tables = $query->fetchAll();

        if (count($tables) == 0) {

            $filePath = 'database/tpe_web2.sql';

            $sql = file_get_contents($filePath);

            $this->db->exec($sql);
        }
    }
}