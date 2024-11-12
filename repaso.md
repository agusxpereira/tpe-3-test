El router nuevo agrega una ruta con una url, verbo, controlador y método. Una vez agregadas les decimos que rutee. Entonces itera sobre las tablas de rutas que fuimos creando viendo cual encaja, y llama al controller correspondiente. 

> Es el router quien implementa nuestros objetos request y response


> Como se maneja el router:

    $router->route($_GET['resource'], );
    Cuando accedemos a una url, por ejemplo:
    (recordemos que reescribimos el htacces)
    
    /api/libros/13
    
    llama a:

    router.php?resource=tareas/15

    por lo que: $_GET['resource'] = tareas/15;

    Esa ruta se la pasamos al router para que matchee esa ruta (para que la busque en la tabla de rutas que añadimso)

    > $_SERVER['REQUEST_METHOD']: contiene informacion del verbo que estamos usando

> Match

    Para saber si una funcion matchea primero compara los verbos, si estos son distintos retorna. Depues cuenta cuenta si la url tiene la misma cantidad de elemtnos. Luego compara que sean iguales. (if $part[0] != ":") // si no es una variable

    Si es una variable directamente la guardamos en el objeto request.


> comentario
>
    Entonces, reponse se crea en la vista, pero el $res que recibe el controlador es el que tiene definido al usuario no confundir. Y el request se crea en el controlador

> Filtrar:

    Si queremos filtrar no podemos hacerlo así: `libros/ofertas` porque no es un recurso, debemos hacerlo con queryParams: `libros?ofertas=true`

    para implementar este filtro debemos hacerlo en el controlador. (como todo lo que es lógica).

    Tenemos dos maneras de hacerlo, una es hacer un llamado distinto a la base de datos, (pasandole un parametro que indique que queremos ofertas en 'obtenerLibros').

    Otra es llamar a una funcion distinta, 'obtenerLibrosOfertas()'

    y una mas es  implementar un filtro en el controlador que recorra los libros que ya pedimos con 'obtenerLibros' y borra las que no estén finalizadas.

    Nosotros vamos a hacer una llamada distinta a la base de datos.


> Ahora que ya defini vario filtros, veamos como funciona:
>
#### Esto nos queda en el arreglo GET
> route.php?resource=api/libros?ofertas=true&ordenarPor=autor&orden=descendente
> $_GET['resource'] = api/libros
> $_GET['ofertas']= true
> $_GET['ordenarPor']= autor
> $_GET['orden']= descendente

#### Cuando llamamos al router pasa esto:

Lo unico que le pasamos es el `$_GET['resource']`. Por ahora los demas get quedan aguera. 

#### Los demás quedan en el request.php

Este archivo hacía lo siguiente:
1. Definia un `private $query = null`
2. Luego lo incializaba en la funcion construct: `$this->query = (objet) $_GET`
3. Castea en un objeto, lo que quiere decir que todo se $query se manejja como un objeto interno
   - `$this->query` es el atributo por ahora definido como null
   - luego este es el objeto con los atributos que obtiene de get
   - Por ejemplo: 
     - `$req->query->ordernarPor = precio` (así llamandolo desde afuera usando uso del request en controlador, que tiene un atributo `$req`, que luego pasamos por parametro cuando llamamos a un controlador)
     - `$req->query->ofertas = true`
     - `$req->query->orden = ascendente`

> Los params de este objeto reuest tambien vienen del router
>
> Nustro sistema de Ruteo ahora SIEMPRE nos manda dos parametros

## POST y PUT

Estos métodos tienen la particularidad de que necesitamos enviar informacion.

Para agregar un libro:
 
```JSON
    {
       "titulo": "titulo",
       "autor": "autor", 
       "genero_id": 3, 
       "paginas": 90, 
       "cover": ""
    }
```

Esto sería el body, cuando ruteamos, nuestro ruter arma el body y lo convierte en un objeto estandar de PHP para que podamos accederlo:

```php
$body = $req->body;
var_dump($body);
```

Nos devuelve algo del tipo:

