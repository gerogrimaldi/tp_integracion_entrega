<?php
$idLoteAves = isset($_GET['idLoteAves']) ? (int)$_GET['idLoteAves'] : 0;
$body = <<<HTML
<div class="container">
    <h1>Aplicación de Vacunas</h1>

    <!-- Seleccionar Lote de Aves -->
    <div class="input-group mb-3">
        <select id="selectLote" name="selectLote" class="form-select rounded-start" style="width:70%" required>
            <!-- opciones cargadas por JS (Select2) -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#modalAplicarVacuna">
            Aplicar Vacuna
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
            <div class="mt-3 d-flex justify-content-end">
                <button id="btnReporteVacunas" class="btn btn-success">Generar Reporte</button>
            </div>
            </ul>
        </div>
    </div>

    <!-- Tabla de aplicaciones de vacunas -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-body table-responsive">
            <table id="tablaVacunas" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Vacuna</th>
                        <th class="text-primary">Lote</th>
                        <th class="text-primary">Fecha</th>
                        <th class="text-primary">Cantidad</th>
                        <th class="text-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody id="aplicacionesVacunas"></tbody>
            </table>
        </div>        
    </div>
</div>

<!-- Modal Aplicar Vacuna -->
<div class="modal fade" id="modalAplicarVacuna" tabindex="-1" aria-labelledby="modalAplicarVacunaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalAplicarVacunaLabel">Aplicar Vacuna</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formAplicarVacuna" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="selectVacunaModal" class="form-label">Vacuna</label>
                        <select id="selectVacunaModal" class="form-select" required>
                            <option value="">Seleccione una vacuna...</option>
                        </select>
                        <div class="invalid-feedback">Seleccione una vacuna.</div>
                    </div>
                    <div class="mb-3">
                        <label for="selectLoteVacunaModal" class="form-label">Lote de Vacuna</label>
                        <select id="selectLoteVacunaModal" class="form-select" disabled required>
                            <option value="">Seleccione una vacuna primero...</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un lote de vacuna.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fechaVacuna" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaVacuna" name="fecha" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-3">
                        <label for="cantidadVacuna" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="cantidadVacuna" name="cantidad" min="1" required>
                        <div class="invalid-feedback">Ingrese una cantidad válida.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarVacuna">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
var idLoteAves = $idLoteAves;
var cantidadActualLoteVacuna = 0; // Variable para almacenar la cantidad disponible del lote de vacuna

//------------------------------------------------
// Cargar Lotes de Aves en select principal
//------------------------------------------------
function cargarLotes() {
    return fetch('index.php?opt=lotesAves&ajax=getAllLotesAves')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectLote');
        select.empty().append('<option value="">Seleccione un lote...</option>');
        data.forEach(function(l) {
            var isSelected = (l.idLoteAves == idLoteAves);
            select.append(new Option(l.identificador, l.idLoteAves, isSelected, isSelected));
        });
        select.trigger('change');
    });
}

//------------------------------------------------
// Cargar datos del lote
//------------------------------------------------
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
        $('#cardLote').removeClass('d-none');
    });
}

//------------------------------------------------
// Cargar tabla de aplicaciones de vacunas
//------------------------------------------------
function cargarAplicacionesVacunas(idLote) {
    if ($.fn.DataTable.isDataTable('#tablaVacunas')) {
            $('#tablaVacunas').DataTable().destroy();
    }
    var tablaVacunasTbody = document.getElementById("aplicacionesVacunas");
    tablaVacunasTbody.innerHTML = '';
    fetch('index.php?opt=lotesAves&ajax=getVacunas&idLoteAves=' + idLote)
    .then(r => r.json())
    .then(data => {
        var tbody = $('#aplicacionesVacunas');
        tbody.empty();
        data.forEach(v => {
            var row = '<tr>' +
                '<td>' + v.idloteVacuna_loteAve + '</td>' +
                '<td>' + v.vacunaNombre + '</td>' +
                '<td>' + v.numeroLote + '</td>' +
                '<td>' + v.fecha + '</td>' +
                '<td>' + v.cantidad + '</td>' +
                '<td>' +
                    //'<button class="btn btn-sm btn-warning btn-edit" data-id="' + v.idloteVacuna_loteAve + '">Editar</button> ' +
                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + v.idloteVacuna_loteAve + '">Eliminar</button>' +
                '</td>' +
                '</tr>';
            tbody.append(row);
        });
        $('#tablaVacunas').DataTable();
    });
}

