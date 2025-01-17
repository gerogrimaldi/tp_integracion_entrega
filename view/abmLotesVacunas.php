<?php
$idVacuna = isset($_GET['idVacuna']) ? (int)$_GET['idVacuna'] : 0;

$body = <<<HTML
<div class="container">
    <h1>Lotes de Vacunas</h1>

    <!-- Seleccionar Vacuna -->
    <div class="input-group mb-3">
        <select id="selectVacuna" name="selectVacuna" class="form-select rounded-start" style="width:70%" required>
            <!-- opciones cargadas por JS (Select2) -->
        </select>
        <button type="button" class="btn btn-primary rounded-end" data-bs-toggle="modal" data-bs-target="#modalAgregarLote">
            Registrar Lote
        </button>
        <div class="invalid-feedback">Debe elegir una vacuna.</div>
    </div>

  <!-- Tabla de lotes de la vacuna -->
  <div class="card shadow-sm rounded-3 mb-3">
      <div class="card-body table-responsive">
          <table id="tablaLotesVacuna" class="table table-striped table-hover align-middle mb-0 bg-white">
              <thead class="table-light">
                  <tr>
                      <th class="text-primary">ID</th>
                      <th class="text-primary">Número de Lote</th>
                      <th class="text-primary">Fecha Compra</th>
                      <th class="text-primary">Cantidad comprada</th>
                      <th class="text-primary">Cantidad disponible</th>
                      <th class="text-primary">Vencimiento</th>
                      <th class="text-primary">Acciones</th>
                  </tr>
              </thead>
              <tbody id="lotesVacuna"></tbody>
          </table>
      </div>
  </div>

</div>

<div class="modal fade" id="modalAgregarLote" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Lote de Vacuna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formAgregarLote" class="needs-validation" novalidate>
          <input type="hidden" id="idVacunaAgregar" name="idVacuna" value=idVacuna>
          <div class="mb-3">
            <label>Número de Lote</label>
            <input type="text" name="numeroLote" class="form-control" required>
            <div class="invalid-feedback">Ingrese el identificador del lote.</div>
          </div>
          <div class="mb-3">
            <label>Fecha Compra</label>
            <input type="date" name="fechaCompra" class="form-control" required>
            <div class="invalid-feedback">Seleccione una fecha de compra (no futura).</div>
          </div>
          <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
            <div class="invalid-feedback">Ingrese una cantidad, mayor a cero.</div>
          </div>
          <div class="mb-3">
            <label>Vencimiento</label>
            <input type="date" name="fechaVencimiento" class="form-control" required>
            <div class="invalid-feedback">Seleccione una fecha de vencimiento.</div>
          </div>


        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="btnAgregarLote">Agregar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditarLote" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Editar Lote de Vacuna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarLote" class="needs-validation" novalidate>
          <input type="hidden" id="idLoteEditar" name="idLoteVacuna">
          <div class="mb-3">
            <label>Número de Lote</label>
            <input type="text" name="numeroLote" class="form-control" required>
            <div class="invalid-feedback">Ingrese el identificador del lote.</div>
          </div>
          <div class="mb-3">
            <label>Fecha Compra</label>
            <input type="date" name="fechaCompra" class="form-control" required>
            <div class="invalid-feedback">Seleccione una fecha de compra (no futura).</div>
          </div>
          <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
            <div class="invalid-feedback">Ingrese una cantidad, mayor a cero.</div>
          </div>
          <div class="mb-3">
            <label>Vencimiento</label>
            <input type="date" name="fechaVencimiento" class="form-control" required>
            <div class="invalid-feedback">Seleccione una fecha de vencimiento.</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="btnEditarLote">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>


<script>
var idVacuna = $idVacuna;

//------------------------------------------------
// Cargar vacunas en select
//------------------------------------------------
function cargarVacunas() {
    return fetch('index.php?opt=vacunas&ajax=getVacunas')
    .then(r => r.json())
    .then(data => {
        var select = $('#selectVacuna');
        select.empty();
        select.append('<option value="">Seleccione una vacuna...</option>');
        data.forEach(function(v){
            var isSelected = (v.idVacuna == idVacuna);
            var opcion = new Option(v.nombre, v.idVacuna, isSelected, isSelected);
            select.append(opcion);
        });
        select.trigger('change');
        return data;
    });
}

