<?php
$idLoteAves = isset($_GET['idLoteAves']) ? (int)$_GET['idLoteAves'] : 0;
$body = <<<HTML
<div class="container">
    <h1>Registro de Mortandad</h1>

    <!-- Seleccionar Lote -->
    <div class="input-group mb-3">
        <select id="selectLote" name="selectLote" class="form-select rounded-start" style="width:70%" required>
            <!-- opciones cargadas por JS (Select2) -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#modalMortandad">
            Registrar Mortandad
        </button>
        <div class="invalid-feedback">Debe elegir un lote.</div>
    </div>

    <!-- Card con datos del lote seleccionado -->
    <div id="cardLote" class="card mb-4 d-none">
        <div class="card-body">
            <h5 class="card-title">Datos del Lote</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Último peso registrado (kg):</strong> <span id="datoUltimoPeso"></span></li>
                <li class="list-group-item"><strong>Cantidad de Aves Compradas:</strong> <span id="datoCantidadOriginal"></span></li>
                <li class="list-group-item"><strong>Cantidad Actual:</strong> <span id="datoCantidadActual"></span></li>
                <li class="list-group-item"><strong>Tipo de Ave:</strong> <span id="datoTipoAve"></span></li>
                <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> <span id="datoFechaNacimiento"></span></li>
                <li class="list-group-item"><strong>Fecha de Compra:</strong> <span id="datoFechaCompra"></span></li>
                <li class="list-group-item"><strong>Granja:</strong> <span id="datoGranja"></span></li>
                <li class="list-group-item"><strong>Galpón:</strong> <span id="datoGalpon"></span></li>
            </ul>
        </div>
    </div>

    <!-- Tabla de registros de mortandad -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <table id="tablaMortandad" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Fecha</th>
                        <th class="text-primary">Causa</th>
                        <th class="text-primary">Cantidad</th>
                        <th class="text-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody id="mortandadAves"></tbody>
            </table>
        </div>
    </div>
    
</div>

<!-- Modal Registrar Mortandad -->
<div class="modal fade" id="modalMortandad" tabindex="-1" aria-labelledby="modalMortandadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalMortandadLabel">Registrar Mortandad</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formMortandad" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="fechaMortandad" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaMortandad" name="fecha" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="causaMortandad" class="form-label">Causa</label>
                        <input type="text" class="form-control" id="causaMortandad" name="causa" maxlength="100" required>
                        <div class="invalid-feedback">Ingrese una causa válida.</div>
                    </div>
                    <div class="mb-4">
                        <label for="cantidadMortandad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="cantidadMortandad" name="cantidad" min="1" required>
                        <div class="invalid-feedback">La cantidad no puede ser 0, ni superar las aves vivas.</div>
                    </div>
                    <input type="hidden" id="idLoteSeleccionado" name="idLoteAves">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarMortandad">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Mortandad -->
<div class="modal fade" id="modalEditarMortandad" tabindex="-1" aria-labelledby="modalEditarMortandadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEditarMortandadLabel">Editar Mortandad</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarMortandad" class="needs-validation" novalidate>
                    <input type="hidden" id="editIdMortandad" name="idMortandad">
                    <div class="mb-4">
                        <label for="editFechaMortandad" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="editFechaMortandad" name="fecha" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="editCausaMortandad" class="form-label">Causa</label>
                        <input type="text" class="form-control" id="editCausaMortandad" name="causa" maxlength="100" required>
                        <div class="invalid-feedback">Ingrese una causa válida.</div>
                    </div>
                    <div class="mb-4">
                        <label for="editCantidadMortandad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="editCantidadMortandad" name="cantidad" min="1" required>
                        <div class="invalid-feedback">La cantidad no puede ser 0, ni superar las aves vivas.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnActualizarMortandad">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<script>
var idLote = $idLoteAves;
var cantidadEdicion = 0;
//------------------------------------------------
// Listeners de botones principales
//------------------------------------------------
document.getElementById('btnGuardarMortandad').addEventListener('click', function() {
    const form = document.getElementById('formMortandad');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    agregarMortandad();
});
document.getElementById('btnActualizarMortandad').addEventListener('click', function() {
    const form = document.getElementById('formEditarMortandad');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    editarMortandad();
});

//------------------------------------------------
// Cambiar lote -> cargar datos + mortandad
//------------------------------------------------
document.getElementById('selectLote').addEventListener('change', function () {
    const idLote = this.value;
    if (idLote) {
        document.getElementById('idLoteSeleccionado').value = idLote;
        cargarDatosLote(idLote);
        cargarMortandad(idLote);
    }
});

