1. MVC es una **arquitectura de software**, tiene las ventajas de: desacoplar codigo, separar la logica de la presentacion así como las reponsabilidades. Tambien permite reutilizar el codigo.


2 a. falso: El intermedio seria el controlador, el modelo es quien se comunica con la base de datos.

2 b. verdadero: Es el encargado de recibir los eventos que pasan en la vista, como clicks o forumlarios.

2 c. verdadero: En el patrón MVC, el modelo es el responsable de manejar la lógica de negocio y las reglas que rigen los datos de la aplicación. Esto incluye validaciones, cálculos, y cualquier lógica que determine cómo deben manejarse y manipularse los datos. Esto mantiene el controlador enfocado en la coordinación de flujos y la vista en la presentación de datos, siguiendo el principio de separación de responsabilidades.

No utilizaria este patron en páginas estaticas simples. O bien en Aplicaciones serverless o microservicios simples.



> Crear una aplicación MVC para listar los nombres de productos de una casa de limpieza. Al seleccionar uno se debe navegar a otra página donde se vea la descripción y precio.
