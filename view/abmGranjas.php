<?php
$body = <<<HTML
<div class="container">
    <h1>Granjas</h1>

    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarGranja">
          Agregar nueva granja
        </button>
    </div>

    <!-- Tabla de granjas -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body table-responsive">
            <table id="tablaGranjas" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Nombre</th>
                        <th class="text-primary">SENASA Nº</th>
                        <th class="text-primary">m²</th>
                        <th class="text-primary">Ubicación</th> 
                        <th class="text-primary">✏</th>
                        <th class="text-primary">❌</th>
                    </tr>
                </thead>
                <tbody id="granjas">
                    <!-- Los datos se insertarán aquí -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal popUp Agregar Granja -->
<div class="modal fade" id="agregarGranja" tabindex="-1" aria-labelledby="agregarGranjaModal" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="agregarGranjaModal">Agregar Granja</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <form id="agregarGranjaForm" class="needs-validation" novalidate>
    <div class="mb-4">
        <label for="nombre" class="form-label">Nombre de la granja</label>
        <input type="text" 
               class="form-control" 
               id="nombre" 
               name="nombre" 
               placeholder="Nombre"
               minlength="3"
               required>
        <div class="invalid-feedback">
            El nombre debe tener al menos 3 caracteres.
        </div>
    </div>
    <div class="mb-4">
        <label for="metrosCuadrados" class="form-label">Metros Cuadrados</label>
        <input type="number" 
               class="form-control" 
               id="metrosCuadrados" 
               name="metrosCuadrados" 
               placeholder="Tamaño de la granja"
               min="1"
               required>
        <div class="invalid-feedback">
            El valor debe ser un número positivo.
        </div>
    </div>
    <div class="mb-4">
        <label for="agregrarGranjaFormTextSenasa" class="form-label">Número de habilitación de SENASA</label>
            <input type="text" 
                 class="form-control" 
                 id="habilitacion" 
                 name="habilitacion" 
                 placeholder="SENASA N°"
                 required>
        <div class="invalid-feedback">
            La habilitación debe tener al menos 3 caracteres.
        </div>
     </div>
    <div class="mb-4">
        <label for="ubicacion" class="form-label">Ubicación</label>
        <input type="text" 
               class="form-control" 
               id="ubicacion" 
               name="ubicacion" 
               placeholder="Localidad"
               minlength="3"
               required>
        <div class="invalid-feedback">
            La ubicación debe tener al menos 3 caracteres.
        </div>
    </div>
</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnAgregarGranja">Agregar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal popUp editar Granja -->
<div class="modal fade" id="editarGranja" tabindex="-1" aria-labelledby="editarGranjaModal" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editarGranjaModal">Editar datos de la Granja</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editarGranjaForm" class="needs-validation" novalidate>
            <div class="mb-4">
                <label for="editarNombre" class="form-label">Nombre de la granja</label>
                <input type="text" 
                       class="form-control" 
                       id="editarNombre" 
                       name="nombre" 
                       placeholder="Nombre"
                       minlength="3"
                       required>
                <div class="invalid-feedback">
                    Nombre inválido (mínimo 3 caracteres)
                </div>
            </div>
            <div class="mb-4">
                <label for="editarHabilitacion" class="form-label">Número de habilitación de SENASA</label>
                <input type="text" 
                       class="form-control" 
                       id="editarHabilitacion" 
                       name="habilitacion" 
                       placeholder="SENASA N°"
                       minlength="3"
                       required>
                <div class="invalid-feedback">
                    Nombre inválido (mínimo 3 caracteres)
                </div>
            </div>
            <div class="mb-4">
                <label for="editarMetros" class="form-label">Metros Cuadrados</label>
                <input type="number" 
                       class="form-control" 
                       id="editarMetros" 
                       name="metrosCuadrados" 
                       placeholder="Tamaño de la granja"
                       min="1" 
                       required>
                <div class="invalid-feedback">
                    Debe ser un número positivo
                </div>
            </div>
            <div class="mb-4">
                <label for="editarUbicacion" class="form-label">Ubicación</label>
                <input type="text" 
                       class="form-control" 
                       id="editarUbicacion" 
                       name="ubicacion" 
                       placeholder="Localidad"
                       minlength="3"
                       required>
                <div class="invalid-feedback">
                    Nombre inválido (mínimo 3 caracteres)
                </div>
            </div>
            <input type="hidden" id="editarIdGranja" name="idGranja">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnEditarGranja">Finalizar</button>
      </div>
  </div>
  </div>
</div>

