<?php
	/*
        Al cargar la página sin argumentos:
        - Boton: agregar vacuna
        - Mostrar vacunas

        Sector lote de vacunas:
        - Form de seleccionar la vacuna que se desea ver los lotes, con botón filtrar
        - Botón de agregar lote
        - Tabla: id lote, vacuna, info, botón eliminar, botón editar
    */

// Si no hay lote de vacuna seleccionada, se carga un array vacío para que la tabla no de error.
$body = <<<HTML
<div class="container">
    <h1>Vacunas</h1>

    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarVacuna">
          Agregar nueva vacuna
        </button>
    </div>
    <!-- Tabla de vacunas -->
    <div class="card shadow-sm rounded-3 mb-3">
        <div class="card-body table-responsive">
            <table id="tablaVacunas" class="table table-striped table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Nombre</th>
                        <th class="text-primary">Vía Aplicación</th>
                        <th class="text-primary">Marca</th>
                        <th class="text-primary">Enfermedad</th>
                        <th class="text-primary">✏</th>
                        <th class="text-primary">❌</th>
                    </tr>
                </thead>
                <tbody id="vacunas">
                    <!-- Los datos se insertarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal agregar Vacuna -->
    <div class="modal fade" id="agregarVacuna" tabindex="-1" aria-labelledby="newVacunaModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="newVacunaModal">Agregar Vacuna</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="agregarVacunaForm" class="needs-validation" novalidate>
                        <div class="mb-4">
                        <label for="nombre" class="form-label">Nombre comercial</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" minlength="3" required>
                            <div class="invalid-feedback">
                                El nombre debe tener al menos 3 caracteres.
                            </div>
                        </div>
                        <div class="mb-4">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" placeholder="Marca" minlength="3" required>
                            <div class="invalid-feedback">
                                La marca debe tener al menos 3 carácteres.
                            </div>
                        </div>
                        <div class="mb-4">
                        <label for="enfermedad" class="form-label">Enfermedad</label>
                        <input type="text" class="form-control" id="enfermedad" name="enfermedad" placeholder="Enfermedad" minlength="3" required>
                            <div class="invalid-feedback">
                                La enfermedad debe tener al menos 3 carácteres.
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="viaAplic" class="form-label">Via de aplicación</label>
                            <select id="selectViaAplicacion" name="viaAplicacion" class="form-control">
                                <!-- Las opciones se agregarán aquí con JavaScript -->
                            </select>
                            <div class="invalid-feedback">
                                Seleccione una opción
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnAgregarVacuna">Finalizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal editar Vacuna -->
    <div class="modal fade" id="editarVacuna" tabindex="-1" aria-labelledby="editVacunaModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editVacunaModal">Editar Vacuna</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarVacunaForm" class="needs-validation" novalidate>
                        <div class="mb-4">
                        <label for="nombre" class="form-label">Nombre comercial</label>
                        <input type="text" class="form-control" id="editarNombre" name="nombre" placeholder="Nombre" minlength="3" required>
                            <div class="invalid-feedback">
                                El nombre debe tener al menos 3 caracteres.
                            </div>
                        </div>
                        <div class="mb-4">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="editarMarca" name="marca" placeholder="Marca" minlength="3" required>
                            <div class="invalid-feedback">
                                La marca debe tener al menos 3 carácteres.
                            </div>
                        </div>
                        <div class="mb-4">
                        <label for="enfermedad" class="form-label">Enfermedad</label>
                        <input type="text" class="form-control" id="editarEnfermedad" name="enfermedad" placeholder="Enfermedad" minlength="3" required>
                            <div class="invalid-feedback">
                                La enfermedad debe tener al menos 3 carácteres.
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="viaAplic" class="form-label">Via de aplicación</label>
                            <select id="editarSelectViaAplicacion" name="viaAplicacion" class="form-control">
                                <!-- Las opciones se agregarán aquí con JavaScript -->
                            </select>
                            <div class="invalid-feedback">
                                Seleccione una opción
                            </div>
                        </div>
                        <input type="hidden" id="editarIdVacuna" name="idVacuna">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnEditarVacuna">Finalizar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
