<?php
require_once 'libs/router.php';
require_once 'app/controladores/libros.api.controlador.php';

//requerimos el controlador que vamos a estar usando, el que nos dieron implementado
$router = new Router();//lo instanciamos
#                 endpoint                  verbo      controller              metodo
$router->addRoute('libros'      ,           'GET',     'LibroApiController',   'obtenerLibros');
$router->addRoute('libros/:id'  ,           'GET',     'LibroApiController',   'obtenerLibro');
$router->addRoute('libros/:id'  ,           'DELETE',  'LibroApiController',   'borrarLibro');
$router->addRoute('libros',                 'POST',    'LibroApiController',   'agregarLibro');
$router->addRoute('libros/:id',             'PUT',     'LibroApiController',   'editarLibro');
$router->addRoute('libros/:id/en_oferta',   'PUT',     'LibroApiController',   'agregarOferta');
$router->addRoute('generos',                'GET',     'LibroApiController',   'obtenerGeneros');


//al final lo llamamos con el recurso o la ruta (seria el action)
$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);
