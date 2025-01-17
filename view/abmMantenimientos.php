<?php
$body = <<<HTML
<div class="container">
    <h1>Mantenimientos</h1>
    
    <p class="d-inline-flex gap-1">
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#verMant" aria-expanded="false" aria-controls="collapseExample">
            Ver tipos de mantenimientos
        </button>
    </p>

    <div class="collapse mb-4" id="verMant">
        <!-- Removed d-inline-flex from this div -->
        <div class="mb-3">
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#agregarMant" aria-expanded="false" aria-controls="collapseExample">
                Agregar tipos de Mantenimientos
            </button>
        </div>
        
        <div class="collapse mb-4" id="agregarMant">
            <div class="card card-body text-dark">
                <form id="agregarTipoMantForm" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="nombreMant" class="form-label">Tipo de mantenimiento</label>
                        <input type="text" class="form-control" 
                            id="nombreMant" name="nombreMant"
                            placeholder="Ejemplo: Corte de césped"
                            min="1" required>
                        <div class="invalid-feedback">
                            Debe contar con al menos 3 carácteres.
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAgregarTipoMant">Agregar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de tipos de mantenimiento -->
        <div class="card shadow-sm rounded-3 mb-3">
            <div class="card-body table-responsive">
                <table id="tablaTiposMant" class="table table-striped table-hover align-middle mb-0 bg-white">
                    <thead class="table-light">
                        <tr>
                            <th class="text-primary">ID</th>
                            <th class="text-primary">Descripción</th>
                            <th class="text-primary">✏</th>
                            <th class="text-primary">❌</th>
                        </tr>
                    </thead>
                    <tbody id="tipoMant">
                        <!-- Los datos se insertarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------------> 
<!-- MODAL: EDITAR TIPO DE MANTENIMIENTO -->
<!------------------------------------------------> 
<div class="modal fade" id="editarTipoMant" tabindex="-1" aria-labelledby="editarTipoMantModal" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editarTipoMantModal">Editar descripción del mantenimiento</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarTipoMantForm" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="nombreMant" class="form-label">Tipo de mantenimiento</label>
                    <input type="text" class="form-control" 
                        id="nombreMantEdit" name="nombreMantEdit"
                        placeholder="Ejemplo: Corte de césped"
                        min="1" required>
                    <div class="invalid-feedback">
                        Debe contar con al menos 3 carácteres.
                    </div>
                </div>
                    <input type="hidden" id="idTipoMant" name="idTipoMant">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEditarTipoMant">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<script>