<!------------------------------------------------->
<!--------- Sección JavaScript de Vacunas -------->
<!-------------------------------------------------> 
<!------- RELLENAR TABLA DE VACUNAS - AJAX -------->
<!-------------------------------------------------> 
function cargarTablaVacunas() {
    //Vaciar la tabla
    if ($.fn.DataTable.isDataTable('#tablaVacunas')) {
        $('#tablaVacunas').DataTable().destroy();
    }
    var tablaVacunasTbody = document.getElementById("vacunas");
    tablaVacunasTbody.innerHTML = '';

    // Realizar la solicitud AJAX
    fetch('index.php?opt=vacunas&ajax=getVacunas')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Recorrer los datos y crear las filas de la tabla
        data.forEach(vacuna => {
            var row = document.createElement("tr");
            row.className = "table-light";
            row.setAttribute('data-id', vacuna.idVacuna);
            row.innerHTML = 
               '<td>' + vacuna.idVacuna + '</td>' +
               '<td>' + vacuna.nombre + '</td>' +
               '<td>' + vacuna.via + '</td>' +
               '<td>' + vacuna.marca + '</td>' +
               '<td>' + vacuna.enfermedad + '</td>' +
               '<td>' +
               '<button type="button" ' +
                    'class="btn btn-warning btn-sm" ' +
                    'data-bs-toggle="modal" ' +
                    'data-bs-target="#editarVacuna" ' +
                    'data-id="' + vacuna.idVacuna + '" ' +
                    'data-nombre="' + vacuna.nombre + '" ' +
                    'data-idViaAp="' + vacuna.idViaAplicacion + '" ' +
                    'data-marca="' + vacuna.marca + '" ' +
                    'data-enfermedad="' + vacuna.enfermedad + '">' +
                    'Editar' +
                '</button>' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarVacuna(' + vacuna.idVacuna + ')">Borrar</button>' +
            '</td>';
            tablaVacunasTbody.appendChild(row);
        });
        $('#tablaVacunas').DataTable();
    })
    .catch(error => {
        console.error('Error al cargar vacunas:', error);
        $('#tablaVacunas').DataTable();
    });
}
<!--------------------------------------------> 
<!-- Vacunas - CAPTAR EL FORMULARIO AGREGAR -->  
<!--------------------------------------------> 
document.getElementById('btnAgregarVacuna').addEventListener('click', function() {
    agregarVacuna();
});
document.getElementById('agregarVacunaForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    agregarVacuna();
});
<!-----------------------------------------------> 
<!-- GRANJAS - CAPTAR EL FORMULARIO DE EDICIÓN -->  
<!-----------------------------------------------> 
document.getElementById('btnEditarVacuna').addEventListener('click', function() {
   editarVacuna();
});
document.getElementById('editarVacunaForm').addEventListener('submit', function(event) {
   event.preventDefault(); // Prevent the default form submission
   editarVacuna();
});
<!----------------------------------------> 
<!---------- VACUNAS - ELIMINAR ---------->  
<!----------------------------------------> 
function eliminarVacuna(idVacuna) {
    // Realizar la solicitud AJAX
    fetch('index.php?opt=vacunas&ajax=delVacuna&idVacuna=' + idVacuna, {
        method: 'GET'
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarTablaVacunas();
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        showToastError('Error desconocido.');
    });
}
<!-------------------------------------------> 
<!--------- VACUNAS - AGREGAR NUEVA --------->  
<!-------------------------------------------> 
function agregarVacuna() {
    const form = document.getElementById('agregarVacunaForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const nombre = document.getElementById('nombre').value;
    const marca = document.getElementById('marca').value;
    const enfermedad = document.getElementById('enfermedad').value;
    const viaAplicacion = document.getElementById('selectViaAplicacion').value;

    fetch('index.php?opt=vacunas&ajax=addVacuna', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombre=' + encodeURIComponent(nombre) +
              '&marca=' + encodeURIComponent(marca) +
              '&enfermedad=' + encodeURIComponent(enfermedad) +
              '&viaAplicacion=' + encodeURIComponent(viaAplicacion)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarTablaVacunas();
                $('#agregarVacuna').modal('hide');
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        showToastError('Error en la solicitud AJAX: ' + error.message);
    });
}
<!-------------------------------------------> 
<!----------- VACUNAS - EDITAR   ------------>
<!-------------------------------------------> 
function editarVacuna() {
    const form = document.getElementById('editarVacunaForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const idVacuna = document.getElementById('editarIdVacuna').value;
    const nombre = document.getElementById('editarNombre').value;
    const marca = document.getElementById('editarMarca').value;
    const enfermedad = document.getElementById('editarEnfermedad').value;
    const viaAplicacion = document.getElementById('editarSelectViaAplicacion').value;

    fetch('index.php?opt=vacunas&ajax=editVacuna', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idVacuna=' + encodeURIComponent(idVacuna) +
              '&nombre=' + encodeURIComponent(nombre) +
              '&marca=' + encodeURIComponent(marca) +
              '&enfermedad=' + encodeURIComponent(enfermedad) +
              '&viaAplicacion=' + encodeURIComponent(viaAplicacion)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarTablaVacunas();
                $('#editarVacuna').modal('hide');
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        showToastError('Error en la solicitud AJAX: ' + error.message);
    });
}
<!-------------------------------------------> 
<!--------- VACUNAS - CARGAR SELECT --------->
<!-------------------------------------------> 
function cargarSelectViaAplicacion(select) {
    //Iniciar tabla, cargar opción por default.
    const selectViaAplicacion = document.getElementById(select);
    selectViaAplicacion.innerHTML = '';
    const defaultOption = document.createElement('option');
        defaultOption.text = 'Seleccione la vía de aplicación';
        defaultOption.value = '';
        selectViaAplicacion.appendChild(defaultOption);

    fetch('index.php?opt=vacunas&ajax=getViasAplicacion')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        data.forEach(viaApp => {
            const optionAgregar = document.createElement('option');
            optionAgregar.value = viaApp.idViaAplicacion;
            optionAgregar.text = viaApp.via;
            selectViaAplicacion.appendChild(optionAgregar);
        });
    })
    .catch(error => {
        console.error('Error al cargar vias de aplicacion:', error);
        showToastError('Error al cargar ias de aplicacion');
    });
}
<!-------------------------------------------------> 
<!-------- RELLENAR FORM EDICION VACUNAS ---------->
<!-------------------------------------------------> 
document.getElementById("editarVacuna").addEventListener("show.bs.modal", function (event) {
    // Botón que activó el modal
    const button = event.relatedTarget;
    // Extraer datos del atributo data-*
    const idVacuna = button.getAttribute("data-id");
    const nombre = button.getAttribute("data-nombre");
    const idViaAplicacion = button.getAttribute("data-idViaAp");
    const marca = button.getAttribute("data-marca");
    const enfermedad = button.getAttribute("data-enfermedad");

    // Asignar los valores a los campos del formulario
    document.querySelector("#editarVacunaForm #editarNombre").value = nombre;
    document.querySelector("#editarVacunaForm #editarMarca").value = marca;
    document.querySelector("#editarVacunaForm #editarEnfermedad").value = enfermedad;
    document.querySelector("#editarVacunaForm #editarSelectViaAplicacion").value = idViaAplicacion;
    document.querySelector('#editarVacunaForm #editarIdVacuna').value = idVacuna;

    // Selecciona la opción correcta en el select
    const opcionesEditar = document.querySelector('#editarVacunaForm #editarSelectViaAplicacion');
    opcionesEditar.value = idViaAplicacion; // Selecciona el valor correcto
    if (opcionesEditar.value !== idViaAplicacion) {
        // Si el valor no está presente, agrega la opción faltante
        const nuevaOpcion = document.createElement('option');
        nuevaOpcion.value = idViaAplicacion;
        nuevaOpcion.text = 'Opción desconocida';
        opcionesEditar.appendChild(nuevaOpcion);
        opcionesEditar.value = idViaAplicacion;
    }
    // Asigna el ID de la granja (oculto)
    
});

