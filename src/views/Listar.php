<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis notas</title>
</head>
<body>
    <h1>Mis asignaturas</h1>

    <ul>
        <?php foreach ($asignaturas as $asig): ?>
            <li>
                <a href="index.php?accion=detalle&id=<?= $asig['id'] ?>"><?= htmlspecialchars($asig['nombre']) ?></a>
                <a href="index.php?accion=editar&id=<?= $asig['id'] ?>">editar</a>
                <a href="index.php?accion=borrar&id=<?= $asig['id'] ?>"
                   onclick="return confirm('¿Borrar esta asignatura y todas sus notas?')">
                   borrar
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (empty($asignaturas)): ?>
        <p>Aún no tienes asignaturas.</p>
    <?php endif; ?>

    <form action="index.php?accion=crear" method="POST">
        <input type="text" name="nombre" placeholder="Nombre de la asignatura" required>
        <button type="submit">Añadir</button>
    </form>
</body>
</html>