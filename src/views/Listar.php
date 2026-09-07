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

    <header class="cabecera-app d-flex justify-content-between align-items-center">
        <h1 class="cabecera-titulo">Seguimiento de notas</h1>
        <button id="btn-modo-edicion" class="btn-modo-edicion" title="Modo edición (F2)">✎ Editar</button>
    </header>

    <main>
        <!-- Selector de titulación / curso / trimestre -->
        <nav class="card selector-nav mb-4" aria-label="Selección de trimestre">
            <?php if (!empty($titulaciones)): ?>
            <div class="selector-fila">
                <span class="selector-etiqueta" id="lbl-titulacion">Titulación</span>
                <div class="d-flex flex-wrap gap-2 align-items-center" role="group" aria-labelledby="lbl-titulacion">
                    <?php foreach ($titulaciones as $t): ?>
                        <span class="d-inline-flex align-items-center gap-1">
                            <a href="index.php?accion=seleccionar&tipo=titulacion&id=<?= $t['id'] ?>"
                               class="btn-selector <?= (int) $t['id'] === $titulacionId ? 'btn-selector--activo' : '' ?>"
                               aria-current="<?= (int) $t['id'] === $titulacionId ? 'true' : 'false' ?>">
                                <?= htmlspecialchars($t['nombre']) ?>
                            </a>
                            <button type="button"
                                    class="btn-borrar-estructura d-none"
                                    title="Borrar titulación"
                                    data-tipo="titulacion"
                                    data-id="<?= $t['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($t['nombre'], ENT_QUOTES) ?>"
                                    aria-label="Borrar titulación <?= htmlspecialchars($t['nombre'], ENT_QUOTES) ?>">×</button>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Nueva titulación (solo en modo edición) -->
            <div class="selector-fila d-none fila-nueva-titulacion">
                <span class="selector-etiqueta"></span>
                <details class="new-subject-toggle" style="max-width: 400px;">
                    <summary class="new-subject-summary">
                        <span class="new-subject-plus">+</span> Nueva titulación
                    </summary>
                    <form action="index.php?controlador=estructura&accion=crear-titulacion" method="POST"
                          class="d-flex gap-2 mt-3">
                        <input type="text" name="nombre" class="form-control"
                               placeholder="Ej: DAW, DAM, ASIR" required>
                        <button type="submit" class="btn btn-primary text-nowrap">Añadir</button>
                    </form>
                </details>
            </div>
            <?php endif; ?>


            <?php if (!empty($cursos)): ?>
            <div class="selector-fila">
                <span class="selector-etiqueta" id="lbl-curso">Curso</span>
                <div class="d-flex flex-wrap gap-2 align-items-center" role="group" aria-labelledby="lbl-curso">
                    <?php foreach ($cursos as $c): ?>
                        <span class="d-inline-flex align-items-center gap-1">
                            <a href="index.php?accion=seleccionar&tipo=curso&id=<?= $c['id'] ?>"
                               class="btn-selector <?= (int) $c['id'] === $cursoId ? 'btn-selector--activo' : '' ?>"
                               aria-current="<?= (int) $c['id'] === $cursoId ? 'true' : 'false' ?>">
                                <?= htmlspecialchars($c['nombre']) ?>
                            </a>
                            <a href="index.php?controlador=estructura&accion=exportar-curso&curso_id=<?= $c['id'] ?>"
                               class="btn-exportar-curso d-none"
                               title="Exportar este curso como JSON">Exportar JSON</a>
                            <button type="button"
                                    class="btn-borrar-estructura d-none"
                                    title="Borrar curso"
                                    data-tipo="curso"
                                    data-id="<?= $c['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>"
                                    aria-label="Borrar curso <?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>">×</button>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($titulacionId > 0): ?>
            <!-- Nuevo curso (solo en modo edición) -->
            <div class="selector-fila d-none fila-nuevo-curso">
                <span class="selector-etiqueta"></span>
                <details class="new-subject-toggle" style="max-width: 480px;">
                    <summary class="new-subject-summary">
                        <span class="new-subject-plus">+</span> Nuevo curso
                    </summary>
                    <form action="index.php?controlador=estructura&accion=crear-curso" method="POST"
                          class="row g-2 mt-3">
                        <input type="hidden" name="titulacion_id" value="<?= $titulacionId ?>">
                        <div class="col-sm-5">
                            <input type="text" name="nombre" class="form-control"
                                   placeholder="Ej: 1º, 2º, 1º rep." required>
                        </div>
                        <div class="col-sm-4">
                            <select name="trimestres" class="form-select" title="Número de trimestres">
                                <option value="2">2 trimestres</option>
                                <option value="3" selected>3 trimestres</option>
                                <option value="4">4 trimestres</option>
                                <option value="6">6 trimestres (semestral)</option>
                            </select>
                        </div>
                        <div class="col-sm-3 d-grid">
                            <button type="submit" class="btn btn-primary">Añadir</button>
                        </div>
                    </form>

                </details>
            </div>

            <!-- Importar curso (solo en modo edición) -->
            <div class="selector-fila d-none fila-importar">
                <span class="selector-etiqueta"></span>
                <details class="new-subject-toggle" style="max-width: 480px;">
                    <summary class="new-subject-summary">
                        <span class="new-subject-plus" style="font-size:14px;">↑</span> Importar curso desde JSON
                    </summary>
                    <form action="index.php?controlador=estructura&accion=importar-curso" method="POST"
                          enctype="multipart/form-data"
                          class="d-flex gap-2 mt-3 align-items-center">
                        <input type="file" name="fichero" accept=".json" class="form-control" required>
                        <button type="submit" class="btn btn-primary text-nowrap">Cargar</button>
                    </form>
                </details>
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
                    Activa el modo edición para crear una titulación y un curso.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Modal de confirmación de borrado de titulación o curso -->
<div id="modal-borrar" class="modal-borrar" role="dialog" aria-modal="true" aria-labelledby="modal-borrar-titulo">
    <div class="modal-borrar__panel">
        <p class="modal-borrar__titulo" id="modal-borrar-titulo"></p>
        <p class="modal-borrar__aviso" id="modal-borrar-aviso"></p>
        <a href="#" id="modal-borrar-exportar" class="modal-borrar__exportar" style="display:none;">
            ↓ Exportar primero como JSON
        </a>
        <label class="modal-borrar__label" for="modal-borrar-input" id="modal-borrar-label"></label>
        <input type="text" id="modal-borrar-input" class="form-control mb-1" autocomplete="off">
        <p class="text-muted" style="font-size:12px; margin-bottom:0;" id="modal-borrar-hint"></p>
        <div class="modal-borrar__acciones">
            <button type="button" id="modal-borrar-cancelar" class="btn btn-outline-secondary btn-sm">Cancelar</button>
            <form id="modal-borrar-form" method="POST" style="display:inline;">
                <input type="hidden" name="id" id="modal-borrar-id">
                <input type="hidden" name="nombre_confirmacion" id="modal-borrar-nombre-confirmacion">
                <button type="submit" id="modal-borrar-confirmar"
                        class="btn btn-sm btn-outline-danger" disabled>Borrar</button>
            </form>
        </div>
    </div>
</div>

<script src="js/notas.js?v=<?= time() ?>"></script>
</body>
</html>