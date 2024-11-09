<?php
require_once 'Modelo.base.php';
class LibrosModelo extends ModeloBase{

    /*Funcion de ayuda*/
    public function obtenerGeneros(){
       $query =  $this->db->prepare("SELECT nombre FROM generos WHERE 1");
       $query->execute();
       $listaGeneros = $query->fetchAll(PDO::FETCH_OBJ);
       
       return $listaGeneros;
    }
    public function obtenerIdGenero($nombre){
       $query =  $this->db->prepare("SELECT id FROM generos WHERE nombre = ?");
       $query->execute([$nombre]);
       $id = $query->fetchAll(PDO::FETCH_COLUMN);
       
       return $id[0];
    
    }

    public function obtenerLibros($filtrarOferta = false, $ordenarPor = false, $orden = false){
        $sql = "SELECT * FROM libros";
        //alteeramos el pedido según el parametro

        if($filtrarOferta){
            $sql = $sql." WHERE en_oferta = 1";
            
        }
        if($ordenarPor){
            switch ($ordenarPor) {
                case 'titulo':
                    $sql .= " ORDER BY titulo";
                    break;
                
                case 'precio':
                    $sql .= " ORDER BY precio";
                    break;
            }
        }

        if($orden && $ordenarPor != false){
            switch ($orden) {
                case 'ascendente':
                    $sql .= " ASC";
                    break;
                
                case 'descendente':
                    $sql .= " DESC";
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        $consulta = $this->db->prepare($sql);
        $consulta->execute();
        $libros = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        return $libros;
    }

    public function obtenerLibroPorId($id)
    {
        $consulta = $this->db->prepare("SELECT * FROM libros WHERE id_libro = ?");
        $consulta->execute([$id]);
        $libro = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        return $libro;
    }


    public function obtenerLibrosPorGenero($id_genero)
    {
        $consulta = $this->db->prepare('SELECT libros.id_libro ,libros.titulo from generos inner join libros on generos.id = libros.id_genero where  generos.id = ?');
        $consulta->execute([$id_genero]);
        $libros = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $libros;
    }

    //agregar
    public function agregarLibro($titulo, $autor, $genero_id, $paginas, $cover, $precio)
    {
        $id = 0;

        try {
            $consulta = $this->db->prepare('INSERT INTO libros(titulo, autor, paginas, cover, id_genero, precio) VALUES (?,?,?,?,?,?)');
            $consulta->execute([$titulo, $autor, $paginas, $cover, $genero_id, $precio]);
            $id = $this->db->lastInsertId();
        } catch (\Throwable $th) {
            $id = -1;
        }

        return $id;
    }



    //editar
    public function editarLibro($titulo, $autor, $id_genero, $paginas, $cover, $id_libro, $precio)
    {

        $consulta = $this->db->prepare("UPDATE libros SET titulo = ?, autor = ?, paginas = ?, cover = ?, id_genero = ?, precio = ? WHERE id_libro = ?");
        try {

            $consulta->execute([$titulo, $autor, intval($paginas), $cover, intval($id_genero), $precio, intval($id_libro)]);
            $validacion = $consulta->rowCount();
            return $validacion;
        } catch (Exception $e) {
            return -1;
        }
    }
    //Eliminar
    public function eliminarLibro($id)
    {
        $consulta = $this->db->prepare("DELETE FROM libros WHERE id_libro = ?");
        $consulta->execute([$id]);

        $validacion = $consulta->rowCount();

        return $validacion;
    }

    public function agregarEnOferta($id){
        $query = $this->db->prepare("UPDATE libros SET en_oferta = 1 WHERE id_libro = ?");
        $query->execute([$id]);

        return $this->obtenerLibroPorId($id);
    }
}
