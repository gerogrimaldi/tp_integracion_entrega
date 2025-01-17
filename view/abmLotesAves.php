<?php
$body = <<<HTML
<div class="container">
    <h1>Lotes de aves</h1>
    
    <p class="d-inline-flex gap-1">
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#verTipoAve" aria-expanded="false" aria-controls="collapseExample">
            Ver tipos de aves
        </button>
    </p>

    <div class="collapse mb-4" id="verTipoAve">
        <div class="mb-3">
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#agregarTipoAve" aria-expanded="false" aria-controls="collapseExample">
                Agregar tipos de Aves
            </button>
        </div>
        
        <div class="collapse mb-4" id="agregarTipoAve">
            <div class="card card-body text-dark">
                <form id="agregarTipoAveForm" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="nombreTipo" class="form-label">Tipo de Ave</label>
                        <input type="text" class="form-control" 
                            id="nombreTipo" name="nombreTipo"
                            placeholder="Ejemplo: ponedora cuello pelado semipesada "
                            min="1" required>
                        <div class="invalid-feedback">
                            Debe contar con al menos 3 letras.
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAgregarTipoAve">Agregar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de tipos de ave -->
        <div class="card shadow-sm rounded-3 mb-3">
            <div class="card-body table-responsive">
                <table id="tablaTiposAve" class="table table-striped table-hover align-middle mb-0 bg-white">
                    <thead class="table-light">
                        <tr>
                            <th class="text-primary">ID</th>
                            <th class="text-primary">Descripción</th>
                            <th class="text-primary">✏</th>
                            <th class="text-primary">❌</th>
                        </tr>
                    </thead>
                    <tbody id="tipoAve">
                        <!-- Los datos se insertarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------------> 
<!-- MODAL: EDITAR TIPO DE Ave -->
<!------------------------------------------------> 
<div class="modal fade" id="editarTipoMant" tabindex="-1" aria-labelledby="editarTipoAveModal" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editarTipoAveModal">Editar descripción del tipo de ave</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarTipoAveForm" class="needs-validation" novalidate>
                <div class="mb-4">
                    <label for="nombreMant" class="form-label">Tipo de ave</label>
                    <input type="text" class="form-control" 
                        id="nombreTipoEdit" name="nombreTipoEdit"
                        placeholder="Ejemplo: ponedora barrada semipesada"
                        min="1" required>
                    <div class="invalid-feedback">
                        Debe contar con al menos 3 letras.
                    </div>
                </div>
                    <input type="hidden" id="idTipoAve" name="idTipoAve">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEditarTipoAve">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<script>
//------------------------------------------------
// Captar botones de agregar
//------------------------------------------------
document.getElementById('btnAgregarTipoAve').addEventListener('click', function() {
    agregarTipoAve();
});
document.getElementById('agregarTipoAveForm').addEventListener('submit', function(event) {
    event.preventDefault();
    agregarTipoAve();
});

//------------------------------------------------
// Captar botones de edición
//------------------------------------------------
document.getElementById('btnEditarTipoAve').addEventListener('click', function() {
    editarTipoAve();
});
document.getElementById('editarTipoAveForm').addEventListener('submit', function(event) {
    event.preventDefault();
    editarTipoAve();
});

//------------------------------------------------
// Rellenar modal de edición
//------------------------------------------------
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.btn-warning')) {
        const button = event.target;
        const idTipoAve = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        document.querySelector('#editarTipoAveForm #nombreTipoEdit').value = nombre;
        document.querySelector('#editarTipoAveForm #idTipoAve').value = idTipoAve;
    }
});