// Listener delegado para eliminar aplicación de vacuna
$(document).on('click', '.btn-delete', function () {
    const idAplicacion = $(this).data('id');
    fetch('index.php?opt=lotesAves&ajax=delVacuna&idAplicacion=' + idAplicacion, {
        method: 'DELETE'
    })
    .then(r => r.json())
    .then(data => {
        if (data.msg) showToastOkay(data.msg);
        // recargar tabla
        cargarAplicacionesVacunas($('#selectLote').val());
    })
    .catch(err => {
        console.error(err);
        showToastError('Error al eliminar');
    });
});

//------------------------------------------------
// Generar reporte imprimible de vacunas con datos exactos de la card
//------------------------------------------------
$(document).on('click', '#btnReporteVacunas', function() {
    var loteNombre = $('#selectLote option:selected').text().trim();
    if (!loteNombre) {
        showToastError("Debe seleccionar un lote primero");
        return;
    }
    var ultimoPeso = (document.getElementById('datoUltimoPeso') && document.getElementById('datoUltimoPeso').textContent) ? document.getElementById('datoUltimoPeso').textContent.trim() : '';
    var cantidadOriginal = (document.getElementById('datoCantidadOriginal') && document.getElementById('datoCantidadOriginal').textContent) ? document.getElementById('datoCantidadOriginal').textContent.trim() : '';
    var cantidadActual = (document.getElementById('datoCantidadActual') && document.getElementById('datoCantidadActual').textContent) ? document.getElementById('datoCantidadActual').textContent.trim() : '';
    var tipoAve = (document.getElementById('datoTipoAve') && document.getElementById('datoTipoAve').textContent) ? document.getElementById('datoTipoAve').textContent.trim() : '';
    var fechaNacimiento = (document.getElementById('datoFechaNacimiento') && document.getElementById('datoFechaNacimiento').textContent) ? document.getElementById('datoFechaNacimiento').textContent.trim() : '';
    var fechaCompra = (document.getElementById('datoFechaCompra') && document.getElementById('datoFechaCompra').textContent) ? document.getElementById('datoFechaCompra').textContent.trim() : '';
    var granja = (document.getElementById('datoGranja') && document.getElementById('datoGranja').textContent) ? document.getElementById('datoGranja').textContent.trim() : '';
    var galpon = (document.getElementById('datoGalpon') && document.getElementById('datoGalpon').textContent) ? document.getElementById('datoGalpon').textContent.trim() : '';

    // Construir filas de la tabla de aplicaciones desde el tbody actual
    var rows = "";
    document.querySelectorAll("#aplicacionesVacunas tr").forEach(function(tr) {
        var tds = tr.querySelectorAll("td");
        if (tds.length >= 5) { // asegurar que sea una fila data
            rows += "<tr>"
                + "<td>" + tds[0].innerText.trim() + "</td>"
                + "<td>" + tds[1].innerText.trim() + "</td>"
                + "<td>" + tds[2].innerText.trim() + "</td>"
                + "<td>" + tds[3].innerText.trim() + "</td>"
                + "<td>" + tds[4].innerText.trim() + "</td>"
                + "</tr>";
        }
    });

    if (rows === "") {
        rows = "<tr><td colspan='5' style='text-align:center'>No hay aplicaciones registradas</td></tr>";
    }

    var reporte = ""
    + "<html>"
    + "<head>"
    + "  <meta charset='utf-8'>"
    + "  <title>Reporte Aplicación de Vacunas - " + loteNombre + "</title>"
    + "  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>"
    + "  <style>"
    + "    body { padding: 18px; font-size: 13px; color: #000; }"
    + "    h2, h4 { text-align: center; margin: 6px 0 14px 0; }"
    + "    .ficha { width:100%; max-width:900px; margin: 0 auto 12px auto; border-collapse: collapse; }"
    + "    .ficha th, .ficha td { padding:6px 8px; vertical-align: top; }"
    + "    .ficha th { text-align: left; width: 220px; }"
    + "    table.data { width:100%; border-collapse: collapse; margin-top: 12px; }"
    + "    table.data th, table.data td { border: 1px solid #000; padding:6px 8px; text-align:left; }"
    + "  </style>"
    + "</head>"
    + "<body>"
    + "  <h2>Reporte de Aplicaciones de Vacunas</h2>"
    + "  <h4>" + loteNombre + "</h4>"

    // Ficha con los datos exactos de la card (2 columnas)
    + "  <table class='ficha'>"
    + "    <tr><th>Último peso registrado (kg):</th><td>" + (ultimoPeso || "-") + "</td><th>Tipo de Ave:</th><td>" + (tipoAve || "-") + "</td></tr>"
    + "    <tr><th>Cantidad de Aves Compradas:</th><td>" + (cantidadOriginal || "-") + "</td><th>Cantidad Actual:</th><td>" + (cantidadActual || "-") + "</td></tr>"
    + "    <tr><th>Fecha de Nacimiento:</th><td>" + (fechaNacimiento || "-") + "</td><th>Fecha de Compra:</th><td>" + (fechaCompra || "-") + "</td></tr>"
    + "    <tr><th>Granja:</th><td>" + (granja || "-") + "</td><th>Galpón:</th><td>" + (galpon || "-") + "</td></tr>"
    + "  </table>"

    // Tabla de aplicaciones
    + "  <table class='data'>"
    + "    <thead>"
    + "      <tr><th>ID</th><th>Vacuna</th><th>Lote</th><th>Fecha</th><th>Cantidad</th></tr>"
    + "    </thead>"
    + "    <tbody>" + rows + "</tbody>"
    + "  </table>"
    + "</body>"
    + "</html>";

    var ventana = window.open("", "_blank");
    ventana.document.open();
    ventana.document.write(reporte);
    ventana.document.close();
    ventana.focus();
    ventana.print();
});

