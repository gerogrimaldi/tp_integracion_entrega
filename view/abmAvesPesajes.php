<?php
$idLoteAves = isset($_GET['idLoteAves']) ? (int)$_GET['idLoteAves'] : 0;
$body = <<<HTML
<div class="container">
    <h1>Registro de Pesajes</h1>

    <!-- Seleccionar Lote -->
    <div class="input-group mb-3">
        <select id="selectLote" name="selectLote" class="form-select rounded-start" style="width:70%" required>
            <!-- opciones cargadas por JS (Select2) -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#modalPesaje">
            Registrar Pesaje
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

    <!-- Tabla de registros de pesaje -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body table-responsive">
            <table id="tablaPesaje" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Fecha</th>
                        <th class="text-primary">Peso (kg)</th>
                        <th class="text-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody id="pesajesAves"></tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Registrar Pesaje -->
<div class="modal fade" id="modalPesaje" tabindex="-1" aria-labelledby="modalPesajeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalPesajeLabel">Registrar Pesaje</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formPesaje" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="fechaPesaje" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaPesaje" name="fecha" required>
                        <div class="invalid-feedback">Seleccione una fecha válida.</div>
                    </div>
                    <div class="mb-4">
                        <label for="pesoPesaje" class="form-label">Peso (kg)</label>
                        <input type="number" class="form-control" id="pesoPesaje" name="peso" step="0.01" min="0" required>
                        <div class="invalid-feedback">Ingrese un peso válido.</div>
                    </div>
                    <input type="hidden" id="idLoteSeleccionado" name="idLoteAves">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarPesaje">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Pesaje -->
<div class="modal fade" id="modalEditarPesaje" tabindex="-1" aria-labelledby="modalEditarPesajeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEditarPesajeLabel">Editar Pesaje</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPesaje" class="needs-validation" novalidate>
                    <input type="hidden" id="editIdPesaje" name="idPesaje">
                    <div class="mb-4">
                        <label for="editFechaPesaje" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="editFechaPesaje" name="fecha" required>
                        <div class="invalid-feedback">Seleccione una fecha válida.</div>
                    </div>
                    <div class="mb-4">
                        <label for="editPesoPesaje" class="form-label">Peso (kg)</label>
                        <input type="number" class="form-control" id="editPesoPesaje" name="peso" step="0.01" min="0" required>
                        <div class="invalid-feedback">Ingrese un peso válido.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnActualizarPesaje">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<script>
var idLote = $idLoteAves;

// Listeners botones
document.getElementById('btnGuardarPesaje').addEventListener('click', function() {
    const form = document.getElementById('formPesaje');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    agregarPesaje();
});
document.getElementById('btnActualizarPesaje').addEventListener('click', function() {
    const form = document.getElementById('formEditarPesaje');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    editarPesaje();
});

// Cambiar lote -> cargar datos + pesajes
document.getElementById('selectLote').addEventListener('change', function () {
    const idLote = this.value;
    if (idLote) {
        document.getElementById('idLoteSeleccionado').value = idLote;
        cargarDatosLote(idLote);
        cargarPesajes(idLote);
    }
});

// Rellenar modal de edición
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.btn-edit')) {
        const id = event.target.getAttribute('data-id');
        const fecha = event.target.getAttribute('data-fecha');
        const peso = event.target.getAttribute('data-peso');

        document.getElementById('editIdPesaje').value = id;
        document.getElementById('editFechaPesaje').value = fecha;
        document.getElementById('editPesoPesaje').value = peso;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarPesaje'));
        modal.show();
    }
});

