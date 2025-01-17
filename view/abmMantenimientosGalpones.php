<?php
//Carga la parte común de la pagina
require_once 'view/abmMantenimientos.php';
$body .= <<<HTML
<div class="container">
    <h2> Galpones</h2>

    <div class="input-group">
        <select id="selectGalpon" name="selectGalpon" class="form-select rounded-start" required>
            <!-- Las opciones se agregan con JavaScript -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#newMantGalpon">
            Agregar mantenimiento
        </button>
        <div class="invalid-feedback">
            Debe elegir una opción.
        </div>
    </div>

    <!-- Filtros de fechas + botones -->
    <div class="row mb-3 g-2">
        <div class="col-12 col-md-3">
            <label for="fechaDesdeGalpon" class="form-label">Desde:</label>
            <input type="date" id="fechaDesdeGalpon" class="form-control">
        </div>
        <div class="col-12 col-md-3">
            <label for="fechaHastaGalpon" class="form-label">Hasta:</label>
            <input type="date" id="fechaHastaGalpon" class="form-control">
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button id="btnFiltrarGalpon" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button id="btnReporteGalpon" class="btn btn-success w-100">Generar Reporte</button>
        </div>
    </div>

    <!-- Tabla de mantenimiento de galpones -->
    <div class="card shadow-sm rounded-3 mb-3">
        <div class="card-body table-responsive">
            <table id="tablaMantGalpon" class="table table-striped table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Fecha</th>
                        <th class="text-primary">Mantenimiento</th>
                        <th class="text-primary">❌</th>
                    </tr>
                </thead>
                <tbody id="mantGalpon">
                    <!-- Los datos se insertarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal agregar Mantenimiento Galpon -->
<!-- TO DO: SI NO SE FILTRA UN GALPON ANTES, DA ERROR EL SQL -->
<div class="modal fade" id="newMantGalpon" tabindex="-1" aria-labelledby="newMantGalponModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="newMantGalponModal">Agregar mantenimiento realizado</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newMantGalponForm" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="fechaMantGalpon" class="form-label">Fecha y hora de realización</label>
                        <input type="datetime-local" class="form-control" 
                            id="fechaMantGalpon" name="fechaMantenimiento"
                            required>
                        <div class="invalid-feedback">
                            Seleccione una fecha y hora válidos.
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="tipoMant" class="form-label">Tipo de mantenimiento</label>
                        <select id="selectTipoMantGalpon" name="tipoMantenimiento" class="form-control">
                            <!-- Las opciones se agregarán aquí con JavaScript -->
                        </select>
                        <div class="invalid-feedback">
                            Seleccione un tipo de mantenimiento.
                        </div>
                    </div>
                    <input type="hidden" id="idGalpon" name="idGalpon">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnAgregarMantGalpon" >Finalizar</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