//------------------------------------------------
// Cargar lotes de vacuna
//------------------------------------------------
function cargarLotesVacuna(idVacuna) {
    //Vaciar la tabla
    if ($.fn.DataTable.isDataTable('#tablaLotesVacuna')) {
        $('#tablaLotesVacuna').DataTable().destroy();
    }
    var tablaLotesVacunaTbody = document.getElementById("lotesVacuna");
    tablaLotesVacunaTbody.innerHTML = '';

    return fetch('index.php?opt=vacunas&ajax=getLotesVacuna&idVacuna=' + idVacuna)
    .then(r => r.json())
    .then(data => {
        var tbody = document.getElementById("lotesVacuna");
        tbody.innerHTML = '';
        data.forEach(l => {
            var row = document.createElement("tr");
            row.innerHTML =
                '<td>' + l.idLoteVacuna + '</td>' +
                '<td>' + l.numeroLote + '</td>' +
                '<td>' + l.fechaCompra + '</td>' +
                '<td>' + l.cantidad + '</td>' +
                '<td>' + l.cantidadDisponible + '</td>' +
                '<td>' + l.vencimiento + '</td>' +
                '<td>' +
                    '<button class="btn btn-sm btn-warning btn-edit" data-id="' + l.idLoteVacuna + '" ' +
                        'data-numero="' + l.numeroLote + '" ' +
                        'data-fecha="' + l.fechaCompra + '" ' +
                        'data-cantidad="' + l.cantidad + '" ' +
                        'data-vencimiento="' + l.vencimiento + '">' +
                        'Editar' +
                    '</button> ' +
                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + l.idLoteVacuna + '">Eliminar</button>' +
                '</td>';
            tbody.appendChild(row);
        });
        $('#tablaLotesVacuna').DataTable()
    });
}

// Agregar Lote
document.getElementById('btnAgregarLote').addEventListener('click', function() {
    const form = document.getElementById('formAgregarLote');
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    form.idVacuna.value = idVacuna;
    const data = new URLSearchParams(new FormData(form));
    fetch('index.php?opt=vacunas&ajax=addLoteVacuna', {
        method: 'POST',
        body: data
    })
    .then(r => r.json().then(resp => {
        if (r.ok) {
            showToastOkay(resp.msg);
            $('#modalAgregarLote').modal('hide');
            cargarLotesVacuna(idVacuna);
        } else {
            showToastError(resp.msg);
        }
    }));
});

// Editar Lote
document.addEventListener('click', function(event) {
    if (event.target && event.target.matches('.btn-edit')) {
        const l = event.target.dataset;
        const form = document.getElementById('formEditarLote');
        form.numeroLote.value = l.numero;
        form.fechaCompra.value = l.fecha;
        form.cantidad.value = l.cantidad;
        form.fechaVencimiento.value = l.vencimiento;
        form.idLoteVacuna.value = l.id;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarLote'));
        modal.show();
    }
    // Eliminar lote
    if(event.target && event.target.matches('.btn-delete')) {
    const idLoteVacuna = event.target.dataset.id;
    fetch('index.php?opt=vacunas&ajax=delLoteVacuna&idLoteVacuna=' + idLoteVacuna)
    .then(r => r.json().then(resp => {
        if (r.ok) {
            showToastOkay(resp.msg);
            cargarLotesVacuna(idVacuna);
        } else {
            showToastError(resp.msg);
        }
    }));
}
});

document.getElementById('btnEditarLote').addEventListener('click', function() {
    const form = document.getElementById('formEditarLote');
    if (!form.validateAll()) {
        form.classList.add('was-validated');
        return;
    }
    const data = new URLSearchParams(new FormData(form));
    fetch('index.php?opt=vacunas&ajax=editLoteVacuna', {
        method: 'POST',
        body: data
    })
    .then(r => r.json().then(resp => {
        if (r.ok) {
            showToastOkay(resp.msg);
            $('#modalEditarLote').modal('hide');
            cargarLotesVacuna(idVacuna);
        } else {
            showToastError(resp.msg);
        }
    }));
});

//------------------------------------------------
// Inicialización
//------------------------------------------------
window.addEventListener('load', function() {
    cargarVacunas().then(() => {
        if (idVacuna) cargarLotesVacuna(idVacuna);
    });

    $('#selectVacuna').select2({
        theme: 'bootstrap-5',
        placeholder: "Seleccione una vacuna...",
        allowClear: false,
        width: 'resolve'
    });

    $('#selectVacuna').on('change', function() {
        idVacuna = $(this).val();
        $('#idLoteVacuna').val(idVacuna);
        if (idVacuna) {
            cargarLotesVacuna(idVacuna);
        }
    });
});
</script>
</script>
<script src="js/formValidator.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => { //identificacion capacidad idTipoAve idGranja
    initFormValidator("formAgregarLote", {
        numeroLote : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        cantidad : (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        fechaCompra : (value, field) => {
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
        fechaVencimiento: (value, field) => {return true;
        }
    });
    initFormValidator("formEditarLote", {
        numeroLote : (value) => {
            if (value.length = 0) return "Ingrese los datos solicitados.";
            return true;
        },
        cantidad : (value) => {
            if (value <= 0) return "Debe ser mayor a 0.";
            return true;
        },
        fechaCompra : (value, field) => {
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
        fechaVencimiento: (value, field) => {return true;
        }
    });
});
</script>
HTML;

include 'view/toast.php';
$body .= $toast;
?>
