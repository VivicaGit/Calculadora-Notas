<?php
function claseColorNota($nota) {
    if ($nota === null) return 'text-muted';
    if ($nota >= 9) return 'nota-sobresaliente';
    if ($nota >= 7) return 'nota-notable';
    if ($nota >= 5) return 'nota-aprobado';
    if ($nota >= 4) return 'nota-suficiente';
    return 'nota-suspenso';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($asignatura['nombre']) ?> — Calculadora de Notas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="contenedor-app">

    <header class="cabecera-app">
        <span class="cabecera-icono" aria-hidden="true">📊</span>
        <h1 class="cabecera-titulo">Calculadora de Notas</h1>
    </header>

    <main>
        <nav aria-label="Miga de pan">
            <a href="index.php" class="enlace-volver">← Mis asignaturas</a>
        </nav>

        <h2 class="h2 mb-4"><?= htmlspecialchars($asignatura['nombre']) ?></h2>

        <?php if (!empty($_SESSION['aviso_plantilla'])): ?>
            <div class="alert alert-warning py-2 px-3 mb-4">
                <?= htmlspecialchars($_SESSION['aviso_plantilla']) ?>
            </div>
            <?php unset($_SESSION['aviso_plantilla']); ?>
        <?php endif; ?>

        <!-- Nota final del trimestre -->
        <div class="caja-media-final d-flex justify-content-between align-items-center mb-4">
            <span class="fs-5 fw-bold text-dark">Nota final del trimestre</span>
            <?php if ($mediaAsignatura !== null): ?>
                <span id="media-asignatura" class="nota-grande <?= claseColorNota($mediaAsignatura) ?>">
                    <?= number_format($mediaAsignatura, 2) ?>
                </span>
            <?php else: ?>
                <span id="media-asignatura" class="nota-grande text-muted">--</span>
            <?php endif; ?>
        </div>

        <!-- Estado de pesos -->
        <?php if ($sumaPesos == 100): ?>
            <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-4" role="alert">
                <span>Las categorías de evaluación suman el <strong>100%</strong>.</span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-4" role="alert">
                <span>Los pesos actuales suman <strong><?= $sumaPesos ?>%</strong> (faltan <?= 100 - $sumaPesos ?>%).</span>
            </div>
        <?php endif; ?>

        <!-- Categorías de evaluación -->
        <section aria-label="Categorías de evaluación">
            <h3 class="h5 text-muted fw-bold mb-3 text-uppercase"
                style="font-size: 0.85rem; letter-spacing: 0.05em;">Categorías de evaluación</h3>

            <?php if (!empty($categorias)): ?>
            <div class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($categorias as $cat): ?>
                <article class="card categoria-card"
                         data-id="<?= $cat['id'] ?>"
                         data-tipo="<?= $cat['tipo'] ?>"
                         data-peso="<?= $cat['peso'] ?>"
                         data-media-actual="<?= $cat['media'] !== null ? $cat['media'] : '' ?>">
                    <div class="card-body p-4">

                        <!-- Cabecera de la categoría -->
                        <div class="d-flex justify-content-between align-items-start card-header-accent">
                            <div>
                                <h4 class="h5 mb-0 fw-bold">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                    <span class="badge-peso ms-2"><?= $cat['peso'] ?>%</span>
                                    <?php if ($cat['tipo'] === 'unica'): ?>
                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.75rem; font-weight: normal;">Nota única</span>
                                    <?php endif; ?>
                                </h4>
                                <!-- Toggle de editar: solo nombre, peso y tipo -->
                                <details class="detalles-config mt-2">
                                    <summary class="detalles-config-summary">✎ Editar</summary>
                                    <form action="index.php?accion=editar&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria"
                                          method="POST"
                                          class="row g-2 align-items-center mt-2">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <div class="col-sm-4">
                                            <input type="text" name="nombre"
                                                   value="<?= htmlspecialchars($cat['nombre']) ?>"
                                                   class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="peso" value="<?= $cat['peso'] ?>"
                                                       min="1" max="100" class="form-control" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <select name="tipo" class="form-select form-select-sm">
                                                <option value="lista" <?= $cat['tipo'] === 'lista' ? 'selected' : '' ?>>Varias notas</option>
                                                <option value="unica" <?= $cat['tipo'] === 'unica' ? 'selected' : '' ?>>Nota única</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 d-flex justify-content-sm-end">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Guardar</button>
                                        </div>
                                    </form>
                                </details>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($cat['media'] !== null): ?>
                                    <span class="media-categoria fw-bold <?= claseColorNota($cat['media']) ?>">
                                        <?= $cat['tipo'] === 'unica' ? 'Nota: ' : 'Media: ' ?><?= number_format($cat['media'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="media-categoria fw-bold"></span>
                                <?php endif; ?>
                                <a href="index.php?accion=borrar&id=<?= $cat['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria"
                                   class="text-danger text-decoration-none"
                                   style="font-size: 1.1rem; line-height: 1;"
                                   title="Borrar categoría"
                                   onclick="return confirm('¿Borrar la categoría «<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>»? Se perderán todas sus notas.')">&times;</a>
                            </div>
                        </div>

                        <!-- Notas de la categoría -->
                        <div class="caja-notas mb-3">
                            <div class="chips-notas d-flex flex-wrap align-items-center gap-2">
                                <?php if ($cat['tipo'] === 'unica'): ?>
                                    <span class="text-muted small fw-semibold me-1">Nota:</span>
                                    <?php if (!empty($cat['notas'])): ?>
                                        <?php $notaUnica = $cat['notas'][0]; ?>
                                        <span class="chip-nota" data-valor="<?= $notaUnica['valor'] ?>">
                                            <span><?= number_format($notaUnica['valor'], 2) ?></span>
                                            <a href="index.php?controlador=nota&accion=borrar&id=<?= $notaUnica['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>"
                                               class="chip-nota-del"
                                               title="Borrar nota">&times;</a>
                                        </span>
                                        <form action="index.php?controlador=nota&accion=crear&asignatura_id=<?= $asignatura['id'] ?>"
                                              method="POST"
                                              class="form-nota-crear d-inline-flex gap-1 ms-auto"
                                              style="display:none !important;">
                                            <input type="hidden" name="categoria_id" value="<?= $cat['id'] ?>">
                                            <input type="hidden" name="trimestre_id" value="<?= $trimestreId ?>">
                                            <input type="number" name="valor" step="0.01" min="0" max="10"
                                                   placeholder="0.00" required
                                                   class="form-control form-control-sm text-center" style="width: 75px;">
                                            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="sin-notas text-muted fst-italic small">Sin nota registrada</span>
                                        <form action="index.php?controlador=nota&accion=crear&asignatura_id=<?= $asignatura['id'] ?>"
                                              method="POST"
                                              class="form-nota-crear d-inline-flex gap-1 ms-auto">
                                            <input type="hidden" name="categoria_id" value="<?= $cat['id'] ?>">
                                            <input type="hidden" name="trimestre_id" value="<?= $trimestreId ?>">
                                            <input type="number" name="valor" step="0.01" min="0" max="10"
                                                   placeholder="0.00" required
                                                   class="form-control form-control-sm text-center" style="width: 75px;">
                                            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small fw-semibold me-1">Notas:</span>
                                    <?php if (!empty($cat['notas'])): ?>
                                        <?php foreach ($cat['notas'] as $nota): ?>
                                            <span class="chip-nota" data-valor="<?= $nota['valor'] ?>">
                                                <span><?= number_format($nota['valor'], 2) ?></span>
                                                <a href="index.php?controlador=nota&accion=borrar&id=<?= $nota['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>"
                                                   class="chip-nota-del"
                                                   title="Borrar nota">&times;</a>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="sin-notas text-muted fst-italic small">Sin notas registradas</span>
                                    <?php endif; ?>

                                    <!-- Formulario añadir nota -->
                                    <form action="index.php?controlador=nota&accion=crear&asignatura_id=<?= $asignatura['id'] ?>"
                                          method="POST"
                                          class="form-nota-crear d-inline-flex gap-1 ms-auto">
                                        <input type="hidden" name="categoria_id" value="<?= $cat['id'] ?>">
                                        <input type="hidden" name="trimestre_id" value="<?= $trimestreId ?>">
                                        <input type="number" name="valor" step="0.01" min="0" max="10"
                                               placeholder="0.00" required
                                               class="form-control form-control-sm text-center" style="width: 75px;">
                                        <button type="submit" class="btn btn-sm btn-primary">Añadir</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-4">
                <p class="mb-0">No hay categorías de evaluación configuradas para esta asignatura.</p>
                <?php if (!empty($plantillas)): ?>
                    <form action="index.php?controlador=plantilla&accion=aplicar&asignatura_id=<?= $asignatura['id'] ?>"
                          method="POST"
                          class="d-flex flex-wrap gap-2 align-items-center mt-3 pt-2 border-top">
                        <span class="small fw-semibold">¿Aplicar una plantilla existente?</span>
                        <select name="plantilla_id" class="form-select form-select-sm w-auto" required>
                            <?php foreach ($plantillas as $pl): ?>
                                <option value="<?= $pl['id'] ?>"><?= htmlspecialchars($pl['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Aplicar plantilla</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Añadir categoría -->
        <section aria-label="Añadir categoría" class="mb-4">
            <div class="card">
                <div class="card-body p-4">
                    <h3 class="h5 card-title mb-3">Añadir categoría</h3>
                    <form action="index.php?accion=crear&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria"
                          method="POST"
                          class="row g-2">
                        <div class="col-sm-5">
                            <input type="text" name="nombre"
                                   placeholder="Nombre (ej: Exámenes, Prácticas)"
                                   class="form-control" required>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group">
                                <input type="number" name="peso" placeholder="Peso"
                                       min="1" max="100" class="form-control" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select name="tipo" class="form-select">
                                <option value="lista">Varias notas (media)</option>
                                <option value="unica">Nota única (fija)</option>
                            </select>
                        </div>
                        <div class="col-sm-2 d-grid">
                            <button type="submit" class="btn btn-primary">Añadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Plantillas de pesos -->
        <section aria-label="Plantillas de pesos">
            <details class="detalles-config">
                <summary class="detalles-config-summary text-secondary fw-semibold">
                    📋 Plantillas de pesos (<?= count($plantillas) ?> disponibles)
                </summary>
                <div class="card mt-2">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <!-- Aplicar plantilla -->
                            <div class="col-md-6">
                                <h4 class="h6 fw-bold mb-2">Aplicar plantilla a esta asignatura</h4>
                                <?php if (!empty($plantillas)): ?>
                                    <form action="index.php?controlador=plantilla&accion=aplicar&asignatura_id=<?= $asignatura['id'] ?>"
                                          method="POST"
                                          class="d-flex gap-2 align-items-center">
                                        <select name="plantilla_id" class="form-select form-select-sm" required>
                                            <option value="">-- Seleccionar plantilla --</option>
                                            <?php foreach ($plantillas as $pl): ?>
                                                <option value="<?= $pl['id'] ?>"><?= htmlspecialchars($pl['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-primary text-nowrap"
                                                <?php if (!empty($categorias)): ?>
                                                onclick="return confirm('Esta asignatura ya tiene categorías. Las de la plantilla se añadirán encima, no se borrarán las existentes. ¿Continuar?')"
                                                <?php endif; ?>>Aplicar</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-muted small mb-0 fst-italic">No tienes plantillas guardadas todavía.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Guardar configuración actual como plantilla -->
                            <div class="col-md-6">
                                <h4 class="h6 fw-bold mb-2">Guardar categorías actuales como plantilla</h4>
                                <?php if (!empty($categorias)): ?>
                                    <form action="index.php?controlador=plantilla&accion=guardar&asignatura_id=<?= $asignatura['id'] ?>"
                                          method="POST"
                                          class="d-flex gap-2 align-items-center">
                                        <input type="text" name="nombre" placeholder="Nombre (ej: Estándar DAW)"
                                               class="form-control form-control-sm" required>
                                        <button type="submit" class="btn btn-sm btn-outline-success text-nowrap">Guardar</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-muted small mb-0 fst-italic">Añade categorías primero para poder guardarlas como plantilla.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Lista para borrar plantillas -->
                        <?php if (!empty($plantillas)): ?>
                            <hr class="my-3">
                            <div>
                                <span class="text-muted small fw-semibold">Tus plantillas guardadas:</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    <?php foreach ($plantillas as $pl): ?>
                                        <span class="chip-nota">
                                            <span><?= htmlspecialchars($pl['nombre']) ?></span>
                                            <a href="index.php?controlador=plantilla&accion=borrar&id=<?= $pl['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>"
                                               class="chip-nota-del"
                                               title="Borrar plantilla"
                                               onclick="return confirm('¿Borrar esta plantilla?')">&times;</a>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </details>
        </section>
    </main>
</div>
<script src="js/notas.js"></script>
</body>
</html>