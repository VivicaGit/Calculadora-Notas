<?php

$asignaturaId = (int) ($_GET['asignatura_id'] ?? $_POST['asignatura_id'] ?? 0);
if ($asignaturaId <= 0) {
    header('Location: index.php');
    exit;
}

$accion  = $_GET['accion'] ?? '';
// Si la petición viene del JS con fetch, respondemos con JSON en vez de redirigir
$esFetch = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $valor       = (float) ($_POST['valor'] ?? -1);
    $trimestreId = (int) ($_POST['trimestre_id'] ?? $_SESSION['trimestre_id'] ?? 1);

    // Validamos que la nota esté dentro del rango 0 a 10
    if ($categoriaId > 0 && $trimestreId > 0 && $valor >= 0 && $valor <= 10) {
        $catInfo = Categoria::obtenerUna($categoriaId);
        $notasExistentes = Nota::obtenerPorCategoria($categoriaId, $trimestreId);
        if ($catInfo && $catInfo['tipo'] === 'unica' && count($notasExistentes) > 0) {
            if ($esFetch) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false]);
                exit;
            }
            header("Location: index.php?accion=detalle&id=$asignaturaId");
            exit;
        }

        $nuevaId = Nota::crear($categoriaId, $valor, $trimestreId);
        if ($esFetch) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'id' => $nuevaId, 'valor' => $valor, 'asignaturaId' => $asignaturaId]);
            exit;
        }
    } elseif ($esFetch) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false]);
        exit;
    }

    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Nota::borrar((int) $_GET['id']);
    if ($esFetch) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }
    header("Location: index.php?accion=detalle&id=$asignaturaId");
    exit;
}

header("Location: index.php?accion=detalle&id=$asignaturaId");
exit;
