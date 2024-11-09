## Libros

Tabla de ruteo:

| Recurso     | Verbo  | Controlador         | Metodo         |
|:------------|:------:|:-------------------:|:--------------:|
| libros      | GET    | LibroApiController  | obtenerLibros  |
| libros/:id  | GET    | LibroApiController  | obtenerLibro   |
| libros/:id  | DELETE | LibroApiController  | borrarLibros   |
| libros      | POST   | LibroApiController  | agregarLibro   |
| libros/:id  | PUT    | LibroApiController  | editarLibro    |

### GET libros

A este método le podemos agregar los siguientes queryParams:

- `ordenarPor`: Puede ser el campo precio o nombre. Son los unicos dos que definí.
- `ofertas` : Para filtrar los libros que estén en oferta.
- `orden`: ascendente o descentiente

Ejemplo: `api/libros?ordenarPor=precio&orden=descendente`

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

Los parametros que se fijan que estén para valdiar los datos son *"titulo"*, *"genero_id"* y *"precio"*. 



