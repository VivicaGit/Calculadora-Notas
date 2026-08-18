<?php
// $_GET['accion'] nos dice qué queremos hacer. Si no viene nada, listamos.
$accion = $_GET['accion'] ?? 'listar';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        Asignatura::crear($nombre);
    }
    // Tras un POST redirigimos con GET, así si recargamos la página
    // no se reenvía el formulario duplicado.
    header('Location: index.php');
    exit;
}

if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nombreNuevo = trim($_POST['nombre'] ?? '');
    if ($id > 0 && $nombreNuevo !== '') {
        Asignatura::editar($id, $nombreNuevo);
    }
    header('Location: index.php');
    exit;
}

if ($accion === 'editar' && isset($_GET['id'])) {
    // Aquí solo mostramos el formulario ya relleno con el nombre actual;
    // el guardado de verdad pasa por el bloque POST de arriba.
    $asignatura = Asignatura::obtenerUna((int) $_GET['id']);
    require __DIR__ . '/../views/editar.php';
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Asignatura::borrar((int) $_GET['id']);
    header('Location: index.php');
    exit;
}

// Acción por defecto: listar
$asignaturas = Asignatura::obtenerTodas();
require __DIR__ . '/../views/listar.php';