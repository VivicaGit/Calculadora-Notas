<?php

$asignaturaId = (int) ($_GET['asignatura_id'] ?? 0);
if ($asignaturaId <= 0) {
    header('Location: index.php');
    exit;
}

$accion = $_GET['accion'] ?? 'ver';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $peso = (float) ($_POST['peso'] ?? 0);
    if ($nombre !== '' && $peso > 0) {
        Categoria::crear($asignaturaId, $nombre, $peso);
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $peso = (float) ($_POST['peso'] ?? 0);
    if ($id > 0 && $nombre !== '' && $peso > 0) {
        Categoria::editar($id, $nombre, $peso);
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Categoria::borrar((int) $_GET['id']);
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}