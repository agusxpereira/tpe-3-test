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
}
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
//errores de carga, se obtienen por post
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
        //verificamos evento y usuario
        if(!$this->modeloEventos->getEventById($id))
            return $this->vistaVentas->mostrarMensaje("Este evento no existe");
        
        if(!$this->modeloUsuarios->getUserById($id))
            return $this->vistaVentas->mostrarMensaje("Este usuario no existe");
        
        //controlamos las entradas
        $entradasDisponibles = $this->modeloEventos->obtenerEntradasDisponibles($id_evento);
        if($cant_entradas > $entradasDisponibles){
            return $this->vistaVentas->mostrarMensaje("No hay entradas suficientes");
        }
        //actualizamos las entradas disponibles
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
        $result = $query->fetchAll(PDO::OBJ);//esto enrealidad lo tengo que cambiar
      //$result = $query->fetch(PDO::FETCH_OBJ);
        $cantidad = $result[0]->entradas_disponibles;
      //$cantidad = $result->entradas_disponibles
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


## Ejercicios adicionales

### Ejercicio 1: gestion de reservas de sala de conferencias

- SalaController

```php

private $reservasModel;
private $salasModel;
private $usuariosModel;
private $resercasView;

class ReservasController{

    public function __construct(){
        $this->reservasModel = new ReservasModel();
        $this->usuariosModel = new UsuariosModel();
        $this->salasModel = new SalasModel();
        $this->reservasView = new reservasView();
    }

    public function crearReserva(){

        if(!isset($_GET['fecha_reserva']) || empty($_GET['fecha_reserva']))
            return "no se seleccionó ningúna fecha";
        if(!isset($_GET['id_sala']) || empty($_GET['sala']))
            return "No se seleccinó ninguna sala";
        if(!isset($_GET['id_usuario']) || empty($_GET['id_usuario']))
            return "No se seleccinó ningun usuario";
    
        $id_usuario = $_GET['id_usuario'];
        $id_sala = $_GET['id_sala'];
        $fecha_reserva = $_GET['fecha_reserva'];

        $fecha = $fecha_reserva->fecha
        $hora_inicio = $fecha_reserva->inicio;
        $hora_fin = $fecha_reserva->fin;


        $usuario = $this->usuariosModel->getUserById($id_usuario);
        $sala = $this->salasModel->getsalaById($id_sala);

        if(!$usuario){
            return $this->view->mostrarMensaje("No existia ese usuario");
        }
        if(!$sala){
            return $this->view->mostrarMensaje("No existia esa sala");
        }

        $confirmacion = $this->reservasModel->sala_disponible($id_sala, $fecha, $hora_inicio, $hora_fin);

        if($confirmacion == false)
            return $this->view->mostrarMensaje("La sala no estaba disponible");
        
        $this->reservasModel->reservar_sala($id_sala, $fecha, $hora_inicio, $hora_fin);
        return $this->view->mostrarMensaje("La sala fue alquilada con exito");

    
    }
}
```
- SalaModel
```php
private $db;
class SalasModel(){
    public function __construct(){
        $this->db = new PDO();//iniciamos el PDO
    }
    
    public function getSalaById($id){
        $sentente = $this->db->prepare("SELECT * FROM `salas` WHERE id = ?");
        $sentence->execute([$id]);

        $sala = $sentence->fetch(PDO::FETCH_OBJ);
        //devuelve un objeto anonimo donde cada propiedad corresponde a una colmna del registro, si no se encontro nada $sala = falsa;
        return $sala;
    }

}


```
- ReservaModel
```php
private $db;
class ReservaModel(){
    public function __construct(){
        $this->db = new PDO();//iniciamos el PDO
    }

    public function sala_disponible($id_sala, $fecha_reserva, $inicio, $fin){

        $sentence = $this->db->prepare("SELECT * FROM `reservas` WHERE id_sala = ? AND fecha = ? AND hora_inicio = ? AND hora_fin = ?");

        $sentence->execute([$id_sala, $fecha_reserva, $inicio, $fin]);

        $sala_disponible = $sentenec->fetch(PDO::FETCH_OBJ);    

        if($sala_diponible == false)
            return false;

        return true;

    }

    public function reservar_sala($id_sala, $fecha, $hora_inicio, $hora_fin, $id_usuario){
        $sentence = $this->db->prepare("INSERT INTO `id_sala`, `id_usuario`, `fecha`, `id_usuario`, `hora_inicio`, `hora_fin` VALUES (?, ?, ?,? ?)");
        $sentence->execute([$id_sala, $id_usuario, $fecha, $hora_inicio, $hora_fin]);
        $id = $this->db->lastInsertId();
        return $id;
    }
}

```
- UsuarioModel
```php
private $db;
class UsuarioModel(){
    public function __construct(){
        $this->db = new PDO();//iniciamos el PDO
    }
}
```

### Ejercicio 2: 

Tablas:

