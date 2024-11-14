<?php
class EjercicioModelo{

    

    public function obtenerProducto(){
        $producto1 = new stdClass();
        $producto2 = new stdClass();
        $producto3 = new stdClass();
        $producto4 = new stdClass();

        $producto1->nombre = "producto 1";
        $producto1->precio = 250;
        $producto2->nombre = "producto 2";
        $producto2->precio = 350;
        $producto3->nombre = "producto 3";
        $producto3->precio = 20;
        $producto4->nombre = "producto 4";
        $producto4->precio = 3450;

        
        return array($producto1, $producto2, $producto3, $producto4);
    }
}