"use strict"

const uri = "http://localhost/web2/tpe3/api/";
const form = document.querySelector("#form-libros");
form.addEventListener('submit', agregarLibro);


let libros = [];
let lista_generos = [];


document.addEventListener("DOMContentLoaded", ()=>{
    obtenerGeneros();
    obtenerLibros();
});

/*Funnciones asincronas */
async function obtenerGeneros(elemento = "#genero") {
    try {
        const response = await fetch(uri+"generos");
        lista_generos = await response.json();
        const select = document.querySelector(elemento);
        select.innerHTML = "";
        for (const element of lista_generos) {
            let op = document.createElement("option");
            op.value = element.nombre;
            op.innerHTML = element.nombre;
            select.appendChild(op);
        }
    } catch (error) {
        console.log(error);
    }
}

async function obtenerLibros(){
    //me falta mostrar los libros filtrados y por orden
   try {
       const response = await fetch(uri+"libros");
       if(!response.ok){
           throw new Error("Error al traer los libros");
       }
       libros = await response.json();
       console.log(libros);
       mostrarLibros();
       
   } catch (error) {
       console.log(error);
   }
}
async function borrarLibro(id_libro) {
    let id = id_libro;
    console.log(id);
    try {
        let response = fetch(uri+"libros/"+id, {method: 'DELETE'});
        if(!response.ok){
            throw new Error("Eror");
        }
    } catch (error) {
        
    }
    libros = libros.filter(libro => libro.id_libro != id );
    mostrarLibros();    

}
async function agregarLibro(e){
    e.preventDefault();
    let data = new FormData(form);
    
    let libro = {
        titulo: data.get('titulo'),
        autor: data.get('autor'),
        id_genero: data.get('genero'),
        paginas: data.get('paginas'),
        cover: data.get('cover'),
        precio: data.get('precio'),
    }
    
    try {

        let response = await fetch(uri+"libros", {
            method: "POST",
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(libro)
        });
        if(!response.ok){
            throw new Error("Error al llamar al servior");
        }
        document.getElementById("form-libros").reset();
        libros.push(libro);
        mostrarLibros();
        console.log(libros);
    } catch (error) {
        
    }

}


async function obtenerLibro(id_libro) {
    let id = id_libro;
    
    try {
        let response = await fetch(uri+"libros/"+id, {method: 'GET'});
        if(!response.ok){
            throw new Error("Eror");
        }
        let libro = await response.json();

        document.querySelector(".titulo").value =      libro[0].titulo;
        document.querySelector(".autor").value =       libro[0].autor;
        //document.querySelector(".genero").value =      libro[0].genero;
        document.querySelector(".cover").value =       libro[0].cover;
        document.querySelector(".inp-paginas").value = Number(libro[0].paginas);
        document.querySelector(".inp-precio").value =  Number(libro[0].precio);
        



    } catch (error) {
        
    }
}

async function editarLibro(id_libro) {
    
    const formEdit = document.querySelector("#form-edit");
    let data = new FormData(formEdit);
    
    let libroModificado = {

        titulo: data.get('titulo'),
        autor: data.get('autor'),
        paginas: data.get('paginas'),
        cover: data.get('cover'),
        id_genero: data.get('genero'),
        precio: data.get('precio'),
    }
    try {
        let response = await fetch(uri+"libros"+`/${id_libro}`, {
            method: "PUT",
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(libroModificado)
        });
        if(!response.ok){
            throw new Error("Error al llamar al servior");
        }
        let i = 0;
        libros.forEach(libro => {
            if(libro.id_libro == id_libro)
                libros[i] = libroModificado;
            else
                i++;
        });
        mostrarLibros();
        return ;

    } catch (error) {
        
    }

    /* document.getElementById("#form-edit").reset(); */
    
}

/*funciones*/
function mostrarLibros(){

   const ul = document.querySelector(".lista-libros");
   ul.innerHTML = "";
   for(const libro of libros){

       let li = document.createElement("li");
       li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-item-center");
       
       let p_titulo = document.createElement("p");
       p_titulo.innerHTML = `Titulo: ${libro.titulo}. Autor: ${libro.autor}. Precio: ${libro.precio}`
       
       let div = document.createElement("div");
       div.classList.add("d-flex", "flex-column", "gap-3");
       
       let buttonDelete = document.createElement("button");
       buttonDelete.classList.add("btn", "btn-danger", "eliminar-libro");
       buttonDelete.setAttribute('data', libro.id_libro);
       buttonDelete.textContent = "Borrar";
   
       let buttonEditar = document.createElement("button");
       buttonEditar.classList.add("btn", "btn-success", "editar-libro");
       //data-toggle="modal" data-target=
       buttonEditar.setAttribute('data',libro.id_libro);
       

       buttonEditar.textContent = "Editar";
        



       div.appendChild(buttonDelete);
       div.appendChild(buttonEditar);
   
       li.appendChild(p_titulo);
       li.appendChild(div);
   
       ul.appendChild(li);
   }

   const botonesBorrar = document.querySelectorAll('.eliminar-libro');
   for (const boton of botonesBorrar) {
    boton.addEventListener('click', (e)=>{
        e.preventDefault();
        borrarLibro(boton.getAttribute('data'));
        
    });
   }
    
    const botonesEditar = document.querySelectorAll('.editar-libro');
    for(const boton of botonesEditar){
        boton.addEventListener('click', (e)=>{
            e.preventDefault();
            let id = boton.getAttribute('data');
            
            mostrarModal(id);
        });
    }

}   

function mostrarModal(id){
    const libro_id = id;
    obtenerGeneros(".modal-genero");
    const modal = document.querySelector(".form-modal");
    const buttonClose = document.querySelector(".btn-close");
    const btnEditar = document.querySelector("#subm-editar");
    modal.classList.add("show");
    obtenerLibro(libro_id);    

    btnEditar.addEventListener('click', (e)=>{
        e.preventDefault();
        editarLibro(libro_id);
        modal.classList.remove("show");
        
    });

    buttonClose.addEventListener('click', (e)=>{
        e.preventDefault();
        modal.classList.remove("show");
        document.querySelector("#form-edita").reset();


    });
    
}


//48:00