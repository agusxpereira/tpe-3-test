<?php

require_once './app/modelos/user.modelo.php';
require_once './app/vistas/jsonview.php';
require_once './libs/jwt.php';


class UserApiController{

    private $modelo;
    private $vista;
    

    public function __construct(){
        $this->modelo = new UserModelo();
        $this->vista = new JSONView();
       
        //lo mismo para las vistas
    }

    public  function getToken(){
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
        } else {
            // Manejo del caso donde no se encuentra el encabezado
            $auth_header = null;
        }
        
       
        $auth_header = explode(' ', $auth_header); // ["Basic", "dXN1YXJpbw=="]
        if(count($auth_header) != 2) {
            return $this->vista->response("Error en los datos ingresados", 400);
        }
        if($auth_header[0] != 'Basic') {
            return $this->vista->response("Error en los datos ingresados", 400);
        }

        $user_pass = base64_decode($auth_header[1]); // "usuario:password"
        $user_pass = explode(':', $user_pass); // ["usuario", "password"]

        $user = $this->modelo->getUserByNombre($user_pass[0]);
       
        // Chequeamos la contraseña
        
        if($user == null || !password_verify($user_pass[1], $user->password)) {
            return $this->vista->response("Error en los datos ingresados", 400);
        }
        // Generamos el token
       
        $token = createJWT(array(
            'sub' => $user->id,
            'email' => $user->email,
            'role' => 'admin',
            'iat' => time(),
            'exp' => time() + 60,
            'Saludo' => 'Hola',
        ));
        
        return $this->vista->response($token);
        
    }
}