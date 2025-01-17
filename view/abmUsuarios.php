<?php
$body = <<<HTML
<div class="container">
    <h1>Usuarios</h1>
    <form id="selectUsuarioForm" class="needs-validation" novalidate>
        <div class="mb-4">
            <label for="selectUsuario" class="form-label">Seleccione un usuario para ver o modificar sus datos.</label>
            <div class="input-group">
                <select id="selectUsuario" name="selectUsuario" class="form-control" required>
                    <!-- Las opciones se agregan con JavaScript -->
                </select>
                <button type="button" class="btn btn-primary rounded ms-2" data-bs-toggle="modal" data-bs-target="#agregarUsuario">
                    Añadir Usuario
                </button>
                <div class="invalid-feedback">
                    Debe elegir una opción.
                </div>
            </div>
        </div>
    </form>
    <div id="cardUsuario" class="card mt-4 d-none">
    <div class="card-body">
        <h5 class="card-title">Datos del Usuario</h5>
        <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Nombre:</strong> <span id="datoNombre"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditar" data-campo="nombre">
            <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Email:</strong> <span id="datoEmail"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditar" data-campo="email">
            <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Domicilio:</strong> <span id="datoDomicilio"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditar" data-campo="domicilio">
            <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Teléfono:</strong> <span id="datoTelefono"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditar" data-campo="telefono">
            <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Fecha Nac.:</strong> <span id="datoFechaNac"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditar" data-campo="fechaNac">
            <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Tipo de usuario:</strong> <span id="datoTipoUsuario"></span></span>
            <button class="btn btn-sm btn-outline-primary btn-editar btnEditar" data-campo="tipoUsuario">
                <i class="bi bi-pencil"></i>
            </button>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>Contraseña</strong> <span id="datoPassword"></span></span>
            <button class="btn btn-sm btn-outline-primary btnEditarPassword">
                <i class="bi bi-pencil"></i> Cambiar
            </button>
        </li>
        <div class="mt-3 d-flex justify-content-end">
            <button id="btnEliminarUsuario" class="btn btn-danger">Eliminar Usuario</button>
        </div>
        </ul>
    </div>
    </div>
</div>

<!-- Modal popUp Agregar Usuario -->
<div class="modal fade" id="agregarUsuario" tabindex="-1" aria-labelledby="agregarUsuarioModal" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="agregarUsuarioModal">Agregar Usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <form id="agregarUsuarioForm" class="needs-validation" novalidate>
    <div class="mb-4">
        <label for="nombre" class="form-label">Nombre completo del usuario</label>
        <input type="text" 
               class="form-control" 
               id="nombre" 
               name="nombre" 
               placeholder="Apellido y nombre"
               minlength="3"
               required>
        <div class="invalid-feedback">
            El nombre debe tener al menos 3 caracteres.
        </div>
    </div>
    <div class="mb-4">
        <label for="email" class="form-label">E-Mail</label>
        <input type="text" 
               class="form-control" 
               id="email" 
               name="email" 
               placeholder="Dirección de correo electrónico"
               min="5"
               required>
        <div class="invalid-feedback">
            El email ingresado no es válido.
        </div>
    </div>
    <div class="mb-4">
        <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text" 
                 class="form-control" 
                 id="domicilio" 
                 name="domicilio" 
                 placeholder="Calle, número y localidad"
                 required>
        <div class="invalid-feedback">
            El campo debe tener al menos 5 caracteres.
        </div>
     </div>
    <div class="mb-4">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" 
               class="form-control" 
               id="telefono" 
               name="telefono" 
               placeholder="Número de teléfono"
               minlength="3"
               required>
        <div class="invalid-feedback">
            La ubicación debe tener al menos 3 caracteres.
        </div>
    </div>
    <div class="mb-4">
        <label for="contrasenia" class="form-label">Contraseña de acceso</label>
        <input type="password" 
               class="form-control" 
               id="contrasenia" 
               name="contrasenia" 
               placeholder="Contraseña"
               minlength="6"
               required>
        <div class="invalid-feedback">
            La contraseña debe tener al menos 6 carácteres.
        </div>
    </div>
    <div class="mb-4">
        <label for="fechaNac" class="form-label">Fecha de Nacimiento</label>
        <input type="date" class="form-control" 
            id="fechaNac" name="fechaNacimiento"
            required>
        <div class="invalid-feedback">
            Seleccione una fecha válida.
        </div>
    </div>
    <div class="mb-3">
        <label for="tipoUsuario" class="form-label">Tipo de usuario</label>
        <select class="form-select" id="tipoUsuario" name="tipoUsuario" required>
            <option value="Encargado">Encargado</option>
            <option value="Propietario">Propietario</option>
        </select>
        <div class="invalid-feedback">
            Por favor, seleccione un tipo de usuario.
        </div>
    </div>
</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnAgregarUsuario">Agregar</button>
      </div>
    </div>
  </div>
</div>

