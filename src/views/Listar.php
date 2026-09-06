<?php
function claseColorNota($nota) {
    if ($nota === null) return 'text-muted';
    if ($nota >= 9) return 'nota-sobresaliente';
    if ($nota >= 7) return 'nota-notable';
    if ($nota >= 5) return 'nota-aprobado';
    if ($nota >= 4) return 'nota-suficiente';
    return 'nota-suspenso';
}

function claseResumenCard($nota) {
    if ($nota === null) return 'resumen-sin-nota';
    if ($nota >= 9) return 'resumen-sobresaliente';
    if ($nota >= 7) return 'resumen-notable';
    if ($nota >= 5) return 'resumen-aprobado';
    if ($nota >= 4) return 'resumen-suficiente';
    return 'resumen-suspenso';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Notas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css?v=<?= time() ?>">
</head>
<body>
<div class="contenedor-app">

    <header class="cabecera-app">
        <h1 class="cabecera-titulo">Seguimiento de notas</h1>
    </header>

    <main>
        <!-- Selector de titulación / curso / trimestre -->
        <nav class="card selector-nav mb-4" aria-label="Selección de trimestre">
            <?php if (!empty($titulaciones)): ?>
            <div class="selector-fila">
                <span class="selector-etiqueta" id="lbl-titulacion">Titulación</span>
                <div class="d-flex flex-wrap gap-2" role="group" aria-labelledby="lbl-titulacion">
                    <?php foreach ($titulaciones as $t): ?>
                        <a href="index.php?accion=seleccionar&tipo=titulacion&id=<?= $t['id'] ?>"
                           class="btn-selector <?= (int) $t['id'] === $titulacionId ? 'btn-selector--activo' : '' ?>"
                           aria-current="<?= (int) $t['id'] === $titulacionId ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($t['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($cursos)): ?>
            <div class="selector-fila">
                <span class="selector-etiqueta" id="lbl-curso">Curso</span>
                <div class="d-flex flex-wrap gap-2" role="group" aria-labelledby="lbl-curso">
                    <?php foreach ($cursos as $c): ?>
                        <a href="index.php?accion=seleccionar&tipo=curso&id=<?= $c['id'] ?>"
                           class="btn-selector <?= (int) $c['id'] === $cursoId ? 'btn-selector--activo' : '' ?>"
                           aria-current="<?= (int) $c['id'] === $cursoId ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($c['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($trimestres)): ?>
            <div class="selector-fila">
                <span class="selector-etiqueta" id="lbl-trimestre">Trimestre</span>
                <div class="d-flex flex-wrap gap-2" role="group" aria-labelledby="lbl-trimestre">
                    <?php foreach ($trimestres as $t): ?>
                        <a href="index.php?accion=seleccionar&tipo=trimestre&id=<?= $t['id'] ?>"
                           class="btn-selector <?= (int) $t['id'] === $trimestreId ? 'btn-selector--activo' : '' ?>"
                           aria-current="<?= (int) $t['id'] === $trimestreId ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($t['nombre']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </nav>

        <?php if (!empty($asignaturas)): ?>
        <!-- Panel resumen del trimestre -->
        <section class="panel-resumen mb-4" aria-label="Resumen del trimestre">
            <h2 class="panel-resumen__titulo">Resumen — <?= htmlspecialchars($trimestreNombre) ?></h2>
            <div class="resumen-grid">
                <?php foreach ($asignaturas as $asig): ?>
                    <div class="resumen-card <?= claseResumenCard($asig['media']) ?>">
                        <span class="resumen-card__nombre"><?= htmlspecialchars($asig['nombre']) ?></span>
                        <span class="resumen-card__nota">
                            <?= $asig['media'] !== null ? number_format($asig['media'], 2) : '--' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="resumen-media-general">
                <span>Media del trimestre</span>
                <?php if ($mediaGeneral !== null): ?>
                    <span class="nota-grande"><?= number_format($mediaGeneral, 2) ?></span>
                <?php else: ?>
                    <span class="nota-grande">--</span>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Grid de asignaturas -->
        <section aria-label="Asignaturas">
            <div class="row g-3">
                <?php foreach ($asignaturas as $asig): ?>
                <div class="col-12 col-sm-6">
                    <article class="card tarjeta-asignatura h-100">
                        <div class="card-body d-flex flex-column p-4">
                            <header class="card-cabecera">
                                <h2 class="h5 mb-0">
                                    <a href="index.php?accion=detalle&id=<?= $asig['id'] ?>"
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($asig['nombre']) ?>
                                    </a>
                                </h2>
                            </header>

                            <div class="nota-final-caja my-3">
                                <span class="text-muted small fw-semibold">Nota final</span>
                                <?php if ($asig['media'] !== null): ?>
                                    <span class="nota-grande <?= claseColorNota($asig['media']) ?>">
                                        <?= number_format($asig['media'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="nota-grande text-muted">--</span>
                                <?php endif; ?>
                            </div>

                            <footer class="mt-auto d-flex gap-2">
                                <a href="index.php?accion=detalle&id=<?= $asig['id'] ?>"
                                   class="btn btn-sm btn-primary flex-grow-1">Ver notas</a>
                                <a href="index.php?accion=borrar&id=<?= $asig['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('¿Borrar esta asignatura y todas sus notas?')">Borrar</a>
                            </footer>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Añadir nueva asignatura colapsable -->
            <details class="new-subject-toggle mt-3">
                <summary class="new-subject-summary">
                    <span class="new-subject-plus">+</span> Nueva asignatura
                </summary>
                <form action="index.php?accion=crear" method="POST" class="d-flex gap-2 mt-3">
                    <input type="text" name="nombre" class="form-control"
                           placeholder="Nombre de la asignatura" required>
                    <button type="submit" class="btn btn-primary text-nowrap">Añadir</button>
                </form>
            </details>

            <?php if (empty($asignaturas)): ?>
            <div class="alert alert-info mt-2">
                <?php if ($cursoId > 0): ?>
                    No hay asignaturas en este curso todavía. ¡Añade la primera!
                <?php else: ?>
                    Ejecuta <code>02_datos_prueba.sql</code> para crear la estructura de titulaciones, cursos y trimestres.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<script src="js/notas.js?v=<?= time() ?>"></script>
</body>
</html>