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
<div class="container py-1">
    <h1 class="text-center mb-4">Panel Principal</h1>

    <!-- Administrativo / Mantenimientos y Compras -->
    <h4 class="mb-3">Administrativo</h4>
    <div class="d-grid gap-3 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="col">
            <a href="index.php?opt=granjas" class="text-decoration-none">
                <div class="card h-100 text-center bg-success text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-house-door display-1"></i>
                        <h6 class="card-title mt-2">Gestionar Granjas</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=mantenimientos" class="text-decoration-none">
                <div class="card h-100 text-center bg-warning text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-tools display-1"></i>
                        <h6 class="card-title mt-2">Mantenimientos Granjas</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=galpones" class="text-decoration-none">
                <div class="card h-100 text-center bg-secondary text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-building display-1"></i>
                        <h6 class="card-title mt-2">Gestionar Galpones</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=mantenimientosGalpones" class="text-decoration-none">
                <div class="card h-100 text-center bg-warning text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-tools display-1"></i>
                        <h6 class="card-title mt-2">Mantenimientos Galpones</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=vacunas" class="text-decoration-none">
                <div class="card h-100 text-center bg-info text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-capsule display-1"></i>
                        <h6 class="card-title mt-2">Vacunas</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=lotesVacunas" class="text-decoration-none">
                <div class="card h-100 text-center bg-primary text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-box-seam display-1"></i>
                        <h6 class="card-title mt-2">Lotes de vacunas</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=compuestos" class="text-decoration-none">
                <div class="card h-100 text-center bg-success text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-leaf display-1"></i>
                        <h6 class="card-title mt-2">Compuestos</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=usuarios" class="text-decoration-none">
                <div class="card h-100 text-center bg-primary text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-people display-1"></i>
                        <h6 class="card-title mt-2">Usuarios</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=database" class="text-decoration-none">
                <div class="card h-100 text-center bg-dark text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-gear display-1"></i>
                        <h6 class="card-title mt-2">Base de datos</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Lotes de Aves / Operaciones -->
    <h4 class="mb-3">Operaciones con Aves</h4>
    <div class="d-grid gap-3 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="col">
            <a href="index.php?opt=lotesAves" class="text-decoration-none">
                <div class="card h-100 text-center bg-primary text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-egg-fried display-1"></i>
                        <h6 class="card-title mt-2">Gestionar Lotes</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=cargarMortandad" class="text-decoration-none">
                <div class="card h-100 text-center bg-danger text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-heartbreak display-1"></i>
                        <h6 class="card-title mt-2">Cargar Mortandad</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=cargarPesaje" class="text-decoration-none">
                <div class="card h-100 text-center bg-warning text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-speedometer2 display-1"></i>
                        <h6 class="card-title mt-2">Cargar Pesaje</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=moverGalpon" class="text-decoration-none">
                <div class="card h-100 text-center bg-secondary text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-arrow-left-right display-1"></i>
                        <h6 class="card-title mt-2">Cambiar ubicación</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=aplicarVacunas" class="text-decoration-none">
                <div class="card h-100 text-center bg-info text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-journal-medical display-1"></i>
                        <h6 class="card-title mt-2">Aplicar Vacunas</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="index.php?opt=bajaLote" class="text-decoration-none">
                <div class="card h-100 text-center bg-danger text-white shadow">
                    <div class="card-body py-3">
                        <i class="bi bi-box-arrow-down display-1"></i>
                        <h6 class="card-title mt-2">Bajas</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>
HTML;

include 'view/toast.php';
$body .= $toast;

if ($diasSinBackup !== null && $diasSinBackup > 15) {
    $body .= "<script>showToastError('No se realiza una copia de seguridad hace más de 15 días (último backup: hace {$diasSinBackup} días). Procure realizarla lo antes posible.');</script>";
}
?>