```php
object(stdClass)#3 (5) {
["titulo"]=>
string(6) "titulo"
["autor"]=>
string(5) "autor"
["genero_id"]=>
int(3)
["paginas"]=>
int(90)
["cover"]=>
string(0) ""
}
```


> La api es RESTful porque hace foco en el recurso.


### SPA CSR

Empezamos con un HTML básico. Debemos agregar un formulario para agregar libros y los libros de hecho.

### Cambiar el valor a un campo
<!-- 1:00:01 https://drive.google.com/file/d/1cDphGCldQv8FuOG4yOlSS23Y1Wjkz3VP/view -->

Llamamos al recurso, en mi caso `libros/:id` con el verbo PUT. En el Body deberiamos mandar todos los campos. En ese caso, debemos recuperar los viejos, modificar el campor que queremos cambiar y enviarlo todo junto.


    PUT libros/:id/en_oferta -> body {"en_oferta" = 1}
                    #Acá siempre va el campo que queremos modificar
    Como programadores podemos dar la opcion de cambiar un sólo campo de esta manera usando PUT

    O bien podriamos usar el verbo PATCH  a la URI 

    PATCH libros/:id -> body = {"en_oderta" = 1}

    En este caos habria que programar la API rest para que se fije dinamicamente que campo estamos queriendo cambiar 


> En el trabajo puedo hacer el editar libro con un modal que muestre un nuevo formulario con los campos de la tarea que queremos editar.



> No se porque pero en obtenerLibroPorId retorna un Arreglo que contiene un ojeto
>
> Cunado lo accedo así: `$libro[0]`


> se vé así : 
> stdClass Object 
    ( 
    [id_libro] => 6 
    [titulo] => El principito 
    [autor] => Antoine De Saint de Exuperry 
    [paginas] => 102 
    [cover] => 
    [id_genero] => 2 
    [en_oferta] => 1 
    [precio] => 9000 
    )

### paginacion y queryParams

Usamos los queryParams para modificar el GET a un recurso. Puede ser orden, filtro o paginaciones. 

Por ejemplo: `libros?page2&limit=10` -> paginacion (programarlo como sea como salga)


### Seguridad

> Las api rest deben ser stateless. La diferencia a como manteniamos la sesion anteriormente es que manteniamos el estado de la sesion para cada usuario. 
>
> Los metodo mas usados son 
- basic: enviamos en los headers el par **usuario/contraseña** en base64

```js
const response = await fetch (`${url}/tarealibros/${id}`, {
    headers: {
        'Authorization': 'Basic d2ViMoxMjM0NTY=',
        'Content-Type': 'application/json'
    }
});
```

> En cada llamado pasamos nuestro usuario y contraseña. En este caso la contraseña viaja casi sin codificar y nos la quedamos en memoria de nuestra app. 

- api key:
  - Se genera una clave única por usuario por servicio
  - la clave es un valor al azar
  - Funciona como una llave para ese servicio
```js
    const response = await fetch (`${url}/libros/${id}`,{
        headers:{
            'Authorization': 'API Key valor',//clave aleatoria
            'Content-Type': 'application/json'
        }
    })
```

> las claves deben almaenarse entre sesiones.

#### Bearer 

> la mayoria de los problmas de tener que almacenar las contraseñas se solucionan con este método
>  Es muy parecido a una api key pero no es aleatorio. El objetivo del token es almacenar informacion del usuario. Tambien se puede definir un vencimiento.
>

#### OAuth

- Usa dos tokens provistos por el servidor:
    - Acces TOken: permite usar el servicio (puede ser unBearer)
    - Refresh token: permite obtener un nuevo token de acceso(opt.)
- Permite que el servidor de autenticacion difiera del de servicio.
- Varias formas de autenticacion posibles:
  - Autorization Code FLow
  - Implicit flow
  - Resource Owner Password flow
  - Client Credentials flow
  
#### JWT

JSON WEB TOKENS:
- codificados en un string
- Estandar RFC 7519
- Permite generar, decoficar y verificar los tokens (lo firma la API)

Tiene una cabezara que suele ser la misma siempre (define un algoritmo), y un payload, que es lo que nosotros querrramos. Y por último una firma y un secreto que se concatena con .

