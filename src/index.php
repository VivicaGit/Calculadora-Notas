<?php
require __DIR__ . '/autoload.php';

$controlador = $_GET['controlador'] ?? 'asignatura';

if ($controlador === 'categoria') {
    require __DIR__ . '/controllers/CategoriaController.php';
} elseif ($controlador === 'nota') {
    require __DIR__ . '/controllers/NotaController.php';
} else {
    require __DIR__ . '/controllers/AsignaturaController.php';
}