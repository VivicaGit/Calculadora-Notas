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
    <title>Mis asignaturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container py-5" style="max-width: 680px;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h2 mb-0">Mis asignaturas</h1>
        </div>

        <?php if (!empty($asignaturas)): ?>
            <div class="mb-4">
                <?php foreach ($asignaturas as $asig): ?>
                    <div class="list-group-item bg-white p-3 d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <a href="index.php?accion=detalle&id=<?= $asig['id'] ?>" class="text-decoration-none text-dark fw-bold fs-5">
                                <?= htmlspecialchars($asig['nombre']) ?>
                            </a>
                            <?php if ($asig['media'] !== null): ?>
                                <span class="badge bg-light border fs-6 <?= claseColorNota($asig['media']) ?>">
                                    <?= number_format($asig['media'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light border text-muted fs-6">--</span>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="index.php?accion=editar&id=<?= $asig['id'] ?>" class="btn btn-outline-secondary">Editar</a>
                            <a href="index.php?accion=borrar&id=<?= $asig['id'] ?>"
                               class="btn btn-outline-danger"
                               onclick="return confirm('¿Borrar esta asignatura y todas sus notas?')">
                               Borrar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-4">No hay asignaturas registradas todavía.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-4">
                <h2 class="h5 card-title mb-3">Añadir asignatura</h2>
                <form action="index.php?accion=crear" method="POST" class="row g-2">
                    <div class="col-8">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de la asignatura" required>
                    </div>
                    <div class="col-4 d-grid">
                        <button type="submit" class="btn btn-primary">Añadir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>