<?php
    $body='<div class="landing text-center col-12 mt-5">
    <div class="row mt-5 justify-content-center mb-3"> 
        <h1 class="fw-bold text-center">Bienvenido</h1>

        <div class="text-center mt-5">
            <form method="POST" action="index.php?opt=test">
                <button type="submit" name="btTest" value="testConnect" class="btn btn-success">
                    Verificar conexion a la BD
                </button>
                <button type="submit" name="btTest" value="crearBD" class="btn btn-success">
                    Crear BD
                </button>
                <button type="submit" name="btTest" value="cargarDatos" class="btn btn-success">
                    Cargar Datos de prueba
                </button>
                <button type="submit" name="btTest" value="crearTablas" class="btn btn-success">
                    Crear tablas
                </button>
                <button type="submit" name="btTest" value="borrarDB" class="btn btn-success">
                    Borrar Base de Datos
                </button>
                <button type="submit" name="btTest" value="backupDB" class="btn btn-success">
                    Backup BD
                </button>
            </form>
        </div>
</div>';
     
?>
