<?php
class ComentariosModelo{
    private $comentarios;

    public function __construct()
    {
        $comentario1 = new stdClass();
        $comentario1->user = "Anon";
        $comentario1->comment = "Holaa";
        $comentarios = array($comentario1);
    }

    public function getComentarios(){
        return $this->comentarios;
    }

    public function addComment($comment){
        array_push($this->comentarios, $comment);
    }

}