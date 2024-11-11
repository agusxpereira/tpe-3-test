<?php

require_once './app/modelos/libros.modelo.php';
require_once './app/vistas/jsonview.php';


class LibroApiController{

    private $modelo;
    private $vista;

    public function __construct(){
        $this->modelo = new LibrosModelo();
        $this->vista = new JSONView();
        //lo mismo para las vistas
        
   
    }

    public function obtenerLibros($req, $res){
      
        
        $filtrarOferta = false;
        $pagina = false;
        $limite = 10;
        $ordenarPor = false;
        $orden=false;

        if(isset($req->query->ofertas) && ($req->query->ofertas == "true"))
            //api/libros?ofertas=true
            $filtrarOferta = "true";
        
        if(isset($req->query->ordenarPor) && ($req->query->ordenarPor != "")){
            //api/libros?ordenarPor="precio"
            $ordenarPor = "precio";
            

            if(isset($req->query->orden) &&($req->query->orden) != "" ){
                $orden = $req->query->orden;
            }
        
        }
        if(isset($req->query->pagina) && ($req->query->pagina != "")){
            $pagina = $req->query->pagina;
            
        }
        if(isset($req->query->limite) && ($req->query->limite != "")){
            $limite = $req->query->limite;                
        }
        
        try {
            
            $libros = $this->modelo->obtenerLibros($filtrarOferta, $ordenarPor, $orden, $pagina, $limite);
            return $this->vista->response($libros);//tiene como defecto 200
        } catch (Exception $e) {
            // Manejo de error: lo mostramos en la respuesta
            return $this->vista->response(['error' => $e->getMessage()], 500);
        }
        
    }
    public function obtenerLibro($req, $res){
        // api/libros/:id
        $id = $req->params->id; // obtenemos el ID de la request: todavía no se en que momento se crea el request
        //en la clase dice que viene de la ruta
        //creo que se refiere a que lo inicializa el constructor complejo cuando accedemos a una ruta
        $libro = $this->modelo->obtenerLibroPorId($id);
        
        if(!$libro){
            return $this->vista->response("No exisitia un libro para el id=$id",404);
        }
        
        return $this->vista->response($libro);
    }
    public function borrarLibro($req, $res){
        $id = $req->params->id;

        $libro = $this->modelo->obtenerLibroPorId($id);

        if(!$libro){
            return $this->vista->response("La tarea con el id=$id no existe.", 404);
        }

        $this->modelo->eliminarLibro($id);
        return $this->vista->response("La tarea con el id=$id fue borrado con exito.");
    }

    public function agregarLibro($req, $res){
       
        //validamos los datos
        if(empty($req->body->titulo) || empty($req->body->id_genero) || empty($req->body->precio)){
            
            return $this->vista->response('Faltan parametros', 400);
        }

        
        //obtenemos los datos del body request
        $titulo = $req->body->titulo;
        $autor = $req->body->autor;
        $id_genero = $req->body->id_genero;
        $paginas = $req->body->paginas;
        $cover = $req->body->cover;
        $precio = $req->body->precio;
        
        if(!is_int($id_genero)){
            $id_genero = intval($this->modelo->obtenerIdGenero($id_genero));  
        }

        $id = $this->modelo->agregarLibro($titulo, $autor, $id_genero, $paginas, $cover, $precio);
        if(!$id)
            return $this->vista->response("Ocurrio un error inesperado", 500);

        $libro = $this->modelo->obtenerLibroPorId($id);
        return $this->vista->response($libro, 201);

    }
    

    public function editarLibro($req, $res){
        $id = $req->params->id;//no se  porque le puse id_libro, siempre lo recibe como id, asi le pusimos a la variable: /:id
        
        $libro = $this->modelo->obtenerLibroPorId($id);

        if(!$libro){
            return $this->vista->response("La tarea con el id=$id no existe.", 404);
        }

        if(!is_int($req->body->id_genero)){
            $req->body->id_genero = intval($this->modelo->obtenerIdGenero($req->body->id_genero));  
        }

        if(empty($req->body->titulo) || empty($req->body->id_genero) || empty($req->body->precio)){
            //aca tenia otro error, los atributos del body tienen exactamente el mismo nombre que en el json del postman, o como los recibe cuando hacemos un GET
            return $this->vista->response('Faltan parametros', 400);
        }
        
        

        $titulo = $req->body->titulo;
        $autor = $req->body->autor;
        $id_genero = $req->body->id_genero;
        $paginas = $req->body->paginas;
        $cover = $req->body->cover;
        $precio = $req->body->precio;

        $this->modelo->editarLibro($titulo, $autor, $id_genero, $paginas, $cover, $id, $precio);
        $libroModificado = $this->modelo->obtenerLibroPorId($id);
        if(!$libroModificado)
            return $this->vista->response("Error.", 500);

        return $this->vista->response($libroModificado, 201);

    }

    public function cambiarOferta($req, $res){
    
        $id = $req->params->id;//el id lo obtenemos siempre de los parametros, no lo envaimos por el body
        $libro = $this->modelo->obtenerLibroPorId($id);
        $valor = 1;
        if(!$libro)
            return $this->vista->response("No existe esa Tarea $id", 404);
        
        if($libro[0]->en_oferta == 1)
            $valor = 0;
        
        $libroModificado = $this->modelo->agregarEnOferta($id, $valor);        
        if(!$libroModificado)
            return $this->vista->response("Ocurrio un error inesperado", 500);
        
        return $this->vista->response($libroModificado, 201);
    }

    public function obtenerGeneros($req, $res){
        $listaGeneros = $this->modelo->obtenerGeneros();
        return $this->vista->response($listaGeneros);
    }
}