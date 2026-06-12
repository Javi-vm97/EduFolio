<?php
/* EduFolio - Vista reutilizable de seccion (esqueleto Fase 1). Variables: $seccion_titulo, $seccion_desc, $seccion_funcs (array), $seccion_icono (clase bi), $seccion_color (clase bg-). */
$seccion_icono = $seccion_icono ?? 'bi-folder-fill';
$seccion_color = $seccion_color ?? 'bg-indigo';
?>
<div class="seccion-encabezado reveal">
    <div class="ic <?= e($seccion_color) ?>"><i class="bi <?= e($seccion_icono) ?>"></i></div>
    <div>
        <h1><?= e($seccion_titulo) ?></h1>
        <p><?= e($seccion_desc) ?></p>
    </div>
</div>

<div class="seccion-vacia reveal">
    <div class="ic-grande"><i class="bi bi-cone-striped"></i></div>
    <h2>Seccion en construccion</h2>
    <p>Esta seccion estara disponible en la <strong>Fase 2</strong> del proyecto.
       Aqui podras gestionar tus contenidos de forma completa.</p>

    <div class="lista-funcs">
        <h3>Funcionalidades previstas</h3>
        <ul>
            <?php foreach ($seccion_funcs as $f): ?>
                <li><i class="bi bi-check-circle-fill"></i> <span><?= e($f) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <a class="btn btn-outline" href="dashboard.php"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
</div>
