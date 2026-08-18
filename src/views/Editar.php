<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar asignatura</title>
</head>
<body>
    <h1>Editar asignatura</h1>

    <form action="index.php?accion=editar" method="POST">
        <input type="hidden" name="id" value="<?= $asignatura['id'] ?>">
        <input type="text" name="nombre" value="<?= htmlspecialchars($asignatura['nombre']) ?>" required>
        <button type="submit">Guardar</button>
    </form>

    <a href="index.php">Cancelar</a>
</body>
</html>