//------------------------------------------------
// Rellenar modal de edición
//------------------------------------------------
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.btn-edit')) {
        const id = event.target.getAttribute('data-id');
        const fecha = event.target.getAttribute('data-fecha');
        const causa = event.target.getAttribute('data-causa');
        const cantidad = event.target.getAttribute('data-cantidad');
        
        cantidadEdicion = event.target.getAttribute('data-cantidad'); //Esto se utiliza esto en la validacion solamente

        document.getElementById('editIdMortandad').value = id;
        document.getElementById('editFechaMortandad').value = fecha;
        document.getElementById('editCausaMortandad').value = causa;
        document.getElementById('editCantidadMortandad').value = cantidad;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarMortandad'));
        modal.show();
    }
});

//------------------------------------------------
// Funciones AJAX
//------------------------------------------------
function cargarLotes() {
    return fetch('index.php?opt=lotesAves&ajax=getAllLotesAves')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectLote');
        select.empty();
        select.append('<option value="">Seleccione un lote...</option>');
        data.forEach(function(l){
            // Si coincide con idLote, marcar como seleccionado
            var isSelected = (l.idLoteAves == idLote) ? true : false;
            var opcion = new Option(l.identificador, l.idLoteAves, isSelected, isSelected);
            select.append(opcion);
        });
        // Actualizar Select2
        select.trigger('change');
        return data;
    })
    .catch(err => console.error('Error cargando lotes:', err));
}

function cargarDatosLote(idLote) {
    fetch('index.php?opt=lotesAves&ajax=getLoteAvesById&idLoteAves=' + idLote)
    .then(r => r.json())
    .then(l => {
        document.getElementById('datoUltimoPeso').textContent = l.ultimoPeso ?? 'Sin registro';
        document.getElementById('datoCantidadOriginal').textContent = l.cantidadAves;
        document.getElementById('datoCantidadActual').textContent = l.cantidadActual ?? l.cantidadAves;
        document.getElementById('datoTipoAve').textContent = l.tipoAveNombre;
        document.getElementById('datoFechaNacimiento').textContent = l.fechaNacimiento;
        document.getElementById('datoFechaCompra').textContent = l.fechaCompra;
        document.getElementById('datoGranja').textContent = l.granjaNombre;
        document.getElementById('datoGalpon').textContent = l.galponIdentificacion;
        document.getElementById('cardLote').classList.remove('d-none');
    })
    .catch(err => console.error('Error cargando datos de lote:', err));
}

function cargarMortandad(idLote) {
    //Vaciar la tabla
    if ($.fn.DataTable.isDataTable('#tablaMortandad')) {
        $('#tablaMortandad').DataTable().destroy();
    }
    var tablaMortandadTbody = document.getElementById("mortandadAves");
    tablaMortandadTbody.innerHTML = '';

    fetch('index.php?opt=lotesAves&ajax=getMuertes&idLoteAves=' + idLote)
    .then(response => {
        if (!response.ok) throw new Error('Error en la solicitud: ' + response.statusText);
        return response.json();
    })
    .then(data => {
        data.forEach(m => {
            var row = document.createElement("tr");
            row.innerHTML =
                '<td>' + m.idMortandad + '</td>' +
                '<td>' + m.fecha + '</td>' +
                '<td>' + m.causa + '</td>' +
                '<td>' + m.cantidad + '</td>' +
                '<td>' +
                    '<button class="btn btn-sm btn-warning btn-edit" ' +
                        'data-id="' + m.idMortandad + '" ' +
                        'data-fecha="' + m.fecha + '" ' +
                        'data-causa="' + m.causa + '" ' +
                        'data-cantidad="' + m.cantidad + '">' +
                        'Editar' +
                    '</button> ' +
                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + m.idMortandad + '">' +
                        'Eliminar' +
                    '</button>' +
                '</td>';
            tablaMortandadTbody.appendChild(row);
        });

        $('#tablaMortandad').DataTable();
    })
    .catch(error => {
        console.error('Error al cargar mortandad:', error);
        $('#tablaMortandad').DataTable();
    });
}

