<?php
session_start();
require __DIR__ . '/autoload.php';

$controlador = $_GET['controlador'] ?? 'asignatura';

if ($controlador === 'categoria') {
    require __DIR__ . '/controllers/CategoriaController.php';
} elseif ($controlador === 'nota') {
    require __DIR__ . '/controllers/NotaController.php';
} elseif ($controlador === 'plantilla') {
    require __DIR__ . '/controllers/PlantillaController.php';
} elseif ($controlador === 'estructura') {
    require __DIR__ . '/controllers/EstructuraController.php';
} else {
    require __DIR__ . '/controllers/AsignaturaController.php';
}