window.addEventListener('load', function() {
    cargarTablaVacunas();
    cargarSelectViaAplicacion('selectViaAplicacion');
    cargarSelectViaAplicacion('editarSelectViaAplicacion');

    // Agregar click a las filas de vacunas para redirigir a los lotes
    const tbodyVacunas = document.getElementById('vacunas');
    tbodyVacunas.addEventListener('click', function(event) {
        // Encontrar el <tr> correspondiente
        let tr = event.target.closest('tr');
        if (!tr) return;

        const idVacuna = tr.getAttribute('data-id');
        if (!idVacuna) return;

        // Preguntar si quiere cargar los lotes
        const respuesta = confirm('¿Desea ver los lotes de esta vacuna?');
        if (respuesta) {
            window.location.href = 'index.php?opt=lotesVacunas&idVacuna=' + idVacuna;
        }
    });
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => { //identificacion capacidad idTipoAve idGranja
    initFormValidator("editarVacunaForm", {
        nombre: (value) => {
            if (value.length <= 0) return "Ingrese el nombre comercial.";
            return true;
        },
        marca: (value) => {
            if (value.length <= 0) return "Ingrese una marca válida.";
            return true;
        },
        enfermedad: (value) => {
            if (value.length <= 0) return "Ingrese una enfermedad válida.";
            return true;
        },
        viaAplicacion : (value) => {
            if (!value) return "Debe seleccionar una vía.";
            return true;
        }
    });
    initFormValidator("agregarVacunaForm", {
        nombre: (value) => {
            if (value.length <= 0) return "Ingrese el nombre comercial.";
            return true;
        },
        marca: (value) => {
            if (value.length <= 0) return "Ingrese una marca válida.";
            return true;
        },
        enfermedad: (value) => {
            if (value.length <= 0) return "Ingrese una enfermedad válida.";
            return true;
        },
        viaAplicacion : (value) => {
            if (!value) return "Debe seleccionar una vía.";
            return true;
        }
    });
});
</script>
HTML;
// Agregar las funciones y el contenedor de los toast
// Para mostrar notificaciones
include 'view/toast.php';
$body .= $toast;
?>