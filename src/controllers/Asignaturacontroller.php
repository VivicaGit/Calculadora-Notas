<?php

$accion    = $_GET['accion'] ?? 'listar';
$usuarioId = 1; // fijo hasta que haya login real

// Cambio de selector: solo actualiza la sesión y redirige, sin tocar la BD
if ($accion === 'seleccionar') {
    $tipo = $_GET['tipo'] ?? '';
    $id   = (int) ($_GET['id'] ?? 0);

    if ($tipo === 'titulacion' && $id > 0) {
        $_SESSION['titulacion_id'] = $id;
        unset($_SESSION['curso_id'], $_SESSION['trimestre_id']);
    } elseif ($tipo === 'curso' && $id > 0) {
        $_SESSION['curso_id'] = $id;
        unset($_SESSION['trimestre_id']);
    } elseif ($tipo === 'trimestre' && $id > 0) {
        $_SESSION['trimestre_id'] = $id;
    }

    header('Location: index.php');
    exit;
}

// Acciones que no necesitan el selector (detalle, editar, borrar)
if ($accion === 'detalle' && isset($_GET['id'])) {
    $asignaturaId    = (int) $_GET['id'];
    $trimestreId     = (int) ($_SESSION['trimestre_id'] ?? 1);
    $asignatura      = Asignatura::obtenerUna($asignaturaId);
    $categorias      = Categoria::obtenerPorAsignatura($asignaturaId);
    foreach ($categorias as &$cat) {
        $cat['notas'] = Nota::obtenerPorCategoria((int) $cat['id'], $trimestreId);
        $cat['media'] = Categoria::calcularMedia($cat['notas']);
    }
    unset($cat);
    $mediaAsignatura = Asignatura::calcularMedia($categorias);
    $sumaPesos       = Categoria::sumaPesos($asignaturaId);
    require __DIR__ . '/../views/detalle.php';
    exit;
}

if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int) ($_POST['id'] ?? 0);
    $nombreNuevo = trim($_POST['nombre'] ?? '');
    if ($id > 0 && $nombreNuevo !== '') {
        Asignatura::editar($id, $nombreNuevo);
    }
    header('Location: index.php');
    exit;
}

if ($accion === 'borrar' && isset($_GET['id'])) {
    Asignatura::borrar((int) $_GET['id']);
    header('Location: index.php');
    exit;
}

// A partir de aquí: acciones que necesitan el selector resuelto (crear, listar)

// Resolver titulación activa
$titulaciones = Titulacion::obtenerPorUsuario($usuarioId);
if (!isset($_SESSION['titulacion_id']) && !empty($titulaciones)) {
    $_SESSION['titulacion_id'] = (int) $titulaciones[0]['id'];
}
$titulacionId = (int) ($_SESSION['titulacion_id'] ?? 0);

// Resolver curso activo
$cursos = $titulacionId ? Curso::obtenerPorTitulacion($titulacionId) : [];
if (!isset($_SESSION['curso_id']) && !empty($cursos)) {
    $_SESSION['curso_id'] = (int) $cursos[0]['id'];
}
$cursoId = (int) ($_SESSION['curso_id'] ?? 0);

// Resolver trimestre activo
$trimestres = $cursoId ? Trimestre::obtenerPorCurso($cursoId) : [];
if (!isset($_SESSION['trimestre_id']) && !empty($trimestres)) {
    $_SESSION['trimestre_id'] = (int) $trimestres[0]['id'];
}
$trimestreId = (int) ($_SESSION['trimestre_id'] ?? 0);

// Nombre del trimestre activo (para el panel resumen)
$trimestreNombre = '';
foreach ($trimestres as $t) {
    if ((int) $t['id'] === $trimestreId) {
        $trimestreNombre = $t['nombre'];
        break;
    }
}

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '' && $cursoId > 0) {
        Asignatura::crear($nombre, $cursoId);
    }
    header('Location: index.php');
    exit;
}

// Listar (acción por defecto)
$asignaturas = Asignatura::obtenerTodas($cursoId);
foreach ($asignaturas as &$asig) {
    $cats = Categoria::obtenerPorAsignatura((int) $asig['id']);
    foreach ($cats as &$c) {
        $notas      = Nota::obtenerPorCategoria((int) $c['id'], $trimestreId);
        $c['media'] = Categoria::calcularMedia($notas);
    }
    unset($c);
    $asig['media'] = Asignatura::calcularMedia($cats);
}
unset($asig);

// Media general del trimestre: media aritmética de las notas finales con datos
$suma   = 0;
$conteo = 0;
foreach ($asignaturas as $asig) {
    if ($asig['media'] !== null) {
        $suma += $asig['media'];
        $conteo++;
    }
}
$mediaGeneral = $conteo > 0 ? $suma / $conteo : null;

require __DIR__ . '/../views/listar.php';