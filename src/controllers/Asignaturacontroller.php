<?php

$accion = $_GET['accion'] ?? 'listar';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        Asignatura::crear($nombre);
    }

    header('Location: index.php');
    exit;
}

if ($accion === 'detalle' && isset($_GET['id'])) {
    $asignaturaId = (int) $_GET['id'];
    $asignatura = Asignatura::obtenerUna($asignaturaId);
    $categorias = Categoria::obtenerPorAsignatura($asignaturaId);
    foreach ($categorias as &$cat) {
        $cat['notas'] = Nota::obtenerPorCategoria((int) $cat['id']);
        $cat['media'] = Categoria::calcularMedia($cat['notas']);
    }
    unset($cat);
    $mediaAsignatura = Asignatura::calcularMedia($categorias);
    $sumaPesos = Categoria::sumaPesos($asignaturaId);
    require __DIR__ . '/../views/detalle.php';
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

    $asignatura = Asignatura::obtenerUna((int) $_GET['id']);
    require __DIR__ . '/../views/editar.php';
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Asignatura::borrar((int) $_GET['id']);
    header('Location: index.php');
    exit;
}

// por defecto: listar
$asignaturas = Asignatura::obtenerTodas();
foreach ($asignaturas as &$asig) {
    $cats = Categoria::obtenerPorAsignatura((int) $asig['id']);
    foreach ($cats as &$c) {
        $notas = Nota::obtenerPorCategoria((int) $c['id']);
        $c['media'] = Categoria::calcularMedia($notas);
    }
    unset($c);
    $asig['media'] = Asignatura::calcularMedia($cats);
}
unset($asig);
require __DIR__ . '/../views/listar.php';