<script>
function cargarSelectUsuarios() {
    //Iniciar tabla, cargar opción por default.
    const selectUsuarios = document.getElementById('selectUsuario');
    selectUsuarios.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.text = 'Seleccione un usuario';
    defaultOption.value = '';
    selectUsuarios.appendChild(defaultOption);

    // Realizar la solicitud AJAX para obtener las granjas
    fetch('index.php?opt=usuarios&ajax=getUsuarios')
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        // Agregar los usuarios desde la API
        data.forEach(user => {
            const optionAgregar = document.createElement('option');
            optionAgregar.value = user.idUsuario;
            optionAgregar.text = user.nombre + " - " + user.email;
            selectUsuarios.appendChild(optionAgregar);
        });

        // Si hay un valor previamente seleccionado, restaurarlo y cargar los galpones
        const previouslySelected = selectUsuarios.getAttribute('data-selected');
        if (previouslySelected) {
            selectUsuarios.value = previouslySelected;
            cargarCardUsuarios();
        }
    })
    .catch(error => {
        console.error('Error al cargar usuarios:', error);
        showToastError('Error al cargar los usuarios');
    });
}

// Listado Usuarios - Filtrar al presionar opción del select
document.getElementById('selectUsuario').addEventListener('change', function(e) {
    this.setAttribute('data-selected', e.target.value);
    if (e.target.value) {
        cargarCardUsuarios();
    }
});

// USUARIOS - CAPTAR EL FORMULARIO AGREGAR
document.getElementById('btnAgregarUsuario').addEventListener('click', function() {
    agregarUsuario();
});
document.getElementById('agregarUsuarioForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    agregarUsuario();
});

// ------- RELLENAR CARD -------
function cargarCardUsuarios() {
    const idUsuario = document.getElementById('selectUsuario').value;
    if (!idUsuario) return;

    fetch('index.php?opt=usuarios&ajax=getUsuario&idUsuario=' + idUsuario)
        .then(response => {
            if (!response.ok) throw new Error('Error al obtener usuario');
            return response.json();
        })
        .then(usuarios => {
            if (!Array.isArray(usuarios) || usuarios.length === 0) {
                throw new Error('No se encontró el usuario');
            }
            const usuario = usuarios[0];  // Tomo el primer usuario del array

            // Mostrar card
            document.getElementById('cardUsuario').classList.remove('d-none');

            // Rellenar datos
            document.getElementById('datoNombre').textContent = usuario.nombre || '';
            document.getElementById('datoEmail').textContent = usuario.email || '';
            document.getElementById('datoDomicilio').textContent = usuario.direccion || '';
            document.getElementById('datoTelefono').textContent = usuario.telefono || '';
            document.getElementById('datoFechaNac').textContent = usuario.fechaNac || '';
            document.getElementById('datoTipoUsuario').textContent = usuario.tipoUsuario || '';
        })
        .catch(error => {
            console.error(error);
            showToastError('Error al cargar datos del usuario.');
        });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.btnEditar')) {
        const btn = e.target.closest('.btnEditar');
        const campo = btn.getAttribute('data-campo');
        const spanDato = document.getElementById('dato' + campo.charAt(0).toUpperCase() + campo.slice(1));
        

        if (btn.classList.contains('modo-edicion')) {
            let nuevoValor;
            if (campo === 'tipoUsuario') {
                // Si el campo es 'tipoUsuario', buscamos el elemento <select>
                nuevoValor = spanDato.querySelector('select').value;
            } else {
                // Para todos los demás campos, buscamos el elemento <input>
                nuevoValor = spanDato.querySelector('input').value;
            }
            actualizarCampoUsuario(campo, nuevoValor, btn, spanDato);
        } else {
            // Cambiar a modo edición
            const valorActual = spanDato.textContent;
            if (campo === "fechaNac") {
            // Si el valor viene con hora (YYYY-MM-DDTHH:MM:SS), cortamos la fecha
            const valorSoloFecha = valorActual.includes('T') ? valorActual.split('T')[0] : valorActual;
                spanDato.innerHTML = '<input type="date" class="form-control form-control-sm" value="' + valorSoloFecha + '">';
            } else if (campo === "tipoUsuario") {
                const options = ['Encargado', 'Propietario'];
                let selectHtml = '<select class="form-control form-control-sm">';
                options.forEach(option => {
                    selectHtml += '<option value="' + option + '" ' + (valorActual === option ? 'selected' : '') + '>' + option + '</option>';
                });
                selectHtml += '</select>';
                spanDato.innerHTML = selectHtml;
            } else {
                spanDato.innerHTML = '<input type="text" class="form-control form-control-sm" value="' + valorActual + '">';
            }
                    btn.innerHTML = '<i class="bi bi-check"></i>';
                    btn.classList.add('modo-edicion');
            }
        }
});