Cada cadena concatenada pertenece al hader, al payload y a la firma.

           /*
            si vienen 13 libros: 
            y pagina = 2;
            [. . . . . . . . . . . . . ]
                                [      ]
            
            
            
            
            
                                */




la idea seria que un usuario envia una una peticion GET a api/usuarios/token con su email y contraseña. Nosotros validamos y creamos el token. Ahora queda verificar ese token por cada peticion.

> Este es el token que me genera: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOm51bGwsImVtYWlsIjpudWxsLCJyb2xlIjoiYWRtaW4iLCJpYXQiOjE3MzEyOTUxNDIsImV4cCI6MTczMTI5NTIwMiwiU2FsdWRvIjoiSG9sYSJ9.d7Z1jdbiqDWjXEVDt17m1bsqfBaWBxqdXEUG5nX6_w0"
>

## Repaso parcial



- **Ejercicio 2**


Implemente el siguiente requerimiento siguiendo el patrón MVC. No es necesario realizar las vistas, sólo controlador(es), modelo(s) y las invocaciones a la vista.

- Se debe mostrar una lista de turnos correspondientes al día actual.
- Se debe informar el nombre de la mascota y su peso.
- Mostrar el total de turnos del día.



```php
<?php
    public function getTurnosDiaActual(){
        $fechaActual = $this->obtenerFechaActual();
        $dia = $fechaActual->dia;
        $mes = $fechaActual->mes;
        $anio = $fechaActual->anio;
        $turnos = $this->turnoModel->getTurnosDiaActual($dia, $mes, $anio);
        $totalTurnos = count($turnos);
        foreach ($turnos as $turno) {
            $mascota = $this->mascotaModel->getMascota($turnos->id_mascota);
            $turno->nombre_mascota = $mascota->nombre;
            $turno->edad = $mascota->edad;
        }
        return $this->view->getTurnosDiaActual($turnos, $totalTurnos);
    }
?>
```

> Primero pedimos el día actual y lo guardamos en la variable `fechaActual`. Luego la usamos para pedir los turnos de el dia actual y los guardamos en una variable llamada `turnos`. Por cada turno buscamos la mascota registrada para dicho turno:
>
> `$mascota = $this->nombre_mascota->getMascota($turnos->id_mascota);`
>
> Por ultimo, agregamos al turno actual la informacion de estas mascotas, y al finalizar la iteraicon devolvemos a una vista y le pasamos el toatl de turnos (que es un numero) y los turnos con las mascotas. La vista se encarga de mostrarlos.
>


- **Ejercicio 1 - Alta de turno**
  
Implemente el siguiente requerimiento siguiendo el patrón MVC. No es necesario el router, ni las vistas ni los modelos: sólo la función del controlador y middlewares necesarios.
Se debe poder agregar un turno indicando todos los datos necesarios y cumpliendo las siguientes condiciones. Informar los errores correspondientes en caso de no cumplirlos.

1. Controlar posibles errores de carga.
1. Verificar que el usuario esté logueado.
1. Verificar que la mascota exista.
1. Verificar que no haya más de 3 turnos en una misma hora.


```php
    //ejercicio 1
    public function addTurno(){
        //hacemos las correspondientes controles
        if(!isset($_POST['dia']) || empty($_POST['dia'])){
            return $this->vista->mostrarMensaje('Falta completar el dia');
        }
        if(!isset($_POST['id_mascota']) || empty($_POST['id_mascota'])){
            return $this->vista->mostrarMensaje('Falta agregar la mascota');
        }
        if(!isset($_POST['mes']) || empty($_POST['mes'])){
            return $this->vista->mostrarMensaje('Falta completar el mes');
        }
        if(!isset($_POST['anio']) || empty($_POST['anio'])){
            return $this->vista->mostrarMensaje('Falta completar el anio');
        }
        if(!isset($_POST['hora']) || empty($_POST['hora'])){
            return $this->vista->mostrarMensaje('Falta completar el hora');
        }

        $dia = $_POST['dia'];
        $mes = $_POST['mes'];
        $id_mascota = $_POST['id_mascota'];
        $anio = $_POST['anio'];
        $hora = $_POST['hora'];

        if($this->mascotaModel->getMascota($id_mascota)){
            return $this->vista->mostrarMensaje("La mascota no existe");
        }
        $totalTurnos = count($this->turnoModel->getTurnosDiaActual($dia, $mes, $anio, $hora));
        //obtenemos los turnos del dia
    
        if($totalTurnos >= MAX_TURNOS_POR_HORA){
            return $this->vista->mostrarMensaje("No se pueden agregar mas de ". MAX_TURNOS_POR_HORA. ' turnos a la misma hora');
        }

        $id = $this->turnoModel->insertarTurnos($dia, $mes, $id_mascota, $anio, $hora);
        if(!$id){
            $this->vista->mostrarMensaje("No se pudo agregar intentalo de nuevo");
        }
        $this->vista->mostrarMensaje("Se agrego correctamente");
    }

```

