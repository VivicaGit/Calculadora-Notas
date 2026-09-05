<?php

$usuarioId = 1; // Fijo hasta tener login real

$asignaturaId = (int) ($_GET['asignatura_id'] ?? $_POST['asignatura_id'] ?? 0);
$accion       = $_GET['accion'] ?? '';

// Guardar las categorías de una asignatura como una nueva plantilla
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '' && $asignaturaId > 0) {
        if (Plantilla::existeNombre($usuarioId, $nombre)) {
            $_SESSION['aviso_plantilla'] = 'Ya tienes una plantilla llamada «' . $nombre . '». Elige otro nombre.';
        } else {
            Plantilla::crearDesdeAsignatura($usuarioId, $nombre, $asignaturaId);
        }
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

// Aplicar una plantilla a la asignatura actual
if ($accion === 'aplicar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $plantillaId = (int) ($_POST['plantilla_id'] ?? 0);
    if ($plantillaId > 0 && $asignaturaId > 0) {
        Plantilla::aplicarAAsignatura($plantillaId, $asignaturaId);
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

// Borrar una plantilla
if ($accion === 'borrar' && isset($_GET['id'])) {
    Plantilla::borrar((int) $_GET['id']);
    if ($asignaturaId > 0) {
        header("Location: index.php?accion=detalle&id=$asignaturaId");
    } else {
        header('Location: index.php');
    }
    exit;
}

if ($asignaturaId > 0) {
    header("Location: index.php?accion=detalle&id=$asignaturaId");
} else {
    header('Location: index.php');
}
exit;