//------------------------------------------------
// Listener para botón Eliminar
//------------------------------------------------
document.addEventListener('click', function(event) {
    if (event.target && event.target.matches('.btn-delete')) {
        const idMortandad = event.target.getAttribute('data-id');
        fetch('index.php?opt=lotesAves&ajax=delMuertes&idMortandad=' + idMortandad, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(r => r.json().then(data => {
            if (r.ok) {
                showToastOkay(data.msg);
                cargarMortandad(idLote); // refrescar tabla
                cargarDatosLote(idLote); // refrescar datos del lote
            } else {
                showToastError(data.msg);
            }
        }))
        .catch(err => showToastError('Error en AJAX: ' + err.message));
    }
});

function agregarMortandad() {
    const idLote = document.getElementById('idLoteSeleccionado').value;
    const fecha = document.getElementById('fechaMortandad').value;
    const causa = document.getElementById('causaMortandad').value;
    const cantidad = document.getElementById('cantidadMortandad').value;

    fetch('index.php?opt=lotesAves&ajax=addMuertes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idLoteAves=' + encodeURIComponent(idLote) +
              '&fecha=' + encodeURIComponent(fecha) +
              '&causa=' + encodeURIComponent(causa) +
              '&cantidad=' + encodeURIComponent(cantidad)
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalMortandad').modal('hide');
            cargarMortandad(idLote);
            cargarDatosLote(idLote);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
}

function editarMortandad() {
    const idMortandad = document.getElementById('editIdMortandad').value;
    const fecha = document.getElementById('editFechaMortandad').value;
    const causa = document.getElementById('editCausaMortandad').value;
    const cantidad = document.getElementById('editCantidadMortandad').value;

    fetch('index.php?opt=lotesAves&ajax=editMuertes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '&idMortandad=' + encodeURIComponent(idMortandad) +
              '&fecha=' + encodeURIComponent(fecha) +
              '&causa=' + encodeURIComponent(causa) +
              '&cantidad=' + encodeURIComponent(cantidad)
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalEditarMortandad').modal('hide');
            cargarMortandad(idLote);
            cargarDatosLote(idLote);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
}

window.addEventListener('load', function() {
    cargarDatosLote(idLote);
    cargarMortandad(idLote);

    $(document).ready(function() {
        // Inicializar Select2 primero
        $('#selectLote').select2({
            theme: 'bootstrap-5',
            placeholder: "Seleccione un lote...",
            allowClear: false,
            width: 'resolve'
        });
        // Luego cargar los lotes
        cargarLotes();
    });

    // Evento cuando cambia la selección
    $('#selectLote').on('change', function() {
        const idLote = $(this).val();
        if (idLote) {
            $('#idLoteSeleccionado').val(idLote);
            cargarDatosLote(idLote);
            cargarMortandad(idLote);
            $('#cardLote').show();
        } else {
            $('#cardLote').hide();
        }
    });

    // Configurar la fecha actual por defecto
    const today = new Date().toISOString().split('T')[0];
    $('#fechaMortandad').val(today);
});

</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("formMortandad", { //fecha causa y cantidad
        causa: (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        fecha: (value, field) => {
            if (!value) return "Debe ingresar una fecha.";
            // Parseo manual YYYY-MM-DD para evitar desfase UTC
            const [year, month, day] = value.split("-").map(Number);
            const fecha = new Date(year, month - 1, day);
            if (isNaN(fecha.getTime())) return "Fecha inválida.";
            const hoy = new Date();
            hoy.setHours(0,0,0,0);
            fecha.setHours(0,0,0,0);
            if (fecha > hoy) return "La fecha no puede ser futura.";
            return true;
        },
        cantidad: (value) => {
            const cantidadActual = parseInt(document.getElementById('datoCantidadActual').textContent, 10);
            if (value <= 0) return "Debe ser mayor a 0.";
            if (value > cantidadActual) return `No puede superar la cantidad de aves vivas.`;
            return true;
        }});
    initFormValidator("formEditarMortandad", { //fecha causa y cantidad
        causa: (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        fecha: (value, field) => {
            if (!value) return "Debe ingresar una fecha.";
            // Parseo manual YYYY-MM-DD para evitar desfase UTC
            const [year, month, day] = value.split("-").map(Number);
            const fecha = new Date(year, month - 1, day);
            if (isNaN(fecha.getTime())) return "Fecha inválida.";
            const hoy = new Date();
            hoy.setHours(0,0,0,0);
            fecha.setHours(0,0,0,0);
            if (fecha > hoy) return "La fecha no puede ser futura.";
            return true;
        },
        cantidad: (value) => {
            const cantidadActual = parseInt(document.getElementById('datoCantidadActual').textContent, 10);
            if (value <= 0) return "Debe ser mayor a 0.";
            if (value > (Number(cantidadEdicion) + Number(cantidadActual))) return `No puede superar la cantidad de aves vivas.`;
            return true;
        }});
});
</script>
HTML;

include 'view/toast.php';
$body .= $toast;
?>