A diferencia de la funcion anterioir, acá pasamos `$hora` para simular que pedimos los turnos de determinado dia en determinada ----------

Ahora miremos los middlewares:

```php
<?php
function sessionAuthMiddleware($res)
{
    session_start();
    if (isset($_SESSION['NOMBRE'])) {
        $res->user = new stdClass();
        $res->user->id = $_SESSION['ID_USER'];
        $res->user->nombre = $_SESSION['NOMBRE'];
        return;
    }
}

?>
```
```php
<?php

173 B

<?php
function verifyAuthMiddleware($res)
{
    if ($res->user) {
        return;
    } else {
        header('Location: ' . BASE_URL . 'showLogin');
        die();
    }
}
?>
```

Solamente declaramos un `$user` en el controlador y después se lo pasmos a la vista cuando la inicializamos.


`$this->view = new TurnoView($this->res->user);`

La vista hace esto: 

```php
class TurnoView{
    private $user = null;

	public function __construct($user)
	{
		$this->user = $user;
	}

//caundo llamamos a una funcion
public function getTurnosDiaActual($turnos, $cantidadTurnos)
	{
		$user = $this->user;
		require 'app/templates/layout/header.phtml';
		require 'app/templates/lista_turnos.phtml';
		require 'app/templates/layout/footer.phtml';
	}
//...
```

Y esto hacía el `model`:

```php
<?php
public function getTurnosDiaActual($dia, $mes, $anio, $hora = null)
	{
		$query = 'SELECT *
        FROM turno t
        WHERE t.dia = ? AND t.mes = ? AND t.anio = ?';

		$queryExecute = [$dia, $mes, $anio];

		if ($hora) { // Solo ej. 1, no se pedía para el parcial
			$query = $query . ' AND t.hora = ?';
			array_push($queryExecute, $hora);
		}

		$query = $query . ' ORDER BY t.hora';

		$query = $this->db->prepare($query);
		$query->execute($queryExecute);
		return $query->fetchAll(PDO::FETCH_OBJ);
	}

?>
```


Podemos ver como se va armando el query y el arreglo a ejecutar según si existe o no la hora.

### Repaso RECU 06/11/23

" plataforma para la venta de tickets de recitales"

Tablas:

    Ventas(id: int, id_evento: int, id_usuario: int, cant_entradas: int, fecha_compra: date)

    Eventos(id: int, nombre: varchar, precio: float, fecha_evento: date)

    Eventos(id: int, nombre: varchar, precio: float, fecha_evento: date)

implemente el siguiente requerimiento siguiendo el patrón MVC

>  No es necesario realizar las vistas, solo controlador(es), modelo(s) y las invocaciones a la vista.

1.  Controle posibles errores de carga. 
1. Los datos ingresados deben obtenerse por POST
1.    Verificar que el evento y el usuario existan
1. Controlar que existan suficientes entradas disponibles para efectuar la venta 
1.  En caso de poder realizar la venta, actualizar la cantidad de entradas restantes.


#### Resolucion:

