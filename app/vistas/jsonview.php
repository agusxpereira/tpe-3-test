<?php
class JSONView{
    //nos genera todas las respuestas: rendiriza nuestro recurso como un JSON
    
    //El contenido del body se lo pasamos en el Controlador
    public function response ($body, $status = 200){
        header("Content-Type: application/json");
        $statusText = $this->_requestStatus($status);//el guion bajo se utliza para recordar que la funcion es privada
        header("HTTP/1.1 $status $statusText");

        echo json_encode($body);
    }

    private function _requestStatus($code){
        $status = array(
            200 => "Ok",
            201 => "Created",
            204 => "No content",
            400 => "Bad Request",
            404 => "Not Found",
            500 => "Internal Server Error"
        );
        //si el código existe dentro de nuestro arreglo lo devolvemos, sinó devolvemos 500
        return (isset($status[$code]) ? $status[$code] : $status[500]);
    }
}
?>