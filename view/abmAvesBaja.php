<?php
$idLoteAves = isset($_GET['idLoteAves']) ? (int)$_GET['idLoteAves'] : -1;
$body = <<<HTML
<div class="container">
    <h1>Gestión de Bajas de Lotes</h1>

    <!-- Botón para registrar nueva baja -->
    <div class="mb-3">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalBaja">
            Registrar Baja
        </button>
    </div>

    <!-- Tabla de bajas -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body table-responsive">
            <table id="tablaBajas" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID Baja</th>
                        <th class="text-primary">Lote</th>
                        <th class="text-primary">Fecha Baja</th>
                        <th class="text-primary">Precio Venta</th>
                        <th class="text-primary">Motivo</th>
                        <th class="text-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyBajas"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar Baja -->
<div class="modal fade" id="modalBaja" tabindex="-1" aria-labelledby="modalBajaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalBajaLabel">Registrar Baja de Lote</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formBaja" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="selectLote" class="form-label">Lote</label>
                        <select id="selectLote" name="idLoteAves" class="form-select" required></select>
                        <div class="invalid-feedback">Seleccione un lote.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fechaBaja" class="form-label">Fecha de Baja</label>
                        <input type="date" id="fechaBaja" name="fechaBaja" class="form-control" required>
                        <div class="invalid-feedback">La fecha no es válida, no puede ser futura.</div>
                    </div>
                    <div class="mb-3">
                        <label for="precioVenta" class="form-label">Precio de Venta (si corresponde)</label>
                        <input type="number" step="0.01" id="precioVenta" name="precioVenta" class="form-control" value="0" required>
                        <div class="invalid-feedback">Ingrese un precio válido (0 o más).</div>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <input type="text" id="motivo" name="motivo" class="form-control" placeholder="Ejemplo: Venta a frigorífico">
                        <div class="invalid-feedback">Máximo 200 caracteres.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger" id="btnGuardarBaja">Guardar Baja</button>
            </div>
        </div>
    </div>
</div>

<script>
const lotePreseleccionado = $idLoteAves;
// === Cargar bajas ===
function cargarBajas() {
    if ($.fn.DataTable.isDataTable('#tablaBajas')) {
        $('#tablaBajas').DataTable().destroy();
    }
    var tablaBajasTbody = document.getElementById("tbodyBajas");
    tablaBajasTbody.innerHTML = '';
    fetch('index.php?opt=lotesAves&ajax=getBajas')
    .then(r => r.json())
    .then(data => {
        var tbody = document.getElementById('tbodyBajas');
        tbody.innerHTML = '';
        if (data.length > 0) {
            data.forEach(b => {
                var row = document.createElement('tr');
                row.innerHTML = 
                    '<td>' + b.idBajaLoteAves + '</td>' +
                    '<td>' + b.identificador + '</td>' +
                    '<td>' + b.fechaBaja + '</td>' +
                    '<td>' + b.precioVenta + '</td>' +
                    '<td>' + (b.motivo ?? '') + '</td>' +
                    '<td><button class="btn btn-sm btn-success" onclick="revertirBaja(' + b.idBajaLoteAves + ')">Revertir</button></td>';
                tbody.appendChild(row);
            });
            $('#tablaBajas').DataTable();
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay bajas registradas</td></tr>';
        }
    })
    .catch(err => console.error('Error cargando bajas:', err));
}

// === Cargar lotes activos (sin baja) ===
function cargarLotesActivos() {
    fetch('index.php?opt=lotesAves&ajax=getAllLotesAves')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectLote');
        select.empty();
        select.append('<option value="">Seleccione un lote...</option>');

        data.forEach(l => {
            var opcion = new Option(l.identificador, l.idLoteAves, false, false);
            select.append(opcion);
        });

        // Si hay un lote preseleccionado, marcarlo
        if (lotePreseleccionado != -1) {
            select.val(lotePreseleccionado).trigger('change'); 
        }
    })
    .catch(err => console.error('Error cargando lotes:', err));
}

// === Guardar nueva baja ===
document.getElementById('btnGuardarBaja').addEventListener('click', function() {
    const form = document.getElementById('formBaja');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const formData = new URLSearchParams(new FormData(form)).toString();
    fetch('index.php?opt=lotesAves&ajax=baja', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    })
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            $('#modalBaja').modal('hide');
            cargarBajas();
            cargarLotesActivos(); // refrescar select
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
});

// === Revertir baja ===
function revertirBaja(idBaja) {
    if (!confirm('¿Está seguro de revertir esta baja?')) return;
    fetch('index.php?opt=lotesAves&ajax=delBaja&idBajaLoteAves=' + idBaja)
    .then(r => r.json().then(data => {
        if (r.ok) {
            showToastOkay(data.msg);
            cargarBajas();
            cargarLotesActivos();
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(err => showToastError('Error en AJAX: ' + err.message));
}

// === Inicialización ===
window.addEventListener('load', function() {
    cargarBajas();
    cargarLotesActivos();

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fechaBaja').value = today;

    $('#selectLote').select2({ 
        theme: 'bootstrap-5', 
        placeholder: "Seleccione un lote...", 
        width: '100%' 
    });

    if (lotePreseleccionado != -1) {
        $('#modalBaja').modal('show');
    }
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("formBaja", {
        fechaBaja: (value, field) => {
            if (!value) return "Debe ingresar una fecha.";

            // Parseo manual YYYY-MM-DD para evitar desfase UTC
            const [year, month, day] = value.split("-").map(Number);
            const fecha = new Date(year, month - 1, day); // local time

            if (isNaN(fecha.getTime())) return "Fecha inválida.";

            const hoy = new Date();
            hoy.setHours(0,0,0,0);
            fecha.setHours(0,0,0,0);

            if (fecha > hoy) return "La fecha no puede ser futura.";
            return true;
        },
        precioVenta: (value) => {
            if (value < 0) return "Debe ser 0 o mayor.";
            return true;
        },
        motivo: (value) => {
            if (value.length > 200) return "Máximo 200 caracteres.";
            return true;
        }
    });
});
</script>

HTML;

include 'view/toast.php';
$body .= $toast;
?>