- VentasController
```php

class VentasController{
    $private $modeloEventos;
    $private $modeloUsuarios;
    $private $modeloVentas;
    $private $vistaVentas;
    public function __construct(){
        //inicializamos
    }

    public function venderEntrada(){
        if(!isset($_POST['id_evento']) || empty($_POST['id_evento']))
            return $this->vistaVentas->mostrarMensaje("No hay ningun evento seleccionado");
    
        if(!isset($_POST['id_usuario']) || empty($_POST['id_usuario']))
            return $this->vistaVentas->mostrarMensaje("Falta completar usuario");
    
        if(!isset($_POST['cant_entradas']) || empty($_POST['cant_entradas']))
            return $this->vistaVentas->mostrarMensaje("No hay ninguna cantidad seleccionada");
        
        if(!isset($_POST['fecha']) || empty($_POST['fecha']))
            return $this->vistaVentas->mostrarMensaje("No hay ninguna fecha seleccionada");
        

        $id_evento = $_POST['id_evento'];
        $id_usuario = $_POST['id_usuario'];
        $cant_entradas = $_POST['cant_entradas'];
        $fecha = $_POST['fecha'];
        
        if(!$this->modeloEventos->getEventById($id))
            return $this->vistaVentas->mostrarMensaje("Este evento no existe");
        
        if(!$this->modeloUsuarios->getUserById($id))
            return $this->vistaVentas->mostrarMensaje("Este usuario no existe");
        
        
        $entradasDisponibles = $this->modeloEventos->obtenerEntradasDisponibles($id_evento);
        if($cant_entradas > $entradasDisponibles){
            return $this->vistaVentas->mostrarMensaje("No hay entradas suficientes");
        }

        $this->modeloEventos->setEntradasDisponibles($id_evento, $cant_entradas);

        //me falto crear la venta lo más importante
        // en el repo de la clase la funcion está así:
        //$venta = $this->ventasModel->create($id_evento, $id_usuario,$cant_entradas, now())
        //tampoco se pide la fecha porque la fecha de la venta va a ser "ahora"

        $venta = $this->modeloVentas->nuevaVenta($id_evento, $id_usuario, $cant_entradas, now());
        //siempre a vamos a devolver el objeto creado porque esto es una buena practica

        $this->vista->ventaCreada($venta);
    }   
}
```

- modeloEventos
```php
<?php

class ModelEvents{
    private $db;

    public function __construct(){
        $this->db = new PDO();
    }


    public function obtenerEntradasDisponibles($id){
        $query = $this->db->prepare("SELECT entradas_disponibles FROM Eventos WHERE id = ?");
        $query->execute([$id]);
        $result = $query->fetchAll(PDO::OBJ);
        $cantidad = $result[0]->entradas_disponibles;
        return $cantidad;
    }

    public function setEntradasDisponibles($id, $cantidad){

        $nuevaCantidad = $this->obtenerEntradasDisponibles($id) - $cantidad;
        $sentence = $this->db->prepare("UPDATE `Eventos` SET entradas_disponibles = ? WHERE id = ?");
        $sentence->execute([$nuevaCantidad]);
        $id = $sentence->rowCount();
        return $id;
    }
}

?>
```

> En MariaDB y MySQL, la sintaxis correcta para actualizar un registro en una tabla no incluye FROM.

### parcial 23/10/23

Es el mismo sistema que el casi anterioir. Pero ahora nos piden:

> Listar todas las ventas realizadas en un día dado y para un evento dado. Por cada venta se deberá indicar id de la venta, cantidad de entradas, y precio total abonado.