//------------------------------------------------
// Funciones ABM
//------------------------------------------------
function agregarTipoAve() {
    const nombreTipo = document.getElementById('nombreTipo').value;

    fetch('index.php?opt=lotesAves&ajax=addTipoAve', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nombre=' + encodeURIComponent(nombreTipo)
    })
    .then(response => response.json().then(data => {
        if (response.ok) {
            recargarTipoAve();
            $('#agregarTipoAve').collapse('hide');
            showToastOkay(data.msg);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(error => {
        console.error('Error AJAX:', error);
        showToastError('Error en la solicitud: ' + error.message);
    });
}

function editarTipoAve() {
    const idTipoAve = document.getElementById('idTipoAve').value;
    const nombreTipoEdit = document.getElementById('nombreTipoEdit').value;
    fetch('index.php?opt=lotesAves&ajax=editTipoAve', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idTipoAve=' + encodeURIComponent(idTipoAve) +
              '&nombre=' + encodeURIComponent(nombreTipoEdit)
    })
    .then(response => response.json().then(data => {
        if (response.ok) {
            recargarTipoAve();
            showToastOkay(data.msg);
            $('#editarTipoMant').modal('hide');
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(error => {
        console.error('Error AJAX:', error);
        showToastError('Error desconocido.');
    });
}

function eliminarTipoAve(idTipoAve) {
    fetch('index.php?opt=lotesAves&ajax=delTipoAve&idTipoAve=' + idTipoAve, {
        method: 'GET'
    })
    .then(response => response.json().then(data => {
        if (response.ok) {
            recargarTipoAve();
            showToastOkay(data.msg);
        } else {
            showToastError(data.msg);
        }
    }))
    .catch(error => {
        console.error('Error AJAX:', error);
        showToastError('Error desconocido.');
    });
}

function recargarTipoAve() {
    cargarTablaTipoAve();
}

function cargarTablaTipoAve() {
    if ($.fn.DataTable.isDataTable('#tablaTiposAve')) {
        $('#tablaTiposAve').DataTable().destroy();
    }
    var tbody = document.getElementById("tipoAve");
    tbody.innerHTML = '';

    fetch('index.php?opt=lotesAves&ajax=getTipoAve')
    .then(response => response.json())
    .then(data => {
        data.forEach(tipoAve => {
            var row = document.createElement("tr");
            row.className = "table-light";
            row.innerHTML = 
                '<td>' + tipoAve.idTipoAve + '</td>' +
                '<td>' + tipoAve.nombre + '</td>' +
                '<td><button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarTipoMant" data-id="' + tipoAve.idTipoAve + '" data-nombre="' + tipoAve.nombre + '">Editar</button></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarTipoAve(' + tipoAve.idTipoAve + ')">Borrar</button></td>';
            tbody.appendChild(row);
        });
        $('#tablaTiposAve').DataTable();
    })
    .catch(error => {
        console.error('Error al cargar tipos de aves:', error);
        $('#tablaTiposAve').DataTable();
    });
}
</script>
HTML;
// Agregar las funciones y el contenedor de los toast
// Para mostrar notificaciones
include 'view/toast.php';
$body .= $toast;

$body .= <<<HTML
<div class="container">
    <h2>Lotes filtrados por granjas</h2>

    <!-- Seleccionar Granja -->
    <div class="input-group mb-3">
        <select id="selectGranja" name="selectGranja" class="form-select rounded-start" required>
            <!-- opciones cargadas por JS -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#newLoteAves">
            Agregar Lote
        </button>
        <div class="invalid-feedback">Debe elegir una opción.</div>
    </div>

    <!-- Filtros de fechas + botones -->
    <div class="row mb-3 g-2">
        <div class="col-12 col-md-3">
            <label for="fechaNacimientoDesde" class="form-label">Fecha nacimiento desde:</label>
            <input type="date" id="fechaNacimientoDesde" class="form-control">
        </div>
        <div class="col-12 col-md-3">
            <label for="fechaNacimientoHasta" class="form-label">Fecha nacimiento hasta:</label>
            <input type="date" id="fechaNacimientoHasta" class="form-control">
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button id="btnFiltrar" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button id="btnReporte" class="btn btn-success w-100">Generar Reporte</button>
        </div>
    </div>

    <!-- Tabla de lotes - En vista movil es un parto-->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <table id="tablaLotes" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">Identificador</th>
                        <th class="text-primary">Fecha Nacimiento</th>
                        <th class="text-primary">Fecha Compra</th>
                        <th class="text-primary">Tipo de Ave</th>
                        <th class="text-primary">Cantidad</th>
                        <th class="text-primary">Precio Compra</th>
                        <th class="text-primary">Galpón</th>
                        <th class="text-primary">Acciones</th>
                    </tr>
                </thead>
                <tbody id="lotesAves"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Agregar Lote de Aves -->
<div class="modal fade" id="newLoteAves" tabindex="-1" aria-labelledby="newLoteAvesModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="newLoteAvesModal">Agregar Lote de Aves</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="newLoteAvesForm" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="identificador" class="form-label">Identificador</label>
                        <input type="text" class="form-control" id="identificador" name="identificador" required>
                        <div class="invalid-feedback">Ingrese un identificador válido.</div>
                    </div>
                    <div class="mb-4">
                        <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="fechaCompra" class="form-label">Fecha de Compra</label>
                        <input type="date" class="form-control" id="fechaCompra" name="fechaCompra" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="selectTipoAve" class="form-label">Tipo de Ave</label>
                        <select id="selectTipoAve" name="tipoAve" class="form-control" required></select>
                        <div class="invalid-feedback">Seleccione un tipo de ave válido.</div>
                    </div>
                    <div class="mb-4">
                        <label for="selectGalpon" class="form-label">Galpón</label>
                        <select id="selectGalpon" name="galpon" class="form-control" required></select>
                        <div class="invalid-feedback">Seleccione un galpón válido.</div>
                    </div>
                    <div class="mb-4">
                        <label for="cantidad" class="form-label">Cantidad de Aves</label>
                        <input type="number" step="1" class="form-control" id="cantidad" name="cantidad" required>
                        <div class="invalid-feedback">Ingrese una cantidad válida (1 o más).</div>
                    </div>
                    <div class="mb-4">
                        <label for="precioCompra" class="form-label">Precio de Compra</label>
                        <input type="number" step="0.01" class="form-control" id="precioCompra" name="precioCompra" required>
                        <div class="invalid-feedback">Ingrese un precio válido (0 o más).</div>
                    </div>
                    <input type="hidden" id="idGranja" name="idGranja">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnAgregarLote">Finalizar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar Lote de Aves -->
<div class="modal fade" id="editLoteAves" tabindex="-1" aria-labelledby="editLoteAvesModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editLoteAvesModal">Editar Lote de Aves</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editLoteAvesForm" class="needs-validation" novalidate>
                    <input type="hidden" id="editIdLote" name="idLoteAves">

                    <div class="mb-4">
                        <label for="editIdentificador" class="form-label">Identificador</label>
                        <input type="text" class="form-control" id="editIdentificador" name="identificador" required>
                        <div class="invalid-feedback">Ingrese un identificador válido.</div>
                    </div>
                    <div class="mb-4">
                        <label for="editFechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="editFechaNacimiento" name="fechaNacimiento" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="editFechaCompra" class="form-label">Fecha de Compra</label>
                        <input type="date" class="form-control" id="editFechaCompra" name="fechaCompra" required>
                        <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
                    </div>
                    <div class="mb-4">
                        <label for="editSelectTipoAve" class="form-label">Tipo de Ave</label>
                        <select id="editSelectTipoAve" name="tipoAve" class="form-control" required></select>
                        <div class="invalid-feedback">Seleccione un tipo de ave válido.</div>
                    </div>
                    <!--div class="mb-4">
                        <label for="editSelectGalpon" class="form-label">Galpón</label>
                        <select id="editSelectGalpon" name="galpon" class="form-control" required></select>
                        <div class="invalid-feedback">Seleccione un galpón válido.</div>
                    </div-->
                    <div class="mb-4">
                        <label for="editCantidad" class="form-label">Cantidad de Aves</label>
                        <input type="number" step="1" class="form-control" id="editCantidad" name="cantidad" required>
                        <div class="invalid-feedback">Ingrese una cantidad válida (1 o más).</div>
                    </div>
                    <div class="mb-4">
                        <label for="editPrecioCompra" class="form-label">Precio de Compra</label>
                        <input type="number" step="0.01" class="form-control" id="editPrecioCompra" name="precioCompra" required>
                        <div class="invalid-feedback">Ingrese un precio válido (0 o más).</div>
                    </div>
                    <input type="hidden" id="editIdGranja" name="idGranja">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<script>
// === Cargar granjas ===
function cargarSelectGranja() {
    const select = document.getElementById('selectGranja');
    select.innerHTML = '<option value="">Seleccione una granja</option>';
    fetch('index.php?opt=granjas&ajax=getGranjas')
        .then(res => res.json())
        .then(data => data.forEach(g => { const opt=document.createElement('option'); opt.value=g.idGranja; opt.text=g.nombre; select.appendChild(opt); }))
        .catch(err => { console.error('Error al cargar granjas:', err); showToastError('Error al cargar las granjas'); });
}

// Helper robusto para setear el valor de un <select>
function setSelectValue(selectEl, val) {
    if (val === null || val === undefined) return;
    const wanted = String(val).trim();
    // 1) Intento directo
    selectEl.value = wanted;
    if (selectEl.value === wanted) return;
    // 2) Intento por comparación estricta de string entre opciones
    for (const opt of selectEl.options) {
        if (String(opt.value).trim() === wanted) {
            opt.selected = true;
            return;
        }
    }
    // 3) Intento numérico (ej: "03" vs 3)
    const nWanted = Number(wanted);
    if (!Number.isNaN(nWanted)) {
        for (const opt of selectEl.options) {
            const nOpt = Number(String(opt.value).trim());
            if (!Number.isNaN(nOpt) && nOpt === nWanted) {
                opt.selected = true;
                return;
            }
        }
    }
}

// === Cargar tipos de ave ===
function cargarSelectTipoAve(selectId, preselectValue = null) {
    return fetch("index.php?opt=lotesAves&ajax=getTipoAve")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById(selectId);
            select.innerHTML = "";
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.idTipoAve;   // puede venir como número o string
                opt.textContent = item.nombre;
                select.appendChild(opt);
            });
            // ✅ preselección robusta
            setSelectValue(select, preselectValue);
        });
}

// === Cargar galpones según granja ===
function cargarSelectGalpon(idGranja, selectId, preselectValue = null) {
    return fetch("index.php?opt=galpones&ajax=getGalponesGranja&idGranja=" + idGranja)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById(selectId);
            select.innerHTML = "";
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.idGalpon;
                opt.textContent = item.identificacion + " - " + item.nombre;
                select.appendChild(opt);
            });
            // ✅ preselección robusta
            setSelectValue(select, preselectValue);
        });
}

