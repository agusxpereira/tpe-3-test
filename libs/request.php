<?php
//es parecido al response, pero agrega cosas de la peticion

    class Request {
        public $body = null; # { nombre: 'Saludar', descripcion: 'Saludar a todos' }
        //es el body de la peticion, viene con datos
        public $params = null; # /api/tareas/:id
        public $query = null; # ?soloFinalizadas=true

        public function __construct() {
            try {
                # file_get_contents('php://input') lee el body de la request
                $this->body = json_decode(file_get_contents('php://input'));
            }
            catch (Exception $e) {
                $this->body = null;
            }
            $this->query = (object) $_GET;//básicamente los parametros GET: ?soloFinalizadas = true
        }
    }
// El request es un objeto, que luego tenemos que crear