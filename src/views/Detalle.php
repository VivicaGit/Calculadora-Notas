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
    <title><?= htmlspecialchars($asignatura['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container py-5" style="max-width: 760px;">
        <a href="index.php" class="text-decoration-none text-secondary d-inline-flex align-items-center mb-3 fw-semibold">
            &larr; Mis asignaturas
        </a>
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h2 mb-0"><?= htmlspecialchars($asignatura['nombre']) ?></h1>
        </div>

        <!-- Recuadro de Nota Final -->
        <div class="caja-media-final d-flex justify-content-between align-items-center mb-4">
            <span class="fs-5 fw-bold text-dark">Nota final del trimestre</span>
            <?php if ($mediaAsignatura !== null): ?>
                <span class="fs-2 <?= claseColorNota($mediaAsignatura) ?>">
                    <?= number_format($mediaAsignatura, 2) ?>
                </span>
            <?php else: ?>
                <span class="fs-5 text-muted">--</span>
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

        <!-- Listado de categorías y notas -->
        <h2 class="h5 text-muted fw-bold mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">Categorías de evaluación</h2>

        <?php if (!empty($categorias)): ?>
            <div class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($categorias as $cat): ?>
                    <div class="card">
                        <div class="card-body p-4">
                            <!-- Cabecera de la categoría con su media -->
                            <div class="d-flex justify-content-between align-items-center card-header-accent">
                                <h3 class="h5 mb-0 fw-bold">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                    <span class="badge-peso ms-2"><?= $cat['peso'] ?>%</span>
                                </h3>
                                <?php if ($cat['media'] !== null): ?>
                                    <span class="fw-bold <?= claseColorNota($cat['media']) ?>">
                                        Media: <?= number_format($cat['media'], 2) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Notas de la categoría -->
                            <div class="caja-notas mb-3">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted small fw-semibold me-1">Notas:</span>
                                    <?php if (!empty($cat['notas'])): ?>
                                        <?php foreach ($cat['notas'] as $nota): ?>
                                            <span class="chip-nota">
                                                <span><?= number_format($nota['valor'], 2) ?></span>
                                                <a href="index.php?controlador=nota&accion=borrar&id=<?= $nota['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>"
                                                   class="chip-nota-del"
                                                   title="Borrar nota"
                                                   onclick="return confirm('¿Borrar esta nota?')">
                                                    &times;
                                                </a>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic small">Sin notas registradas</span>
                                    <?php endif; ?>

                                    <!-- Formulario añadir nota -->
                                    <form action="index.php?controlador=nota&accion=crear&asignatura_id=<?= $asignatura['id'] ?>" method="POST" class="d-inline-flex gap-1 ms-auto">
                                        <input type="hidden" name="categoria_id" value="<?= $cat['id'] ?>">
                                        <input type="number" name="valor" step="0.01" min="0" max="10" placeholder="0.00" required class="form-control form-control-sm text-center" style="width: 75px;">
                                        <button type="submit" class="btn btn-sm btn-primary">Añadir</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Edición / Borrado de categoría -->
                            <div class="pt-2">
                                <form action="index.php?accion=editar&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria" method="POST" class="row g-2 align-items-center">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <div class="col-sm-5">
                                        <input type="text" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="peso" value="<?= $cat['peso'] ?>" min="1" max="100" class="form-control" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 d-flex gap-2 justify-content-sm-end">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Guardar</button>
                                        <a href="index.php?accion=borrar&id=<?= $cat['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Borrar esta categoría?')">
                                           Borrar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-4">No hay categorías de evaluación configuradas para esta asignatura.</div>
        <?php endif; ?>

        <!-- Formulario para añadir nueva categoría -->
        <div class="card">
            <div class="card-body p-4">
                <h3 class="h5 card-title mb-3">Añadir categoría</h3>
                <form action="index.php?accion=crear&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria" method="POST" class="row g-2">
                    <div class="col-sm-6">
                        <input type="text" name="nombre" placeholder="Nombre (ej: Exámenes, Prácticas)" class="form-control" required>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="number" name="peso" placeholder="Peso" min="1" max="100" class="form-control" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Añadir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>