document.getElementById('newLoteAves').addEventListener('show.bs.modal', function(){
    const selectedGranjaId = document.getElementById('selectGranja').value;
    if(!selectedGranjaId){
        showToastError('Debe seleccionar una granja primero');
        return;
    }
    document.getElementById('idGranja').value = selectedGranjaId;

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fechaNacimiento').value = today;
    document.getElementById('fechaCompra').value = today;

    cargarSelectTipoAve("selectTipoAve");
    cargarSelectGalpon(selectedGranjaId, "selectGalpon");
});

// Evento para el botón "Finalizar" del modal Nuevo Lote
document.getElementById('btnAgregarLote').addEventListener('click', function (e) {
    e.preventDefault(); // evita que se envíe el form por defecto
    agregarLote();
});
document.getElementById('newLoteAvesForm').addEventListener('submit', function(e){
    e.preventDefault();
    agregarLote();
});
// === Agregar lote ===
function agregarLote() {
    const form = document.getElementById('newLoteAvesForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const identificador = document.getElementById('identificador').value;
    const fechaNac = document.getElementById('fechaNacimiento').value;
    const fechaCompra = document.getElementById('fechaCompra').value;
    const tipoAve = document.getElementById('selectTipoAve').value;
    const galpon = document.getElementById('selectGalpon').value;
    const cantidad = document.getElementById('cantidad').value;
    const idGranja = document.getElementById('idGranja').value;
    const precioCompra = document.getElementById('precioCompra').value;
    fetch('index.php?opt=lotesAves&ajax=addLoteAves', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'identificador='+encodeURIComponent(identificador)
            +'&fechaNac='+encodeURIComponent(fechaNac)
            +'&fechaCompra='+encodeURIComponent(fechaCompra)
            +'&idTipoAve='+encodeURIComponent(tipoAve)
            +'&idGalpon='+encodeURIComponent(galpon)
            +'&cantidadAves='+encodeURIComponent(cantidad)
            +'&idGranja='+encodeURIComponent(idGranja)
            +'&precioCompra='+encodeURIComponent(precioCompra)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // HTTP 200 → agregado correcto
                document.getElementById('btnFiltrar').click();
                $('#newLoteAves').modal('hide');
                showToastOkay(data.msg);
            } else {
                // HTTP 400 → error
                showToastError(data.msg || "Error desconocido");
            }
        });
    })
    .catch(err => {
        console.error('Error AJAX:', err);
        showToastError('Error en la solicitud: ' + err.message);
    });
}