- VentasController
```php
private $eventoModel;
private $ventasModel;
private $ventasVista;
//si me lo pidieran acá tendia un $res
class VentasController{

    public function __construct(){
        $this->eventoModel = new EventoModel();
        $this->ventasModel = new VentasModel();
        $this->ventasVista = new VentasVista();//y como parametro iria $res->user
    }

    public function getVentasDia(){

        if(!isset($_GET['dia']) || empty($_GET['dia']))
            return $this->vista->mostrarMensaje("No se especifico un día");
        
        if(!isset($_GET['mes']) || empty($_GET['mes']))
            return $this->vista->mostrarMensaje("No se especifico un mes");
        
        if(!isset($_GET['anio']) || empty($_GET['anio']))
            return $this->vista->mostrarMensaje("No se especifico un año");
        
        if(!isset($_GET['id_evento']) || empty($_GET['id_evento']))
            return $this->vista->mostrarMensaje("No se especifico un evento");
        
        $dia = $_GET['dia'];
        $mes = $_GET['mes'];
        $anio = $_GET['anio'];
        $id_evento = $_GET['id_evento'];

        $evento = $this->modeloEventos->getEventoById($id_evento);

        if(!$evento)
            return $this->vistaVentas->mostrarMensaje("El evento no existe");

        $ventas = $this->modelo->getVentasFecha($anio, $mes, $dia);
        foreach($ventas as $venta){
            $venta->id_evento = $evento->id;
            $venta->precio_evento = $evento->precio;
        }

        return $this->vista->mostrarVentasFecha($ventas, $anio, $mes, $dia);//para poder mostrar el dia tambien
    }
}
```
- ModeloVentas
```php
private $db;//conexion a base de datos
class ModeloVentas{
    public function __construct(){
        $this->db = new PDO();
    }

   public function getVentasFecha($anio, $mes, $dia){
    //los suponesmos como correctos

    $fechaBuscada = "$anio-$mes-$dia";

    $sql = "SELECT * FROM `ventas` WHERE fecha_compra = ?";
    $sentence = $this->db->prepare($sql);
    $sentece->execute([$fecahBuscada]);

    $ventas = $sentece->fetchAll(PDO::FETCH_OBJ);

    return $ventas;
   }

}
```
- ModeloEventos
```php
private $db;//conexion a base de datos
class ModeloEventos{
    public function __construct(){
        $this->db = new PDO();
    }
    public function getEventoById($id){
        $sql = "SELECT * FROM `Eventos` WHERE id = ?";
        $sentence = $this->db->prepare($sql);
        $sentece->execute([$id]);     
        $ventas = $sentece->fetchAll(PDO::FETCH_OBJ);       
        return $ventas;
    }
}
```

#### Comparacion con la resolucion publicada

> Para empezar componen la fecha en un sólo atributo por lo que el control cambia un poco :p

```php
if (empty($_GET['fecha_compra']) || empty($_GET['id_evento'])) {
            $this->view->showError('Falta ingresar datos obligatorios');
            return;
        }

```

> Acá hice algo parecido, solamente que manda toda la fecha junta y pregunta tambien por el evento del ID, es decir buscar por las dos cosas en Ventas, lo que es un error mio, por que si hay dos eventos en la misma fecha, mi solucion no distinguiria:

```php

   $ventas = $this->ventaModel->getByFechaYEvento($fechaCompra, $idEvento);
   foreach($ventas as $venta) {
       $venta->total = $venta->cant_entra * $evento->precio;
   }

```

> tambien hay un problema con el total del precio, el tema es que claro, cada venta  puede varian la cantidad de entrada, por lo que esta bien calularlo así. Yo por a cada venta le asignaba nada más el precio del evento pero no calculaba. Prestar Mucha atencion a estas cosas. Lo que si hice bien fue pedir el precio al objeto evento que ya habia creado. Tambien aclarar que ventas ya tiene un id_evento asignado por lo que no es neceseario asignarselo.


> Así obtenemos la ventas con lo parametros solicitados, según las varibales que pedimos:
>
```php
    public function getByFechaYEvento($fechaCompra, $idEvento) {
        $db = new PDO('mysql:dbname=test;host=localhost', 'root', '');

        $query = $db->prepare("SELECT id, cant_entra FROM ventas WHERE fecha_compra = ? AND id_evento = ?");
        $query->execute([$fechaCompra, $idEvento]);

        return $query->fetchAll(PDO::FETCH_OBJ);
    }
```

> Esto yo tambien lo habia hecho distinto, parte del error empieza en no haber diferenciado los eventos de la misma fecha (el AND id_evento). Y la otra es que acá seleccionan los parametros de la consulta, no se traen el registro entero, solo que se pide.

> Despues el evento si esta bien, lo unico que cambia es un fetchAll por un `->fetch(PDO::FETCH_OBJ);`


Tengo que prestar mas atencion y leerlo un poco mas, si bien lo de las fecha no lo consideraria un error la verdad es que no se si está para aprobar mi solucion.


