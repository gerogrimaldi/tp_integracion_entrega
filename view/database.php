<?php
require_once __DIR__.'/../model/testModel.php';
$ultimoBackup = Test::obtenerUltimaFechaBackup();
$diasSinBackup = null;
if ($ultimoBackup) {
    $fechaBackup = new DateTime($ultimoBackup);
    $hoy = new DateTime();
    $diasSinBackup = $hoy->diff($fechaBackup)->days;
}

$body = <<<HTML
<div class="container">
    <h1>Gestión de base de datos</h1>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <form id="formBackup" action="index.php?opt=test" method="POST" class="m-0">
            <input type="hidden" name="btTest" value="backupDB">
            <button type="submit" class="btn btn-primary" id="btnBackup">Crear y descargar backup</button>
        </form>

        <button class="btn btn-primary" type="button" id="btnRestore">
            Restaurar copia de seguridad
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Última copia de seguridad</h5>
            <p class="card-text">__INFORMACION__</p>
        </div>
    </div>

    <!-- Input oculto para seleccionar archivo -->
    <input type="file" id="archivoBackup" name="archivoBackup" accept=".sql" style="display:none">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btTest = document.getElementById('btTest');
    const archivoBackup = document.getElementById('archivoBackup');

    document.getElementById('btnRestore').addEventListener('click', function () {
        archivoBackup.click();
    });
    archivoBackup.addEventListener('change', function () {
        if (archivoBackup.files.length > 0) {
            const formData = new FormData();
            formData.append('btTest', 'restoreDB');
            formData.append('archivoBackup', archivoBackup.files[0]);

            fetch('index.php?opt=test', {
                method: 'POST',
                body: formData
            })
            .then(resp => resp.ok ? resp.json() : Promise.reject(resp.json()))
            .then(data => {
                if (data.msg) {
                    showToastOkay(data.msg);
                }
            })
            .catch(async err => {
                const data = await err;
                showToastError(data.msg || 'Error al restaurar la base de datos.');
            });
        }
    });
});
</script>
HTML;
// Si el backup tiene más de 15 días, generar script JS para mostrar toast
if ($diasSinBackup !== null && $diasSinBackup > 15) {
    $body = str_replace('__INFORMACION__', htmlspecialchars('No se realiza una copia de seguridad hace más de 15 días (último backup: hace __DIAS__ días). Procure realizarla lo antes posible.'), $body);
}else{
    $body = str_replace('__INFORMACION__', htmlspecialchars('Se ha realizado una copia de seguridad hace __DIAS__ días.'), $body);
}
$body = str_replace('__DIAS__', htmlspecialchars($diasSinBackup), $body);
include 'view/toast.php';
$body .= $toast;
?>