// === Filtrar lotes ===
document.getElementById("btnFiltrar").addEventListener("click", function() {
    const idGranja = document.getElementById("selectGranja").value;
    const desde = document.getElementById("fechaNacimientoDesde").value;
    const hasta = document.getElementById("fechaNacimientoHasta").value;
    if(!idGranja){ showToastError("Debe seleccionar una granja primero"); return; }
    if(!desde||!hasta){ showToastError("Debe seleccionar fechas Desde y Hasta"); return; }

    if($.fn.DataTable.isDataTable('#tablaLotes')) $('#tablaLotes').DataTable().destroy();
    document.getElementById("lotesAves").innerHTML = '';

    fetch("index.php?opt=lotesAves&ajax=getLotesAves&idGranja="+idGranja+"&desde="+desde+"&hasta="+hasta)
    .then(res=>res.json())
    .then(data=>{
        data.forEach(function(l){
            var row = "<tr class='table-light'>"
                + "<td>"+l.identificador+"</td>"
                + "<td>"+l.fechaNacimiento+"</td>"
                + "<td>"+l.fechaCompra+"</td>"
                + "<td>"+l.tipoAveNombre+"</td>"
                + "<td>"+l.cantidadAves+"</td>"
                + "<td>"+l.precioCompra+"</td>"
                + "<td>"+l.galponIdentificacion+"</td>"
                + "<td>"
                    + "<div class='dropdown'>"
                        + "<button class='btn btn-primary btn-sm dropdown-toggle' type='button' id='accionesDropdown"+l.idLoteAves+"' data-bs-toggle='dropdown' aria-expanded='false'>Acciones</button>"
                        + "<ul class='dropdown-menu' aria-labelledby='accionesDropdown"+l.idLoteAves+"'>"
                            + "<li><a class='dropdown-item' href='#' onclick='editarLote("+l.idLoteAves+")'>Editar</a></li>"
                            + "<li><a class='dropdown-item' href='#' onclick='eliminarLote("+l.idLoteAves+")'>Borrar</a></li>"
                            + "<li><a class='dropdown-item' href='index.php?opt=cargarMortandad&idLoteAves="+l.idLoteAves+"'>Registrar mortandad</a></li>"
                            + "<li><a class='dropdown-item' href='index.php?opt=cargarPesaje&idLoteAves="+l.idLoteAves+"'>Registrar pesaje</a></li>"
                            + "<li><a class='dropdown-item' href='index.php?opt=aplicarVacunas&idLoteAves="+l.idLoteAves+"'>Aplicar vacunas</a></li>"
                            + "<li><a class='dropdown-item' href='index.php?opt=moverGalpon&idLoteAves="+l.idLoteAves+"'>Mover de galpón</a></li>"
                            + "<li><a class='dropdown-item' href='index.php?opt=bajaLote&idLoteAves="+l.idLoteAves+"'>Dar de baja</a></li>"
                        + "</ul>"
                    + "</div>"
                + "</td>"
            + "</tr>";
            document.getElementById("lotesAves").insertAdjacentHTML("beforeend", row);
        });
        $('#tablaLotes').DataTable();
    })
    .catch(err=>{ console.error("Error:", err); $('#tablaLotes').DataTable(); });
});