- Pedidos: id, id_usuario, fecha_pedido, total.
- Productos: id, nombre, precio, stock.
- DetallePedido: id_pedido, id_producto, cantidad.

<!-- Controlador Pedidos -->
```php
class PedidosController{
    //gestiona los pedidos del restarutante
    private $pedidosModel;
    private $detallesModel;
    private $productosModel;
    private $view;

    public function __construct(){
        $this->pedidosModel = new PedidosModel();
        $this->productosModel = new ProductosModel();
        $this->detallesModel = new DetallersModel();
        $this->view = new ProductosView();
    }

    public function realizarPedido(){
        //verificamos que se esten pidiendo productos
        $descuento = false;
        if(!isset($_POST['id_producto']) || empty($_POST['id_producto'])){
            return $this->view->mostrarMensaje("No se seleccionó ningún producto");
        }
        //verificamos que nos pasen un id_usario
        if(!isset($_POST['id_usuario']) || empty($_POST['id_usuario'])){
            return $this->view->mostrarMensaje("No se seleccionó ningún usuario");
        }
        //controlamos la cantidad de productos pedidos:
        if(!isset($_POST['cantidad_productos']) || empty($_POST['cantidad_productos'])){
            return $this->view->mostrarMensaje("No se seleccionó ningúna cantidad");
        }
        //verificamos si hay descuentos
        if(isset($_POST['descuento']) && !empty($_POST['descuento'])){
            $descuento = $_POST['descuento'];
        }


        $id_usuario = $_POST['id_usuario'];
        $cantidad = $_POST['cantidad_productos'];
        //verificamos que los productos estén disponibles en el inventario
        $producto = $this->productosModel->getProductoById($id_producto);
        if(!$producto || $producto->stock == 0){
            return $this->view->mostrarMensaje("Este producto no está disponible");
        }

        $costoTotal = $cantidad * $producto->precio;
        if($desceunto){
            $costoTotal = $costoTotal - ($descuento/100) * $costoTotal;
        }

        //actualizamos el inventario
        $this->productosModel->actualizarInventario($id_producto, $cantidad);
        
        //mostramos el pedido detallado
        //aca lo creo como deberia quedar
        $id_pedido = $this->pedidosModel->realizarPedido($id_usuario, now(), $costoTotal);
        /*Me falto crear un pedido nada mas que devueva el id_del pedido, en la funcion de abajo lo pasamos pero nunca lo obtenemos de ningun lado*/
        $pedidoDetallado = $this->detallesModel->crearPedidoDetallado($id_pedido, $id_producto, $cantidad);
        return $this->view->mostrarDetalle($pedidoDetallado);

    }
}

```
### Ejercicio 3

Prestamos de bibliotecas

1. Crear un controlador para gestionar los prestamos de libros
2. verificar que el libro y el usuario existan
3. controlar que el libro el libro no esté prestado y el usuario no tenga mas de 3 libros
4. registrar el prestamo, mostrar el detalle del prestamo y actualizar el estado del libro

Tablas

- Prestamos: id, id_libro, id_usuario, fecha_prestamo, fecha_devolucion.
- Libros: id, titulo, autor, estado.
- Usuarios: id, nombre, tipo_usuario (estudiante/profesor), cantidad_libros.

1: PrestamosController

```php

class PrestamosController{
    private $librosModel;
    private $usuariosModel;
    private $prestamosModel;
    private $view;


    function __construct(){
        //asumismos las inicializaciones
    }

    //funcion para pedir prestado un libros
    public function pedirLibro(){
        if(isset($POST['id_usuario']) || empty($POST['id_usuario']))
            return $this->view->mostrarMensaje("No hay ningún usuario seleccionado")
        if(isset($POST['id_usuario']) || empty($POST['id_usuario']))
            return $this->view->mostrarMensaje("No hay ningún libro seleccionado")
        
        $id_libro = $_GET['id_libro'];
        $id_usuario = $_GET['id_usuario'];
        
        $libro = $this->librosModel->getLibroById($id_libro);
        $usuario = $this->usuariosModel->getUsuarioById($id_usuario);

        if(!$libro)
            return $this->view->mostrarMensaje("No hay existe ese libro");
        if(!$usuario)
            return $this->view->mostrarMensaje("No hay existe ese usuario");
        
        //si llegamos hasta acá es porque existe el libro y el usuario
        $libros_prestados = $usuario->libros_prestados;
        if($libro->estado == "prestado"){
            return $this->view->mostrarMensaje("El libro ya fué prestado"); 
        }
        if($libros_prestados >= 3){
            return $this->view->mostrarMensaje("No podemos prestarte más libros");
        }

        $prestamo = $this->prestamosModel->registrarPrestamo($id_libros, $id_usuario, now(), now()+7);
        $this->librosModel->actualizarEstado($id_libro, "prestado");
        //me falto actualizar la cantidad de libros prestados al usuario
        return $this->view->mostrarDetalle($prestamo);
    }

}

```