<!-------------------------------------------------->
<!-- Sección JavaScript - Mantenimiento de Galpon -->
<!--------------------------------------------------> 
<!------ CARGAR GALPONES DISPONIBLES EN SELECT -----> 
<!-------------------------------------------------->
function cargarSelectGalpon() {
    //Iniciar tabla, cargar opción por default.
    const selectFiltrarGalpon = document.getElementById('selectGalpon');
    selectFiltrarGalpon.innerHTML = '';
    const defaultOption = document.createElement('option');
        defaultOption.text = 'Seleccione un galpón';
        defaultOption.value = '';
        selectFiltrarGalpon.appendChild(defaultOption);

    fetch('index.php?opt=galpones&ajax=getAllGalpones')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {

        data.forEach(galpones => {
            const optionAgregar = document.createElement('option');
            optionAgregar.value = galpones.idGalpon;
            optionAgregar.text = galpones.identificacion + " - " + galpones.nombre;
            selectFiltrarGalpon.appendChild(optionAgregar);
        });

        // Si hay un valor previamente seleccionado, restaurarlo y cargar los galpones
        const previouslySelected = selectFiltrarGalpon.getAttribute('data-selected');
        if (previouslySelected) {
            selectFiltrarGalpon.value = previouslySelected;
            cargarTablaGalpones();
        }
    })
    .catch(error => {
        console.error('Error al cargar galpones:', error);
        showToastError('Error al cargar galpones');
    });
}
<!-------------------------------------------------> 
<!------ RELLENAR TABLA MANT. GALPON - AJAX ------->
<!-------------------------------------------------> 
function cargarTablaMantGalpon() {
    $('#tablaMantGalpon').DataTable();
}
<!-- Listado Galpones - eliminar datos de la tabla al presionar el select -->
document.getElementById('selectGalpon').addEventListener('change', function(e) {
    if ($.fn.DataTable.isDataTable('#tablaMantGalpon')) {
        $('#tablaMantGalpon').DataTable().clear().draw();
    }
});
<!----------------------------------------------------->
<!-----------------------------------------------------> 
<!--------- MANT. GALPON - FORMULARIO AGREGAR --------->  
<!-----------------------------------------------------> 
<!--- Pasar al formulario el ID Galpon seleccionado --->  
<!------- y presentar error si no hay seleccion ------->  
document.getElementById("newMantGalpon").addEventListener("show.bs.modal", function (event) {
    // Get the currently selected Galpon ID
    const selectedGalponId = document.getElementById('selectGalpon').value;
    if (!selectedGalponId) {
        event.preventDefault();
        showToastError('Debe seleccionar un galpón primero');
        return;
    }
    // Set the hidden input value
    document.querySelector("#newMantGalponForm #idGalpon").value = selectedGalponId;;
    cargarSelectTipoMant('selectTipoMantGalpon');
});
<!---- Cambiar la acción del botón enviar y enter ---->  
document.getElementById('btnAgregarMantGalpon').addEventListener('click', function() {
    agregarMantGalpon();
});
document.getElementById('newMantGalponForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    agregarMantGalpon();
});
<!-----------------------------------------------> 
<!-------- MANT. Galpon - AGREGAR NUEVO --------->  
<!-----------------------------------------------> 
function agregarMantGalpon() {
    const form = document.getElementById('newMantGalponForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const fechaMant = document.getElementById('fechaMantGalpon').value;
    const tipoMantenimiento = document.getElementById('selectTipoMantGalpon').value;
    const idGalpon = document.getElementById('idGalpon').value;

    fetch('index.php?opt=mantenimientos&ajax=newMantGalpon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'fechaMant=' + encodeURIComponent(fechaMant) +
              '&tipoMantenimiento=' + encodeURIComponent(tipoMantenimiento) +
              '&idGalpon=' + encodeURIComponent(idGalpon)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                document.getElementById("btnFiltrarGalpon").click();
                $('#newMantGalpon').modal('hide');
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
<!-----------------------------------------------> 
<!----------- MANT. Galpon - ELIMINAR ----------->  
<!-----------------------------------------------> 
function eliminarMantGalpon(idMantenimientoGalpon) {
    // Realizar la solicitud AJAX
    fetch('index.php?opt=mantenimientos&ajax=delMantGalpon&idMantenimientoGalpon=' + idMantenimientoGalpon, {
        method: 'GET'
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Si la eliminación fue exitosa, recargar la tabla y los select
                document.getElementById("btnFiltrarGalpon").click();
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
// === FILTRAR POR FECHAS - GALPONES ===
document.getElementById("btnFiltrarGalpon").addEventListener("click", function() {
    var idGalpon = document.getElementById("selectGalpon").value;
    var desde = document.getElementById("fechaDesdeGalpon").value;
    var hasta = document.getElementById("fechaHastaGalpon").value;

    if (!idGalpon) {
        showToastError("Debe seleccionar un galpón primero");
        return;
    }
    if (!desde || !hasta) {
        showToastError("Debe seleccionar fechas Desde y Hasta");
        return;
    }

    // Vaciar y recargar la tabla con las fechas
    if ($.fn.DataTable.isDataTable('#tablaMantGalpon')) {
        $('#tablaMantGalpon').DataTable().destroy();
    }
    document.getElementById("mantGalpon").innerHTML = "";

    fetch('index.php?opt=mantenimientos&ajax=getMantGalpon&idGalpon=' + encodeURIComponent(idGalpon) +
          '&desde=' + encodeURIComponent(desde) +
          '&hasta=' + encodeURIComponent(hasta))
    .then(function(res) { return res.json(); })
    .then(function(data) {
        data.forEach(function(m) {
            var row = '<tr class="table-light">' +
                        '<td>' + m.idMantenimientoGalpon + '</td>' +
                        '<td>' + m.fecha + '</td>' +
                        '<td>' + m.nombre + '</td>' +
                        '<td>' +
                            '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarMantGalpon(' + m.idMantenimientoGalpon + ')">Borrar</button>' +
                        '</td>' +
                      '</tr>';
            document.getElementById("mantGalpon").insertAdjacentHTML("beforeend", row);
        });
        $('#tablaMantGalpon').DataTable();
    })
    .catch(function(err) {
        console.error("Error:", err);
        $('#tablaMantGalpon').DataTable();
    });
});

// === GENERAR REPORTE IMPRIMIBLE - GALPONES ===
document.getElementById("btnReporteGalpon").addEventListener("click", function() {
    var desde = document.getElementById("fechaDesdeGalpon").value;
    var hasta = document.getElementById("fechaHastaGalpon").value;
    var galponNombre = document.querySelector("#selectGalpon option:checked").text;

    if (!desde || !hasta) {
        showToastError("Debe seleccionar fechas Desde y Hasta");
        return;
    }

    var rows = "";
    document.querySelectorAll("#mantGalpon tr").forEach(function(tr) {
        var tds = tr.querySelectorAll("td");
        rows += '<tr>' +
                    '<td>' + tds[0].innerText + '</td>' +
                    '<td>' + tds[1].innerText + '</td>' +
                    '<td>' + tds[2].innerText + '</td>' +
                '</tr>';
    });

    var reporte = '<html>' +
                   '<head>' +
                   '<title>Reporte Mantenimientos</title>' +
                   '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">' +
                   '<style>' +
                     'body { padding: 20px; }' +
                     'h2, h4 { text-align: center; margin-bottom: 20px; }' +
                     'table { width: 100%; border-collapse: collapse; margin-top: 20px; }' +
                     'th, td { border: 1px solid #000; padding: 8px; text-align: left; }' +
                   '</style>' +
                   '</head>' +
                   '<body>' +
                   '<h2>' + galponNombre + '</h2>' +
                   '<h4>Listado de mantenimientos de galpón</h4>' +
                   '<p><strong>Desde:</strong> ' + desde + ' &nbsp;&nbsp; <strong>Hasta:</strong> ' + hasta + '</p>' +
                   '<table>' +
                   '<thead>' +
                     '<tr><th>ID</th><th>Fecha</th><th>Descripción</th></tr>' +
                   '</thead>' +
                   '<tbody>' + rows + '</tbody>' +
                   '</table>' +
                   '</body>' +
                   '</html>';

    var ventana = window.open("", "_blank");
    ventana.document.write(reporte);
    ventana.document.close();
    ventana.print();
});

window.addEventListener('load', function() {
    cargarTablaTipoMant();
    cargarSelectGalpon();
    cargarTablaMantGalpon()
    const fechaHasta = new Date();
    const fechaDesde = new Date();
    fechaDesde.setMonth(fechaHasta.getMonth() - 1);

    function formatDate(d) {
        return d.toISOString().split('T')[0]; // yyyy-mm-dd
    }
    document.getElementById('fechaDesdeGalpon').value = formatDate(fechaDesde);
    document.getElementById('fechaHastaGalpon').value = formatDate(fechaHasta);

    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return year+'-'+month+'-'+day+'T'+hours+':'+minutes;
    }
    const now = new Date();
    document.getElementById('fechaMantGalpon').value = formatDateForInput(now);

});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => { //identificacion capacidad idTipoAve idGranja
    initFormValidator("newMantGalponForm", {
    fechaMantenimiento : (value) => {
        if (!value) return "Debe ingresar una fecha.";

        // Tomar solo la parte de la fecha (YYYY-MM-DD)
        const soloFecha = value.split("T")[0];
        const [year, month, day] = soloFecha.split("-").map(Number);
        const fecha = new Date(year, month - 1, day);

        if (isNaN(fecha.getTime())) return "Fecha inválida.";

        const hoy = new Date();
        hoy.setHours(0,0,0,0);
        fecha.setHours(0,0,0,0);

        if (fecha > hoy) return "La fecha no puede ser futura.";
        return true;
    },
        tipoMantenimiento : (value) => {
            if (!value) return "Debe seleccionar un tipo.";
            return true;
        }
    });
});
</script>
HTML;
?>

