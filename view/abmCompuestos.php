<?php
$body = <<<HTML
<div class="container">
    <h1>Compuestos</h1>
    
    <p class="d-inline-flex gap-1">
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#verCompuesto" aria-expanded="false" aria-controls="collapseExample">
            Ver compuestos
        </button>
    </p>

    <div class="collapse mb-4" id="verCompuesto">
        <div class="mb-3">
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#agregarCompuesto" aria-expanded="false" aria-controls="collapseExample">
                Agregar compuesto
            </button>
        </div>
        
        <div class="collapse mb-4" id="agregarCompuesto">
            <div class="card card-body text-dark">
                <form id="agregarCompuestoForm" class="needs-validation" novalidate>
                    <div class="mb-2">
                        <label for="nombre" class="form-label">Compuesto</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ejemplo: bolsa de soja 10kg" min="1" required>
                        <div class="invalid-feedback">Debe contar con al menos 3 letras.</div>
                    </div>
                    <div class="mb-2">
                        <label for="proveedor" class="form-label">Proveedor</label>
                        <input type="text" class="form-control" id="proveedor" name="proveedor" placeholder="Ejemplo: Axonutra" min="1" required>
                        <div class="invalid-feedback">Debe contar con al menos 3 letras.</div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAgregarCompuesto">Agregar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de compuestos -->
        <div class="card shadow-sm rounded-3 mb-3">
            <div class="card-body table-responsive">
                <table id="tablaCompuesto" class="table table-striped table-hover align-middle mb-0 bg-white">
                    <thead class="table-light">
                        <tr>
                            <th class="text-primary">ID</th>
                            <th class="text-primary">Nombre</th>
                            <th class="text-primary">Proveedor</th>
                            <th class="text-primary">✏</th>
                        </tr>
                    </thead>
                    <tbody id="compuestos">
                        <!-- Los datos se insertarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!----------------------------> 
<!-- MODAL: Edit Compuestos -->
<!----------------------------> 
<div class="modal fade" id="editarCompuesto" tabindex="-1" aria-labelledby="editarCompuestoModal" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editarCompuestoModal">Editar Compuesto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarCompuestoForm" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="nombreEdit" class="form-label">Compuesto</label>
                        <input type="text" class="form-control" id="nombreEdit" name="nombreEdit" placeholder="Ejemplo: soja" min="1" required>
                        <div class="invalid-feedback">Debe contar con al menos 3 carácteres.</div>
                    </div>
                    <div class="mb-4">
                        <label for="proveedorEdit" class="form-label">Proveedor</label>
                        <input type="text" class="form-control" id="proveedorEdit" name="proveedorEdit" placeholder="Proveedor" min="1" required>
                        <div class="invalid-feedback">Debe contar con al menos 3 carácteres.</div>
                    </div>
                    <input type="hidden" id="idCompuesto" name="idCompuesto">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEditarCompuesto">Finalizar</button>
            </div>
        </div>
    </div>
</div>

