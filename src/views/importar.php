<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar curso — Calculadora de Notas</title>
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
        <a href="index.php" class="enlace-volver">← Volver</a>

        <h2 class="h5 fw-bold mb-1">Importar curso</h2>
        <p class="text-muted small mb-4">
            Fichero: <strong><?= htmlspecialchars($datos['curso']) ?></strong><?php if (!empty($datos['titulacion'])): ?> · <?= htmlspecialchars($datos['titulacion']) ?><?php endif; ?>
        </p>

        <div class="card p-4" style="max-width: 480px;">
            <form action="index.php?controlador=estructura&accion=importar-curso" method="POST">
                <input type="hidden" name="modo" value="nuevo">
                <div class="mb-3">
                    <label class="form-label small fw-semibold" for="nombre_curso">Nombre del curso</label>
                    <input type="text" id="nombre_curso" name="nombre_curso"
                           value="<?= htmlspecialchars($datos['curso']) ?>"
                           class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold" for="titulacion_id">Titulación</label>
                    <select id="titulacion_id" name="titulacion_id" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($titulaciones as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Importar</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