// Funciones AJAX
function cargarLotes() {
    return fetch('index.php?opt=lotesAves&ajax=getAllLotesAves')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectLote');
        select.empty();
        select.append('<option value="">Seleccione un lote...</option>');
        data.forEach(function(l){
            var isSelected = (l.idLoteAves == idLote) ? true : false;
            var opcion = new Option(l.identificador, l.idLoteAves, isSelected, isSelected);
            select.append(opcion);
        });
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

function cargarPesajes(idLote) {
    if ($.fn.DataTable.isDataTable('#tablaPesaje')) {
        $('#tablaPesaje').DataTable().destroy();
    }
    var tbody = document.getElementById("pesajesAves");
    tbody.innerHTML = '';

    fetch('index.php?opt=lotesAves&ajax=getPesaje&idLoteAves=' + idLote)
    .then(response => response.json())
    .then(data => {
        data.forEach(p => {
            var row = document.createElement("tr");
            row.innerHTML =
                '<td>' + p.idPesaje + '</td>' +
                '<td>' + p.fecha + '</td>' +
                '<td>' + p.peso + '</td>' +
                '<td>' +
                    '<button class="btn btn-sm btn-warning btn-edit" ' +
                        'data-id="' + p.idPesaje + '" ' +
                        'data-fecha="' + p.fecha + '" ' +
                        'data-peso="' + p.peso + '">' +
                        'Editar' +
                    '</button> ' +
                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + p.idPesaje + '">' +
                        'Eliminar' +
                    '</button>' +
                '</td>';
            tbody.appendChild(row);
        });
        $('#tablaPesaje').DataTable();
    })
    .catch(err => console.error('Error al cargar pesajes:', err));
}

// Eliminar
document.addEventListener('click', function(event) {
    if (event.target && event.target.matches('.btn-delete')) {
        const idPesaje = event.target.getAttribute('data-id');
        fetch('index.php?opt=lotesAves&ajax=delPesaje&idPesaje=' + idPesaje, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(r => r.json().then(data => {
            if (r.ok) {
                showToastOkay(data.msg);
                cargarPesajes(idLote);
                cargarDatosLote(idLote);
            } else {
                showToastError(data.msg);
            }
        }))
        .catch(err => showToastError('Error en AJAX: ' + err.message));
    }
});

// Agregar
function agregarPesaje() {
    const idLote = document.getElementById('idLoteSeleccionado').value;
    const fecha = document.getElementById('fechaPesaje').value;
    const peso = document.getElementById('pesoPesaje').value;

    fetch('index.php?opt=lotesAves&ajax=addPesaje', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idLoteAves=' + encodeURIComponent(idLote) +
              '&fecha=' + encodeURIComponent(fecha) +
              '&peso=' + encodeURIComponent(peso)
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalPesaje').modal('hide');
            cargarPesajes(idLote);
            cargarDatosLote(idLote);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
}

// Editar
function editarPesaje() {
    const idPesaje = document.getElementById('editIdPesaje').value;
    const fecha = document.getElementById('editFechaPesaje').value;
    const peso = document.getElementById('editPesoPesaje').value;

    fetch('index.php?opt=lotesAves&ajax=editPesaje', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idPesaje=' + encodeURIComponent(idPesaje) +
              '&fecha=' + encodeURIComponent(fecha) +
              '&peso=' + encodeURIComponent(peso)
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalEditarPesaje').modal('hide');
            cargarPesajes(idLote);
            cargarDatosLote(idLote);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
}

// Inicialización
window.addEventListener('load', function() {
    cargarDatosLote(idLote);
    cargarPesajes(idLote);

    $(document).ready(function() {
        $('#selectLote').select2({
            theme: 'bootstrap-5',
            placeholder: "Seleccione un lote...",
            allowClear: false,
            width: 'resolve'
        });
        cargarLotes();
    });

    $('#selectLote').on('change', function() {
        const idLote = $(this).val();
        if (idLote) {
            $('#idLoteSeleccionado').val(idLote);
            cargarDatosLote(idLote);
            cargarPesajes(idLote);
            $('#cardLote').show();
        } else {
            $('#cardLote').hide();
        }
    });

    const today = new Date().toISOString().split('T')[0];
    $('#fechaPesaje').val(today);
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("formPesaje", { //fecha causa y cantidad
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
        peso: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        }});
    initFormValidator("formEditarPesaje", { //fecha causa y cantidad
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
        peso: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        }});
});
</script>
HTML;

include 'view/toast.php';
$body .= $toast;
?>