// === Eliminar lote ===
function eliminarLote(idLote){
    fetch("index.php?opt=lotesAves&ajax=delLoteAves&idLoteAves="+idLote)
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // HTTP 200 → agregado correcto
                document.getElementById('btnFiltrar').click();
                showToastOkay(data.msg);
            } else {
                // HTTP 400 → error
                showToastError(data.msg || "Error desconocido");
            }
        });
    })
    .catch(err => {
        console.error('Error AJAX:', err);
        showToastError('Error en la solicitud: ' + err.message);
    });
}

// === Reporte imprimible ===
document.getElementById("btnReporte").addEventListener("click", function(){
    const desde=document.getElementById("fechaNacimientoDesde").value;
    const hasta=document.getElementById("fechaNacimientoHasta").value;
    const granjaNombre=document.querySelector("#selectGranja option:checked").text;
    if(!desde||!hasta){ showToastError("Debe seleccionar fechas Desde y Hasta"); return; }

    let rows="";
    document.querySelectorAll("#lotesAves tr").forEach(function(tr){
        const tds=tr.querySelectorAll("td");
        rows += "<tr>"
            + "<td>"+tds[0].innerText+"</td>"
            + "<td>"+tds[1].innerText+"</td>"
            + "<td>"+tds[2].innerText+"</td>"
            + "<td>"+tds[3].innerText+"</td>"
            + "<td>"+tds[4].innerText+"</td>"
            + "<td>"+tds[5].innerText+"</td>"
            + "</tr>";
    });

    const reporte = "<html>"
        + "<head>"
        + "<title>Reporte Lotes de Aves</title>"
        + "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>"
        + "<style>body{padding:20px;} h2,h4{text-align:center;margin-bottom:20px;} table{width:100%;border-collapse:collapse;margin-top:20px;} th,td{border:1px solid #000;padding:8px;text-align:left;}</style>"
        + "</head>"
        + "<body>"
        + "<h2>"+granjaNombre+"</h2>"
        + "<h4>Listado de Lotes de Aves</h4>"
        + "<p><strong>Desde:</strong> "+desde+" &nbsp;&nbsp; <strong>Hasta:</strong> "+hasta+"</p>"
        + "<table><thead><tr><th>ID</th><th>Fecha Nacimiento</th><th>Fecha Compra</th><th>Tipo Ave</th><th>Cantidad</th><th>Galpón</th></tr></thead><tbody>"
        + rows
        + "</tbody></table>"
        + "</body></html>";

    const ventana=window.open("","_blank");
    ventana.document.write(reporte);
    ventana.document.close();
    ventana.print();
});
function editarLote(idLote){
    fetch("index.php?opt=lotesAves&ajax=getLoteAvesById&idLoteAves=" + idLote)
    .then(res => res.json())
    .then(async lote => {
        // Inputs simples
        document.getElementById('editIdLote').value = lote.idLoteAves;
        document.getElementById('editIdentificador').value = lote.identificador;
        document.getElementById('editFechaNacimiento').value = lote.fechaNacimiento;
        document.getElementById('editFechaCompra').value = lote.fechaCompra;
        document.getElementById('editCantidad').value = lote.cantidadAves;
        document.getElementById('editIdGranja').value = lote.idGranja;
        document.getElementById('editPrecioCompra').value = lote.precioCompra;
        // Cargar selects con preselección integrada
        await cargarSelectTipoAve("editSelectTipoAve", lote.idTipoAve);
        //await cargarSelectGalpon(lote.idGranja, "editSelectGalpon", lote.idGalpon);

        // Mostrar modal al final
        $('#editLoteAves').modal('show');
    })
    .catch(err => {
        console.error("Error:", err);
        showToastError("No se pudo cargar el lote");
    });
}