<script>
<!------------------------------------------------->
<!--- Sección JavaScript de Granjas -->
<!-------------------------------------------------> 
<!------- RELLENAR TABLA DE GRANJAS - AJAX -------->
<!-------------------------------------------------> 
function cargarTablaGranjas() {
    //Vaciar la tabla
    if ($.fn.DataTable.isDataTable('#tablaGranjas')) {
        $('#tablaGranjas').DataTable().destroy();
    }
    var tablaGranjasTbody = document.getElementById("granjas");
    tablaGranjasTbody.innerHTML = '';

    // Realizar la solicitud AJAX
    fetch('index.php?opt=granjas&ajax=getGranjas')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Recorrer los datos y crear las filas de la tabla
        data.forEach(granja => {
            var row = document.createElement("tr");
            row.className = "table-light";
            row.innerHTML = 
                '<td>' + granja.idGranja + '</td>' +
                '<td>' + granja.nombre + '</td>' +
                '<td>' + granja.habilitacionSenasa + '</td>' +
                '<td>' + granja.metrosCuadrados + '</td>' +
                '<td>' + granja.ubicacion + '</td>' +
                '<td>' +
                    '<button type="button" ' +
                            'class="btn btn-warning btn-sm" ' +
                            'data-bs-toggle="modal" ' +
                            'data-bs-target="#editarGranja" ' +
                            'data-id="' + granja.idGranja + '" ' +
                            'data-nombre="' + granja.nombre + '" ' +
                            'data-habilitacion="' + granja.habilitacionSenasa + '" ' +
                            'data-metros="' + granja.metrosCuadrados + '" ' +
                            'data-ubicacion="' + granja.ubicacion + '">' +
                        'Editar' +
                    '</button>' +
                '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarGranja(' + granja.idGranja + ')">Borrar</button>' +
                '</td>';
            tablaGranjasTbody.appendChild(row);
        })
        $('#tablaGranjas').DataTable();
    })
    .catch(error => {
        console.error('Error al cargar granjas:', error);
        $('#tablaGranjas').DataTable();
    });
}
<!--------------------------------------------> 
<!-- GRANJAS - CAPTAR EL FORMULARIO AGREGAR -->  
<!--------------------------------------------> 
document.getElementById('btnAgregarGranja').addEventListener('click', function() {
    agregarGranja();
});
document.getElementById('agregarGranjaForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    agregarGranja();
});
<!-----------------------------------------------> 
<!-- GRANJAS - CAPTAR EL FORMULARIO DE EDICIÓN -->  
<!-----------------------------------------------> 
document.getElementById('btnEditarGranja').addEventListener('click', function() {
   editarGranja();
});
document.getElementById('editarGranjaForm').addEventListener('submit', function(event) {
   event.preventDefault(); // Prevent the default form submission
   editarGranja();
});
<!-------------------------------------------------> 
<!------- RELLENAR FORMULARIO DE EDICION   -------->
<!-------------------------------------------------> 
document.getElementById("editarGranja").addEventListener("show.bs.modal", function (event) {
    // Botón que activó el modal
    const button = event.relatedTarget;

    // Extraer datos del atributo data-*
    const idGranja = button.getAttribute("data-id");
    const nombre = button.getAttribute("data-nombre");
    const habilitacion = button.getAttribute("data-habilitacion");
    const metros = button.getAttribute("data-metros");
    const ubicacion = button.getAttribute("data-ubicacion");

    // Asignar los valores a los campos del formulario
    document.querySelector("#editarGranjaForm #editarNombre").value = nombre;
    document.querySelector("#editarGranjaForm #editarHabilitacion").value = habilitacion;
    document.querySelector("#editarGranjaForm #editarMetros").value = metros;
    document.querySelector("#editarGranjaForm #editarUbicacion").value = ubicacion;
    document.querySelector("#editarGranjaForm #editarIdGranja").value = idGranja;
});
<!-----------------------------------------------> 
<!---------- GRANJAS - ELIMINAR ---------->  
<!-----------------------------------------------> 
function eliminarGranja(idGranja) {
    // Realizar la solicitud AJAX
    fetch('index.php?opt=granjas&ajax=delGranja&idGranja=' + idGranja, {
        method: 'GET'
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Si la eliminación fue exitosa, recargar la tabla y los select
                cargarTablaGranjas();
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
<!-----------------------------------------------> 
<!---------- GRANJAS - AGREGAR NUEVA ---------->  
<!-----------------------------------------------> 
function agregarGranja() {
    const form = document.getElementById('agregarGranjaForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const nombre = document.getElementById('nombre').value;
    const metrosCuadrados = document.getElementById('metrosCuadrados').value;
    const habilitacion = document.getElementById('habilitacion').value;
    const ubicacion = document.getElementById('ubicacion').value;

    fetch('index.php?opt=granjas&ajax=addGranja', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombre=' + encodeURIComponent(nombre) +
              '&metrosCuadrados=' + encodeURIComponent(metrosCuadrados) +
              '&habilitacion=' + encodeURIComponent(habilitacion) +
              '&ubicacion=' + encodeURIComponent(ubicacion)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarTablaGranjas();
                $('#agregarGranja').modal('hide');
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
<!-------------------------------------------------> 
<!------------ GRANJAS - EDITAR   ------------->
<!-------------------------------------------------> 
function editarGranja() {
    const form = document.getElementById('editarGranjaForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const idGranja = document.getElementById('editarIdGranja').value;
    const nombre = document.getElementById('editarNombre').value;
    const metrosCuadrados = document.getElementById('editarMetros').value;
    const habilitacion = document.getElementById('editarHabilitacion').value;
    const ubicacion = document.getElementById('editarUbicacion').value;

    fetch('index.php?opt=granjas&ajax=editGranja', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idGranja=' + encodeURIComponent(idGranja) +
              '&nombre=' + encodeURIComponent(nombre) +
              '&metrosCuadrados=' + encodeURIComponent(metrosCuadrados) +
              '&habilitacion=' + encodeURIComponent(habilitacion) +
              '&ubicacion=' + encodeURIComponent(ubicacion)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarTablaGranjas();
                $('#editarGranja').modal('hide');
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
<!-------------------------------------------------> 
<!---  FUNCIONES A EJECUTAR AL CARGAR LA PÁGINA --->
<!-------------------------------------------------> 
window.addEventListener('load', function() {
        cargarTablaGranjas()
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => { //identificacion capacidad idTipoAve idGranja
    initFormValidator("agregarGranjaForm", {
        nombre : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        metrosCuadrados : (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        habilitacion : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        ubicacion : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        }
    });
    initFormValidator("editarGranjaForm", {
        nombre : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        metrosCuadrados : (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        habilitacion  : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        ubicacion : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
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