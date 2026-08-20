<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar asignatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container py-5" style="max-width: 500px;">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h4 card-title mb-4">Editar asignatura</h1>

                <form action="index.php?accion=editar" method="POST">
                    <input type="hidden" name="id" value="<?= $asignatura['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="nombre" class="form-label text-muted fw-semibold">Nombre de la asignatura</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($asignatura['nombre']) ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Guardar</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>