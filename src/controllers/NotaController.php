<?php

$asignaturaId = (int) ($_GET['asignatura_id'] ?? $_POST['asignatura_id'] ?? 0);
if ($asignaturaId <= 0) {
    header('Location: index.php');
    exit;
}

$accion = $_GET['accion'] ?? '';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $valor = (float) ($_POST['valor'] ?? -1);

    // Validamos que la nota esté dentro del rango 0 a 10
    if ($categoriaId > 0 && $valor >= 0 && $valor <= 10) {
        Nota::crear($categoriaId, $valor);
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Nota::borrar((int) $_GET['id']);
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

header("Location: index.php?accion=detalle&id=$asignaturaId");
exit;
