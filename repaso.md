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