<!-------------------------------------------------->
<!-- Sección JavaScript de Tipos de Mantenimiento -->
<!--------------------------------------------------> 
<!--- TIPO MANTENIMIENTO - RELLENAR FORM EDICIÓN ---> 
<!--------------------------------------------------> 
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.btn-warning')) {
        const button = event.target;
        const idTipoMant = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        document.querySelector('#editarTipoMantForm #nombreMantEdit').value = nombre;
        document.querySelector('#editarTipoMantForm #idTipoMant').value = idTipoMant;
    }
});
<!--------------------------------------------------------> 
<!-- TIPO MANTENIMIENTOS - CAPTAR EL FORMULARIO AGREGAR -->  
<!--------------------------------------------------------> 
document.getElementById('btnAgregarTipoMant').addEventListener('click', function() {
    agregarTipoMant();
});
document.getElementById('agregarTipoMantForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    agregarTipoMant();
});
<!--------------------------------------------------------> 
<!-- TIPO MANTENIMIENTOS - ACCION FORMULARIO DE EDICIÓN -->  
<!--------------------------------------------------------> 
document.getElementById('btnEditarTipoMant').addEventListener('click', function() {
    editarTipoMant();
});
document.getElementById('editarTipoMantForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    editarTipoMant();
});
<!--------------------------------------------------------> 
<!------- TIPOS DE MANTENIMIENTOS - RESOLUCIÓN ABM ------->  
<!--------------------------------------------------------> 
function agregarTipoMant() {
    const form = document.getElementById('agregarTipoMantForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const nombreMant = document.getElementById('nombreMant').value;

    fetch('index.php?opt=mantenimientos&ajax=addTipoMant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombreMant=' + encodeURIComponent(nombreMant)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Recargar la tabla
                recargarTipoMant();
                // Cerrar el modal
                $('#agregarTipoMant').modal('hide');
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

function editarTipoMant() {
    const form = document.getElementById('editarTipoMantForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const idTipoMant = document.getElementById('idTipoMant').value;
    const nombreMantEdit = document.getElementById('nombreMantEdit').value;

    fetch('index.php?opt=mantenimientos&ajax=editTipoMant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idTipoMant=' + encodeURIComponent(idTipoMant) +
        '&nombreMantEdit=' + encodeURIComponent(nombreMantEdit)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                recargarTipoMant();
                showToastOkay(data.msg);
                $('#editarTipoMant').modal('hide');
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

function eliminarTipoMantenimiento(idTipoMant) {
    // Realizar la solicitud AJAX
    fetch('index.php?opt=mantenimientos&ajax=delTipoMant&idTipoMant=' + idTipoMant, {
        method: 'GET'
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Si la eliminación fue exitosa, recargar la tabla y los select
                recargarTipoMant();
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

<!-------------------------------------------------> 
<!----- TIPO DE MANTENIMIENTO - CARGAR SELECT ----->
<!-------------------------------------------------> 
function cargarSelectTipoMant(select) {
    //Cargar opción por default.
    const selectTipoMant = document.getElementById(select);
    selectTipoMant.innerHTML = '';
    const defaultOption = document.createElement('option');
        defaultOption.text = 'Seleccione el tipo de mantenimiento';
        defaultOption.value = '';
        selectTipoMant.appendChild(defaultOption);

    fetch('index.php?opt=mantenimientos&ajax=getTipoMant')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        data.forEach(tipoMant => {
            const optionAgregar = document.createElement('option');
            optionAgregar.value = tipoMant.idTipoMantenimiento;
            optionAgregar.text = tipoMant.nombre;
            selectTipoMant.appendChild(optionAgregar);
        });
    })
    .catch(error => {
        console.error('Error al cargar tipos de mantenimiento:', error);
        showToastError('Error al cargar tipos de mantenimiento');
    });
}

function recargarTipoMant() {
    //Recargar secciones de la página cuando se cambia un tipo mant.
    cargarTablaTipoMant();
    //cargarTablaMantGalpon();
    cargarTablaMantGranja()
}

<!-- JS Para rellenar tabla tipo mantenimientos -->
function cargarTablaTipoMant() {
    // Vaciar tabla
    if ($.fn.DataTable.isDataTable('#tablaTiposMant')) {
            $('#tablaTiposMant').DataTable().destroy();
    }
    var tipoMantTbody = document.getElementById("tipoMant");
    tipoMantTbody.innerHTML = '';

    // Realizar la solicitud AJAX
    fetch('index.php?opt=mantenimientos&ajax=getTipoMant')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Recorrer los datos y crear las filas de la tabla
        data.forEach(tipoMant => {
            var row = document.createElement("tr");
            row.className = "table-light";
            row.innerHTML = 
                '<td>' + tipoMant.idTipoMantenimiento + '</td>' +
                '<td>' + tipoMant.nombre + '</td>' +
                '<td>' +
                    '<button type="button" ' +
                        'class="btn btn-warning btn-sm" ' +
                        'data-bs-toggle="modal" ' +
                        'data-bs-target="#editarTipoMant" ' +
                        'data-id="' + tipoMant.idTipoMantenimiento + '" ' +
                        'data-nombre="' + tipoMant.nombre + '">' +
                        'Editar' +
                    '</button>' +
                '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarTipoMantenimiento(' + tipoMant.idTipoMantenimiento + ')">Borrar</button>' +
                '</td>'
            tipoMantTbody.appendChild(row);
        });
        $('#tablaTiposMant').DataTable();

    })
    .catch(error => {
        console.error('Error al cargar los tipos de mantenimiento:', error);
        $('#tablaTiposMant').DataTable();
    });
}
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("agregarTipoMantForm", {
        nombreMant : (value) => {
            if (value.length < 3) return "Ingrese los datos solicitados.";
            return true;
        }
    });
    initFormValidator("editarTipoMantForm", {
        nombreMantEdit : (value) => {
            if (value.length < 3) return "Ingrese los datos solicitados.";
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

