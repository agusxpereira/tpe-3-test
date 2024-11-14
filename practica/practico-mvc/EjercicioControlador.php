<?php
require_once 'EjercicioModelo.php';
require_once 'EjercicioVista.php';
class EjercicioControlador{

    private $modelo;
    private $vista;

    public function __construct(){
        $this->modelo = new EjercicioModelo();
        $this->vista = new EjercicioVista();
    }
    
    public function mostrarProductos(){
        $productos = $this->modelo->obtenerProducto();
        var_dump($productos);
        return $this->vista->mostrarProductos($productos);

    }

    //esto claramente esta mal no es asi por que nada tiene que ver productos con comentarios
    public function mostrarComentarios(){
        //es listar todos los comentarios creo que no tendria que chequear nada, salvo que pidam por ejemplo todos los comentarios de...
        $comentarios = $this->modeloComentarios->getComentarios();
        if(!$comentarios)
            return $this->vista->mostrarMensaje("No habia comentarios para mostrar")

    }

}
?>