function actualizarCampoUsuario(campo, valor, btn, spanDato) {
    const idUsuario = document.getElementById('selectUsuario').value;
    console.log('idUsuario=' + encodeURIComponent(idUsuario) +
              '&campo=' + encodeURIComponent(campo) +
              '&valor=' + encodeURIComponent(valor));
    fetch('index.php?opt=usuarios&ajax=updateCampo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idUsuario=' + encodeURIComponent(idUsuario) +
              '&campo=' + encodeURIComponent(campo) +
              '&valor=' + encodeURIComponent(valor)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                spanDato.textContent = valor;
                btn.innerHTML = '<i class="bi bi-pencil"></i>';
                btn.classList.remove('modo-edicion');
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(error => {
        console.error(error);
        showToastError('Error al actualizar el dato.');
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.btnEditarPassword')) {
        const btn = e.target.closest('.btnEditarPassword');
        const contenedor = document.getElementById('datoPassword');

        // Si está en modo edición → enviar
        if (btn.classList.contains('modo-edicion')) {
            const actual = contenedor.querySelector('input[name="passwordActual"]').value;
            const nueva = contenedor.querySelector('input[name="passwordNueva"]').value;
            actualizarPasswordUsuario(actual, nueva, btn, contenedor);
        } else {
            // Modo edición: mostrar inputs
            contenedor.innerHTML = `
                <input type="password" name="passwordActual" class="form-control form-control-sm mb-1" placeholder="Contraseña actual">
                <input type="password" name="passwordNueva" class="form-control form-control-sm" placeholder="Nueva contraseña">
                <button class="btn btn-sm btn-success btnEditarPassword modo-edicion mt-1">
                    <i class="bi bi-check"></i> Confirmar
                </button>
            `;
        }
    }
});

function actualizarPasswordUsuario(passwordActual, passwordNueva, btn, contenedor) {
    const idUsuario = document.getElementById('selectUsuario').value;
    fetch('index.php?opt=usuarios&ajax=updatePassword', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'idUsuario=' + encodeURIComponent(idUsuario) +
              '&campo=' + encodeURIComponent(passwordActual) +
              '&valor=' + encodeURIComponent(passwordNueva)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                contenedor.innerHTML = `<button class="btn btn-sm btn-outline-primary btnEditarPassword">
                                            <i class="bi bi-pencil"></i> Cambiar
                                         </button>`;
                showToastOkay(data.msg);
            } else {
                showToastError(data.msg);
            }
        });
    })
    .catch(error => {
        console.error(error);
        showToastError('Error al actualizar la contraseña.');
    });
}

function agregarUsuario() {
    const nombre = document.getElementById('nombre').value;
    const email = document.getElementById('email').value;
    const domicilio = document.getElementById('domicilio').value;
    const telefono = document.getElementById('telefono').value;
    const contrasenia = document.getElementById('contrasenia').value;
    const fechaNac = document.getElementById('fechaNac').value;
    const tipoUsuario = document.getElementById('tipoUsuario').value;

    fetch('index.php?opt=usuarios&ajax=registrar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombre=' + encodeURIComponent(nombre) +
              '&email=' + encodeURIComponent(email) +
              '&direccion=' + encodeURIComponent(domicilio) + // backend espera 'direccion'
              '&telefono=' + encodeURIComponent(telefono) +
              '&password=' + encodeURIComponent(contrasenia) + // backend espera 'password'
              '&fechaNac=' + encodeURIComponent(fechaNac) +
              '&tipoUsuario=' + encodeURIComponent(tipoUsuario)
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                cargarSelectUsuarios(); // recarga lista de usuarios
                const modal = bootstrap.Modal.getInstance(document.getElementById('agregarUsuario'));
                modal.hide();
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

// Evento para el botón de eliminar
document.getElementById('btnEliminarUsuario').addEventListener('click', function() {
    const idUsuario = document.getElementById('selectUsuario').value;
    if (!idUsuario) {
        showToastError('Seleccione un usuario primero');
        return;
    }
    
    if (confirm('¿Está seguro de eliminar este usuario permanentemente?')) {
        eliminarUsuario(idUsuario);
    }
});

function eliminarUsuario(idUsuario) {
    fetch('index.php?opt=usuarios&ajax=delUsuario&idUsuario=' + idUsuario, {
        method: 'GET'
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.msg || 'Error en la solicitud');
            });
        }
        return response.json();
    })
    .then(data => {
        // Ocultar card y recargar lista
        document.getElementById('cardUsuario').classList.add('d-none');
        document.getElementById('selectUsuario').value = '';
        cargarSelectUsuarios();
        showToastOkay(data.msg);
    })
    .catch(error => {
        console.error('Error:', error);
        showToastError(error.message || 'Error al eliminar usuario');
    });
}

// ... Otras funciones comentadas (si quieres las activas, quítales /* */ y ajusta comentarios JS)

// Activar funciones al cargar la página
window.addEventListener('load', function() {
    cargarSelectUsuarios();
});
</script>

HTML;

// Agregar las funciones y el contenedor de los toast
// Para mostrar notificaciones
include 'view/toast.php';
$body .= $toast;
?>