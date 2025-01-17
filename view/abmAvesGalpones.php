<?php
$idLoteAves = isset($_GET['idLoteAves']) ? (int)$_GET['idLoteAves'] : 0;
$body = <<<HTML
<div class="container">
    <h1>Registrar cambio de ubicación</h1>

    <!-- Seleccionar Lote -->
    <div class="input-group mb-3">
        <select id="selectLote" name="selectLote" class="form-select rounded-start" style="width:70%" required>
            <!-- opciones cargadas por JS -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#modalCambioUbicacion">
            Registrar Cambio
        </button>
        <div class="invalid-feedback">Debe elegir un lote.</div>
    </div>

    <!-- Card con datos del lote seleccionado -->
    <div id="cardLote" class="card mb-4 d-none">
        <div class="card-body">
            <h5 class="card-title">Datos del Lote</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Galpón Actual:</strong> <span id="datoGalponActual"></span></li>
                <li class="list-group-item"><strong>Granja:</strong> <span id="datoGranjaActual"></span></li>
                <li class="list-group-item"><strong>Fecha Inicio:</strong> <span id="datoFechaInicio"></span></li>
            </ul>
        </div>
    </div>

    <!-- Tabla de cambios de ubicación -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body table-responsive">
            <table id="tablaUbicaciones" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Galpón</th>
                        <th class="text-primary">Fecha Inicio</th>
                        <th class="text-primary">Fecha Fin</th>
                    </tr>
                </thead>
                <tbody id="ubicacionesLote"></tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Registrar Cambio de Ubicación -->
<div class="modal fade" id="modalCambioUbicacion" tabindex="-1" aria-labelledby="modalCambioUbicacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalCambioUbicacionLabel">Registrar Cambio de Ubicación</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formCambioUbicacion" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="selectGalpon" class="form-label">Galpón</label>
                        <select id="selectGalpon" class="form-select" name="selectGalpon" style="width:100%" required>
                            <!-- opciones cargadas por JS -->
                        </select>
                        <div class="invalid-feedback">Seleccione un galpón.</div>
                    </div>
                    <div class="mb-4">
                        <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                        <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <input type="hidden" id="idLoteSeleccionado" name="idLoteAves">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarUbicacion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
var idLote = $idLoteAves;

// Cargar lotes
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

// Cargar galpones
function cargarGalpones() {
    return fetch('index.php?opt=galpones&ajax=getAllGalpones')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectGalpon');
        select.empty();
        select.append('<option value="">Seleccione un galpón...</option>');
        data.forEach(g => {
            select.append(new Option(g.identificacion + ' - ' + g.nombre, g.idGalpon));
        });

        // Inicializa select2 después de llenar las opciones
        select.select2({
            theme: 'bootstrap-5',
            placeholder: "Seleccione un galpón...",
            allowClear: false,
            dropdownParent: $('#modalCambioUbicacion'), // Esto es clave para modales
            width: '100%'
        });
    })
    .catch(err => console.error('Error cargando galpones:', err));
}

// Cargar datos de ubicación del lote
function cargarUbicaciones(idLote) {
    fetch('index.php?opt=lotesAves&ajax=getUbicacionAve&idLoteAves=' + idLote)
    .then(r => r.json())
    .then(data => {
        var tbody = document.getElementById('ubicacionesLote');
        tbody.innerHTML = '';
        if (data.length > 0) {
            data.forEach(u => {
                var row = document.createElement('tr');
                row.innerHTML = 
                    '<td>' + u.idGalpon_loteAve + '</td>' +
                    '<td>' + u.galponIdentificacion + '</td>' +
                    '<td>' + u.fechaInicio + '</td>' +
                    '<td>' + (u.fechaFin ?? '') + '</td>';
                tbody.appendChild(row);
            });
            var last = data[data.length - 1];
            document.getElementById('datoGalponActual').textContent = last.galponIdentificacion;
            document.getElementById('datoFechaInicio').textContent = last.fechaInicio;
            document.getElementById('datoGranjaActual').textContent = last.nombreGranja;
            document.getElementById('cardLote').classList.remove('d-none');
        } else {
            document.getElementById('cardLote').classList.add('d-none');
        }
    })
    .catch(err => console.error('Error cargando ubicaciones:', err));
}

// Guardar cambio de ubicación
document.getElementById('btnGuardarUbicacion').addEventListener('click', function() {
    const idLoteSel = $('#idLoteSeleccionado').val();
    const idGalpon = $('#selectGalpon').val();
    const fechaInicio = $('#fechaInicio').val();
    if (!idLoteSel || !idGalpon || !fechaInicio) {
        showToastError('Complete todos los campos.');
        return;
    }
    const form = document.getElementById('formCambioUbicacion');
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    fetch('index.php?opt=lotesAves&ajax=editUbicacionAve', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idLoteAves=' + encodeURIComponent(idLoteSel) +
              '&idGalpon=' + encodeURIComponent(idGalpon) +
              '&fechaInicio=' + encodeURIComponent(fechaInicio)
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalCambioUbicacion').modal('hide');
            cargarUbicaciones(idLoteSel);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
});

// Inicialización
window.addEventListener('load', function() {
    cargarLotes().then(() => cargarUbicaciones(idLote));
    cargarGalpones();

    $(document).ready(function() {
        $('#selectLote').select2({ theme: 'bootstrap-5', placeholder: "Seleccione un lote...", allowClear: false, width: 'resolve' });
        $('#selectGalpon').select2({ theme: 'bootstrap-5', placeholder: "Seleccione un galpón...", allowClear: false, width: 'resolve' });
    });

    $('#selectLote').on('change', function() {
        const idLote = $(this).val();
        $('#idLoteSeleccionado').val(idLote);
        if (idLote) {
            cargarUbicaciones(idLote);
        } else {
            $('#cardLote').hide();
        }
    });

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fechaInicio').value = today;
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("formCambioUbicacion", {
        selectGalpon: (value) => {
            if (!value) return "Debe seleccionar un galpón.";
            return true;
        },
        fechaInicio: (value, field) => {
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
        idLoteSeleccionado: (value) => {
            if (!value) return "Debe seleccionar un lote.";
            return true;
        }
    });
});
</script>
HTML;

include 'view/toast.php';
$body .= $toast;
?>
