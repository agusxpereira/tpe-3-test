<?php

require_once 'EjercicioControlador.php';
$action = "home";
if(isset($_GET['action'])){
    $action = $_GET['action'];
}

$params = explode($action, '');
//var_dump($action);
switch ($action) {
    case 'home':
        //var_dump($params);
        break;
    case 'productos':
        var_dump("hola");
        $controlador = new EjercicioControlador();
        $controlador->mostrarProductos();   
        break;
    
    default:
        # code...
        break;
}

