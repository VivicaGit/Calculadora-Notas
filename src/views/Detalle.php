<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($asignatura['nombre']) ?></title>
</head>
<body>
    <a href="index.php">&larr; Mis asignaturas</a>
    <h1><?= htmlspecialchars($asignatura['nombre']) ?></h1>

    <h2>Categorías de evaluación</h2>
    <ul>
        <?php foreach ($categorias as $cat): ?>
            <li>
                <form action="index.php?accion=editar&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria" method="POST" style="display:inline">
                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                    <input type="text" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" required>
                    <input type="number" name="peso" value="<?= $cat['peso'] ?>" min="1" max="100" required>
                    <button type="submit">Guardar</button>
                </form>
                <a href="index.php?accion=borrar&id=<?= $cat['id'] ?>&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria"
                   onclick="return confirm('¿Borrar esta categoría?')">
                   borrar
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($sumaPesos == 100): ?>
        <p>✅ Los pesos suman 100%</p>
    <?php else: ?>
        <p>⚠️ Los pesos suman <?= $sumaPesos ?>% (faltan <?= 100 - $sumaPesos ?>%)</p>
    <?php endif; ?>

    <form action="index.php?accion=crear&asignatura_id=<?= $asignatura['id'] ?>&controlador=categoria" method="POST">
        <input type="text" name="nombre" placeholder="Nombre categoría (ej: Examen)" required>
        <input type="number" name="peso" placeholder="Peso %" min="1" max="100" required>
        <button type="submit">Añadir categoría</button>
    </form>
</body>
</html>