document.getElementById('btnGuardarCambios').addEventListener('click', guardarCambios);
document.getElementById('editLoteAvesForm').addEventListener('submit', function(e){ e.preventDefault(); guardarCambios(); });
function guardarCambios(){
    const form = document.getElementById('editLoteAvesForm');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const idLote = document.getElementById('editIdLote').value;
    const identificador = document.getElementById('editIdentificador').value;
    const fechaNac = document.getElementById('editFechaNacimiento').value;
    const fechaCompra = document.getElementById('editFechaCompra').value;
    const tipoAve = document.getElementById('editSelectTipoAve').value;

    const cantidad = document.getElementById('editCantidad').value;
    const precioCompra = document.getElementById('editPrecioCompra').value;
   fetch('index.php?opt=lotesAves&ajax=editLoteAves', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'idLoteAves='+encodeURIComponent(idLote)
            +'&identificador='+encodeURIComponent(identificador)
            +'&fechaNac='+encodeURIComponent(fechaNac)
            +'&fechaCompra='+encodeURIComponent(fechaCompra)
            +'&idTipoAve='+encodeURIComponent(tipoAve)

            +'&cantidadAves='+encodeURIComponent(cantidad)
            +'&precioCompra='+encodeURIComponent(precioCompra)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                document.getElementById("btnFiltrar").click();
                $('#editLoteAves').modal('hide');
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(err => {
        console.error('Error AJAX:', err);
        showToastError('Error en la solicitud: ' + err.message);
    });
}
// === Inicialización ===
window.addEventListener("load", function(){
    cargarSelectGranja();
    cargarTablaTipoAve();
    const fechaHasta = new Date();
    const fechaDesde = new Date(); fechaDesde.setMonth(fechaHasta.getMonth()-1);
    function formatDate(d){ return d.toISOString().split("T")[0]; }
    document.getElementById("fechaNacimientoDesde").value = formatDate(fechaDesde);
    document.getElementById("fechaNacimientoHasta").value = formatDate(fechaHasta);
    $('#tablaLotes').DataTable();
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => { //identificacion capacidad idTipoAve idGranja
    initFormValidator("newLoteAvesForm", {
        fechaNacimiento : (value) => {
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
        fechaCompra : (value) => {
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
        identificador: (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        capacidad: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        galpon: (value) => {
            if (!value) return "Debe seleccionar un galpón.";
            return true;
        },
        tipoAve: (value) => {
            if (!value) return "Debe seleccionar un tipo de ave.";
            return true;
        },
        cantidad: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        precioCompra: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        }
    });
    initFormValidator("editLoteAvesForm", {
        fechaNacimiento : (value) => {
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
        fechaCompra : (value) => {
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
        identificador: (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        capacidad: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        tipoAve: (value) => {
            if (!value) return "Debe seleccionar un tipo de ave.";
            return true;
        },
        cantidad: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        precioCompra: (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        }
    });
});
</script>


HTML;