//------------------------------------------------
// Inicialización Select2 y eventos
//------------------------------------------------
$(document).ready(function() {
    $('#selectLote').select2({ theme: 'bootstrap-5', placeholder: "Seleccione un lote...", width: 'resolve' });
    $('#selectVacunaModal').select2({ 
        theme: 'bootstrap-5', 
        placeholder: "Seleccione una vacuna...", 
        width: '100%',
        dropdownParent: $('#modalAplicarVacuna')
    });

    $('#selectLoteVacunaModal').select2({ 
        theme: 'bootstrap-5', 
        placeholder: "Seleccione un lote...", 
        width: '100%',
        dropdownParent: $('#modalAplicarVacuna')
    });
    cargarLotes();

    $('#selectLote').on('change', function() {
        const idLote = $(this).val();
        if (idLote) {
            cargarDatosLote(idLote);
            cargarAplicacionesVacunas(idLote);
            $('#cardLote').show();
        } else {
            $('#cardLote').hide();
        }
    });

    // Fecha por defecto
    const today = new Date().toISOString().split('T')[0];
    $('#fechaVacuna').val(today);


    //A partir de aca los Listener de los modales y los botones
    //------------------------------------------------
    // Abrir modal aplicar vacuna -> cargar vacunas
    //------------------------------------------------
    $('#modalAplicarVacuna').on('show.bs.modal', function () {
        const idLote = $('#selectLote').val();
        if (!idLote) {
            showToastError('Debe seleccionar un lote primero');
            return;
        }

        // Limpiar selects
        $('#selectVacunaModal').empty().append('<option value="">Seleccione una vacuna...</option>');
        $('#selectLoteVacunaModal').empty().append('<option value="">Seleccione una vacuna primero...</option>').prop('disabled', true);

        // Cargar vacunas
        fetch('index.php?opt=vacunas&ajax=getVacunas')
        .then(r => r.json())
        .then(vacunas => {
            vacunas.forEach(v => $('#selectVacunaModal').append(new Option(v.nombre, v.idVacuna)));
            $('#selectVacunaModal').trigger('change');
        });
    });

    //------------------------------------------------
    // Selección de vacuna -> habilitar lotes
    //------------------------------------------------
    $('#selectVacunaModal').on('change', function() {
        const idVacuna = $(this).val();
        const selectLote = $('#selectLoteVacunaModal');
        selectLote.empty().append('<option value="">Seleccione un lote...</option>');
        
        // Resetear la cantidad disponible
        cantidadActualLoteVacuna = 0;
        $('#mensajeDisponible').text('');
        
        if (idVacuna) {
            fetch('index.php?opt=vacunas&ajax=getLotesVacuna&idVacuna=' + idVacuna)
            .then(r => r.json())
            .then(lotes => {
                lotes.forEach(l => {
                    // Crear option con data attribute para la cantidad disponible
                    const option = new Option(
                         l.numeroLote + ' (Disponible: ' + l.cantidadDisponible +')', 
                        l.idLoteVacuna
                    );
                    // Agregar data attribute con la cantidad disponible
                    $(option).data('cantidad-disponible', l.cantidadDisponible);
                    selectLote.append(option);
                });
                selectLote.prop('disabled', false).trigger('change');
            });
        } else {
            selectLote.prop('disabled', true);
        }
    });
    //------------------------------------------------
    // Selección de lote de vacuna -> capturar cantidad disponible
    //------------------------------------------------
    $('#selectLoteVacunaModal').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        cantidadActualLoteVacuna = selectedOption.data('cantidad-disponible') || 0;
        // Actualizar mensaje informativo
        $('#mensajeDisponible').text('Cantidad disponible: '+ cantidadActualLoteVacuna);
        // Establecer el máximo permitido en el input de cantidad
        $('#cantidadVacuna').attr('max', cantidadActualLoteVacuna);
    });
    //------------------------------------------------
    // Guardar aplicación de vacuna
    //------------------------------------------------
    $('#btnGuardarVacuna').on('click', function() {
        const form = document.getElementById('formAplicarVacuna');
        // ejecutar las validaciones
        if (!form.validateAll()) {
            form.classList.add('was-validated');
            return;
        }
        const idLoteAves = $('#selectLote').val(); 
        const idLoteVacuna = $('#selectLoteVacunaModal').val(); 
        const fecha = $('#fechaVacuna').val();
        const cantidad = $('#cantidadVacuna').val();

        if (!idLoteAves || !idLoteVacuna || !fecha || !cantidad) {
            showToastError('Complete todos los campos');
            return;
        }

        fetch('index.php?opt=lotesAves&ajax=addVacuna', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'idLoteAves=' + encodeURIComponent(idLoteAves) +
                '&idLoteVacuna=' + encodeURIComponent(idLoteVacuna) +
                '&fecha=' + encodeURIComponent(fecha) +
                '&cantidad=' + encodeURIComponent(cantidad)
        })
        .then(r => r.json())
        .then(data => {
            if (data.msg) showToastOkay(data.msg);

            // Cerrar modal correctamente en Bootstrap 5
            var modalVacuna = bootstrap.Modal.getInstance(document.getElementById('modalAplicarVacuna'));
            modalVacuna.hide();

            // Recargar tabla con el lote correcto
            cargarAplicacionesVacunas(idLoteAves);
            
        });
    });
});
</script>
<script src="js/formValidator.js"></script>
<script>
initFormValidator("formAplicarVacuna", {
    selectVacunaModal: (value) => {
        if (!value) return "Seleccione una vacuna.";
        return true;
    },
    selectLoteVacunaModal: (value) => {
        if (!value) return "Seleccione un lote de vacuna.";
        return true;
    },
    fecha: (value) => {
        if (!value) return "Debe ingresar una fecha.";
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
        if (value <= 0) return "Debe ser mayor a 0.";
        // Validar contra la cantidad disponible del lote de vacuna
        if (value > Number(cantidadActualLoteVacuna)) return 'No puede superar la cantidad disponible ('+cantidadActualLoteVacuna+').';
        return true;
    }
});
</script>
HTML;

include 'view/toast.php';
$body .= $toast;
?>
