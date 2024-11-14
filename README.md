## Libros

Tabla de ruteo:

| Recurso               | Verbo  | Controlador         | Metodo         |
|:----------------------|:------:|:-------------------:|:--------------:|
| libros                | GET    | LibroApiController  | obtenerLibros  |
| libros/:id            | GET    | LibroApiController  | obtenerLibro   |
| libros                | POST   | LibroApiController  | agregarLibro   |
| libros/:id            | DELETE | LibroApiController  | borrarLibros   |
| libros/:id            | PUT    | LibroApiController  | editarLibro    |
| libros/:id/en_oferta  | PUT    | LibroApiController  | editarLibro    |

### GET libros

> `| libros      | GET    | LibroApiController  | obtenerLibros  |`

A este método le podemos agregar los siguientes queryParams:

- `ordenarPor`: Puede ser el campo precio o nombre. Son los unicos dos que definí.
- `ofertas` : Para filtrar los libros que estén en oferta.
- `orden`: ascendente o descentiente

Ejemplo: `(GET) api/libros?ordenarPor=precio&orden=descendente`

#### Ejemplo filtrado:

Para poder filtrar libros agregue el campo ´en_oferta´. Por ejemplo:

´(GET) api/libros?ofertas=true´


### Get libros/:id

> | libros/:id  | GET    | LibroApiController  | obtenerLibro   |

Este es el servicio para obtener un sólo libros según su id

Ejemplo: `(GET) api/libros/6`

### DELETE libro/:id

>| libros/:id  | DELETE | LibroApiController  | borrarLibros   |

Este es el servicio para eliminar un libro según su ID.

Ejemplo: `(DELETE) api/libros/6`

### POST libro

Un ejemplo de una insercion: 

```json
{
  "titulo": "titulo",
  "autor": "autor", 
  "genero_id":3 , 
  "paginas": 90, 
  "cover": "",
  "precio": 7500.0
}
```

`(POST) api/libros`

Los parametros que se fijan que estén para valdiar los datos son *"titulo"*, *"genero_id"* y *"precio"*. 

> En `genero_id` también acepta el nombre de cualquiera de los generos que existan. Esto tuve que hacerlo así para poder mantener un **select** en el frontend. Cuando llega un nombre, la funcion pregunta si es un numero y si no lo es llama a una funcion que obtiene el id según el nombre.



### PUT libros/:id

> | libros/:id  | PUT    | LibroApiController  | editarLibro    |

Para editar un libro necesitamos todo el tener el libro que vamos a editar, osea el `id` y alterar los campos que querramos (obligatoriamente debe contener un nombre, id_genero y precio).

Ejemplo: `(PUT) api/libros/:id/`

```JSON
{
    "id_libro": 25,
    "titulo": "Libro Editado",
    "autor": "Este valor va a ser editado editado",
    "paginas": 1,
    "cover": "",
    "id_genero": 1,
    "en_oferta": 0,
    "precio": 1000
  }
```


### PUT libros/:id/en_oferta

> | libros/:id/en_oferta  | PUT    | LibroApiController  | editarLibro    |

Este es el servicio para cambiar el campo `en_oferta`. 

> Si ya está en oferta lo saca.

Ejemplo: `(PUT) api/libros/:id/en_oferta`
```JSON
{
    "id_libro": 25,
    "titulo": "Libro Editado",
    "autor": "Este valor va a ser editado editado",
    "paginas": 1,
    "cover": "",
    "id_genero": 1,
    "en_oferta": 0,
    "precio": 1000
  }
```

### Paginacion:

Para realizar una paginacion de los libros agregue los siguientes queryParams:

- pagina
- limite

Que se utilizan sobre el get de libros normal. Ejemplo:

`(GET) api/libros?pagina=2&limite=5`

Si hubiesen 10 libros traeria los ultimos 5.

Si limite no es definido toma por defecto el valor 10. Si pagina no es definido va a funcionar como funcionaria normalmente sin los parametros.

Esta es la funcion que hace la páginacion:

```php
if(isset($pagina) && $pagina!=false){
  if(!isset($limite) || $limite == false)
     $limite = 10;   
  if (count($libros) > $limite) {
      //    13          10
      $final =  $limite * $pagina;//10 * 2
      $inicio = $final - $limite  // 20 - 10 
      if($final > count($libros)) // Si (20 > 13)
          $final = count($libros);// Final se transforma en la cantidad de libros*
      for($i = $inicio; $i < $final; $i++){
          array_push($librosLimitados, $libros[$i]);
      }   
  }
}
```

>  *: Esto seria en caso de ser la **última página**. Por ejemplo: si me piden la pagina 2 con limite de 10 y  hay 13 libros.
>
> Las varibales quedarian
> 
> $inicio = 10;
> 
> $final = 20;
> 
> count($libros) = 13;
> 
> En este caso me pasaria de largo, por eso hago este control, porque ya sé que estoy en el elemento 10 y entonces solamente voy a hasta el final de los libros que tengo, en este caso 13.