<script>
<!----------------------------------->
<!-- Sección JavaScript Compuestos -->
<!-----------------------------------> 
<!--- RELLENAR FORM EDICIÓN ---> 
<!-----------------------------> 
document.addEventListener('click', function (event) {
    if (event.target && event.target.matches('.btn-warning')) {
        const button = event.target;
        const idCompuesto = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const proveedor = button.getAttribute('data-proveedor');
        document.querySelector('#editarCompuestoForm #nombreEdit').value = nombre;
        document.querySelector('#editarCompuestoForm #proveedorEdit').value = proveedor;
        document.querySelector('#editarCompuestoForm #idCompuesto').value = idCompuesto;
    }
});
<!----------------------------------> 
<!-- CAPTAR EL FORMULARIO AGREGAR -->  
<!----------------------------------> 
document.getElementById('btnAgregarCompuesto').addEventListener('click', function() {
    agregarCompuesto();
});
document.getElementById('agregarCompuestoForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    agregarCompuesto();
});
<!----------------------------------> 
<!-- ACCION FORMULARIO DE EDICIÓN -->  
<!----------------------------------> 
document.getElementById('btnEditarCompuesto').addEventListener('click', function() {
    editarCompuesto();
});
document.getElementById('editarCompuestoForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    editarCompuesto();
});
<!---------------------------------> 
<!------- AGREGAR COMPUESTO ------->  
<!---------------------------------> 
function agregarCompuesto() {
    const nombre = document.getElementById('nombre').value;
    const proveedor = document.getElementById('proveedor').value;

    fetch('index.php?opt=compuestos&ajax=addCompuesto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombre=' + encodeURIComponent(nombre) +
        '&proveedor=' + encodeURIComponent(proveedor)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Recargar la tabla
                recargarCompuestos();
                // Cerrar el modal
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

function editarCompuesto() {
    const idCompuesto = document.getElementById('idCompuesto').value;
    const nombreEdit = document.getElementById('nombreEdit').value;
    const proveedorEdit = document.getElementById('proveedorEdit').value;

    fetch('index.php?opt=compuestos&ajax=editCompuesto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idCompuesto=' + encodeURIComponent(idCompuesto) +
        '&nombre=' + encodeURIComponent(nombreEdit) +
        '&proveedor=' + encodeURIComponent(proveedorEdit)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                recargarCompuestos();
                showToastOkay(data.msg);
                $('#editarCompuesto').modal('hide');
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

function eliminarCompuesto(idCompuesto) {
    fetch('index.php?opt=compuestos&ajax=delCompuesto&idCompuesto=' + idCompuesto, {
        method: 'GET'
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                // Si la eliminación fue exitosa, recargar la tabla y los select
                recargarCompuestos();
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

function recargarCompuestos() {
    //Recargar secciones de la página cuando se cambia un tipo mant.
    cargarTablaCompuestos();
    cargarTablaComprasCom();
}

function cargarTablaCompuestos() {
    // Vaciar tabla
    if ($.fn.DataTable.isDataTable('#tablaCompuesto')) {
            $('#tablaCompuesto').DataTable().destroy();
    }
    var tablaCompuestoTbody = document.getElementById("compuestos");
    tablaCompuestoTbody.innerHTML = '';

    // Realizar la solicitud AJAX
    fetch('index.php?opt=compuestos&ajax=getCompuestos')
    .then(response => {
        if (!response.ok) { throw new Error('Error en la solicitud: ' + response.statusText); }
        return response.json();
    })
    .then(data => {
        // Recorrer los datos y crear las filas de la tabla
        data.forEach(comp => {
            var row = document.createElement("tr");
            row.className = "table-light";
            row.innerHTML = 
                '<td>' + comp.idCompuesto + '</td>' +
                '<td>' + comp.nombre + '</td>' +
                '<td>' + comp.proveedor + '</td>' +
                '<td>' +
                    '<button type="button" ' +
                        'class="btn btn-warning btn-sm" ' +
                        'data-bs-toggle="modal" ' +
                        'data-bs-target="#editarCompuesto" ' +
                        'data-id="' + comp.idCompuesto + '" ' +
                        'data-nombre="' + comp.nombre + '"' +
                        'data-proveedor="' + comp.proveedor + '">' +
                        'Editar' +
                    '</button>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="eliminarCompuesto(' + comp.idCompuesto + ')">Borrar</button>' +
                '</td>'
            tablaCompuestoTbody.appendChild(row);
        });
        $('#tablaCompuesto').DataTable();

    })
    .catch(error => {
        console.error('Error al cargar los tipos de mantenimiento:', error);
        $('#tablaTiposMant').DataTable();
    });
}

</script>
HTML;
$body .= <<<HTML
<div class="container">
    <h2>Compras por granjas</h2>

    <div class="input-group mb-3">
        <select id="selectGranja" name="selectGranja" class="form-select rounded-start" required>
            <!-- opciones -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#newMantGranja">
            Agregar compra
        </button>
        <div class="invalid-feedback">
            Debe elegir una opción.
        </div>
    </div>

    <!-- Tabla de mantenimiento de granjas -->
    <div class="card shadow-sm rounded-3 mb-3">
        <div class="card-body table-responsive">
            <table id="tablaComprasCom" class="table table-striped table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-primary">ID</th>
                        <th class="text-primary">Compuesto</th>
                        <th class="text-primary">Cantidad</th>
                        <th class="text-primary">Precio</th>
                        <th class="text-primary">Fecha</th>
                        <th class="text-primary">❌</th>
                    </tr>
                </thead>
                <tbody id="comprasCompuesto">
                    <!-- Los datos se insertarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal agregar compra de compuesto -->
<div class="modal fade" id="newMantGranja" tabindex="-1" aria-labelledby="newMantGranjaModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Agregar nueva compra de compuesto</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="newCompraComp" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="idcompuesto" class="form-label">Compuesto</label>
            <select id="idcompuesto" name="idcompuesto" class="form-select" required></select>
            <div class="invalid-feedback">Seleccione un compuesto.</div>
          </div>
          <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
            <div class="invalid-feedback">Ingrese una cantidad válida (1 o más).</div>
          </div>
          <div class="mb-3">
            <label for="fechaCompra" class="form-label">Fecha de compra</label>
            <input type="date" id="fechaCompra" name="fechaCompra" class="form-control" required>
            <div class="invalid-feedback">Seleccione una fecha válida (no futura).</div>
          </div>
          <div class="mb-3">
            <label for="preciocompra" class="form-label">Precio</label>
            <input type="number" id="preciocompra" name="preciocompra" step="0.01" class="form-control" required>
            <div class="invalid-feedback">Ingrese un precio válido (0 o más).</div>
          </div>
          <input type="hidden" id="idGranja" name="idGranja">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnAgregarCompra">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
<!-------------------------------------------------->
<!-- Sección JavaScript - Mantenimiento de Granja -->
<!--------------------------------------------------> 
<!------ CARGAR GRANJAS DISPONIBLES EN SELECT ------> 
<!-------------------------------------------------->
function cargarSelectGranja() {
    //Iniciar tabla, cargar opción por default.
    const selectFiltrarGranja = document.getElementById('selectGranja');
    selectFiltrarGranja.innerHTML = '';
    const defaultOption = document.createElement('option');
        defaultOption.text = 'Seleccione una granja';
        defaultOption.value = '';
        selectFiltrarGranja.appendChild(defaultOption);

    // Realizar la solicitud AJAX para obtener las granjas
    fetch('index.php?opt=granjas&ajax=getGranjas')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Agregar las granjas desde la API
        data.forEach(granja => {
            const optionAgregar = document.createElement('option');
            optionAgregar.value = granja.idGranja;
            optionAgregar.text = granja.nombre;
            selectFiltrarGranja.appendChild(optionAgregar);
        });

        // Si hay un valor previamente seleccionado, restaurarlo y cargar los galpones
        const previouslySelected = selectGranja.getAttribute('data-selected');
        if (previouslySelected) {
            selectFiltrarGranja.value = previouslySelected;
            cargarTablaGalpones();
        }
    })
    .catch(error => {
        console.error('Error al cargar granjas:', error);
        showToastError('Error al cargar las granjas');
    });
}

// === Cargar select de compuestos ===
function cargarSelectCompuestos(selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '';
    fetch('index.php?opt=compuestos&ajax=getCompuestos')
    .then(res => res.json())
    .then(data => {
        data.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.idCompuesto;
            opt.text = c.nombre + " (" + c.proveedor + ")";
            select.appendChild(opt);
        });
    })
    .catch(err => console.error("Error cargando compuestos:", err));
}

// === Abrir modal compras ===
document.getElementById("newMantGranja").addEventListener("show.bs.modal", function (event) {
    const selectedGranja = document.getElementById("selectGranja").value;
    if (!selectedGranja) {
        event.preventDefault();
        showToastError("Debe seleccionar una granja primero");
        return;
    }
    document.getElementById("idGranja").value = selectedGranja;
    cargarSelectCompuestos("idcompuesto");
});


// === Guardar compra ===
document.getElementById("btnAgregarCompra").addEventListener("click", function () {
    const form = document.getElementById('newCompraComp');
    // ejecutar las validaciones
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const data = new URLSearchParams(new FormData(form));
    fetch("index.php?opt=compuestos&ajax=addCompra", {
        method: "POST",
        body: data
    })
    .then(async response => {
        let resData;
        try {
            resData = await response.json();
        } catch (e) {
            resData = { msg: response.statusText };
        }

        if (response.ok) {
            showToastOkay(resData.msg);
            $('#newMantGranja').modal('hide');
            cargarTablaComprasCom();
        } else {
            showToastError(resData.msg || "Error desconocido: " + response.status);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud AJAX:", error);
        showToastError("Error en la solicitud AJAX: " + error.message);
    });
});

// === Tabla de compras ===
function cargarTablaComprasCom() {
    const idGranja = document.getElementById("selectGranja").value;
    if (!idGranja) return;

    if ($.fn.DataTable.isDataTable('#tablaComprasCom')) {
        $('#tablaComprasCom').DataTable().destroy();
    }
    document.getElementById("comprasCompuesto").innerHTML = "";
    fetch("index.php?opt=compuestos&ajax=getComprascompuesto&idGranja=" + encodeURIComponent(idGranja))
    .then(res => res.json())
    .then(data => {
        data.forEach(c => {
            var row = '<tr class="table-light">' +
                '<td>' + c.idCompraCompuesto + '</td>' +
                '<td>' + c.nombre + '</td>' +
                '<td>' + c.cantidad + '</td>' +
                '<td>' + c.precioCompra + '</td>' +
                '<td>' + c.fechaCompra + '</td>' +
                '<td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarCompra(' + c.idCompraCompuesto + ')">Borrar</button></td>' +
            '</tr>';
            document.getElementById("comprasCompuesto").insertAdjacentHTML("beforeend", row);
        });
        $('#tablaComprasCom').DataTable();
    })
    .catch(err => {
        console.error("Error cargando compras:", err);
        $('#tablaComprasCom').DataTable();
    });
}

// === Eliminar compra ===
function eliminarCompra(id) {
    fetch("index.php?opt=compuestos&ajax=delCompra&idCompraCompuesto=" + id)
    .then(res => res.json())
    .then(data => {
        showToastOkay(data.msg);
        cargarTablaComprasCom();
    })
    .catch(err => showToastError("Error al eliminar compra: " + err));
}

<!-- Listado GRANJAS - Filtrar al presionar opción del select -->
document.getElementById('selectGranja').addEventListener('change', function(e) {
    this.setAttribute('data-selected', e.target.value);
    if (e.target.value) {
        cargarTablaComprasCom();
    } else {
        // Limpiar la tabla si no hay granja seleccionada
        if ($.fn.DataTable.isDataTable('#tablaComprasCom')) {
            $('#tablaComprasCom').DataTable().clear().draw();
        }
    }
});

window.addEventListener('load', function() {
    cargarSelectGranja();
    cargarTablaCompuestos();
    $('#tablaComprasCom').DataTable();

    // === Configurar fecha por defecto ===
    const hoy = new Date();
    function formatDate(d) {
        return d.toISOString().split('T')[0]; // yyyy-mm-dd
    }
    document.getElementById('fechaCompra').value = formatDate(hoy);
});
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    initFormValidator("newCompraComp", {
    fechaCompra : (value) => {
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
    cantidad: (value) => {
        if (value <= 0) return "Debe ser mayor a 0.";
        return true;
    },
    precioCompra: (value) => {
        if (value <= 0) return "Debe ser mayor a 0.";
        return true;
    },
    idcompuesto : (value) => {
        if (!value) return "Debe seleccionar un tipo.";
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

