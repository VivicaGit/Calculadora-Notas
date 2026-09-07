<?php

$accion    = $_GET['accion'] ?? '';
$usuarioId = 1; // fijo hasta que haya login real

// ── Titulaciones ──────────────────────────────────────────────────────────────

if ($accion === 'crear-titulacion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        Titulacion::crear($usuarioId, $nombre);
    }
    header('Location: index.php');
    exit;
}

if ($accion === 'borrar-titulacion' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        Titulacion::borrar($id);
        // Si era la titulación activa en sesión, la limpio
        if (isset($_SESSION['titulacion_id']) && (int) $_SESSION['titulacion_id'] === $id) {
            unset($_SESSION['titulacion_id'], $_SESSION['curso_id'], $_SESSION['trimestre_id']);
        }
    }
    header('Location: index.php');
    exit;
}

// ── Cursos ────────────────────────────────────────────────────────────────────

if ($accion === 'crear-curso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre       = trim($_POST['nombre'] ?? '');
    $titulacionId = (int) ($_POST['titulacion_id'] ?? 0);
    $trimestres   = max(1, min(6, (int) ($_POST['trimestres'] ?? 3)));

    if ($nombre !== '' && $titulacionId > 0) {
        $cursoId = Curso::crear($titulacionId, $nombre);
        Trimestre::crearVarios($cursoId, $trimestres);
    }
    header('Location: index.php');
    exit;
}

if ($accion === 'borrar-curso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre_confirmacion'] ?? '');

    if ($id > 0 && $nombre !== '') {
        $curso = Curso::obtenerUno($id);
        // Solo borro si el nombre escrito coincide exactamente
        if (!empty($curso) && $curso['nombre'] === $nombre) {
            Curso::borrar($id);
            if (isset($_SESSION['curso_id']) && (int) $_SESSION['curso_id'] === $id) {
                unset($_SESSION['curso_id'], $_SESSION['trimestre_id']);
            }
        }
    }
    header('Location: index.php');
    exit;
}

// ── Exportar curso como JSON ──────────────────────────────────────────────────

if ($accion === 'exportar-curso') {
    $cursoId = (int) ($_GET['curso_id'] ?? 0);
    if ($cursoId <= 0) {
        header('Location: index.php');
        exit;
    }

    $curso       = Curso::obtenerUno($cursoId);
    $titulacion  = !empty($curso) ? Titulacion::obtenerUna((int) $curso['titulacion_id']) : [];
    $trimestres  = Trimestre::obtenerPorCurso($cursoId);
    $asignaturas = Asignatura::obtenerTodas($cursoId);

    foreach ($asignaturas as &$asig) {
        $categorias = Categoria::obtenerPorAsignatura((int) $asig['id']);
        foreach ($categorias as &$cat) {
            $notasPorTrimestre = [];
            foreach ($trimestres as $t) {
                $notas = Nota::obtenerPorCategoria((int) $cat['id'], (int) $t['id']);
                if (!empty($notas)) {
                    $notasPorTrimestre[$t['nombre']] = array_column($notas, 'valor');
                }
            }
            $cat['notas_por_trimestre'] = $notasPorTrimestre;
            unset($cat['media']); // campo calculado, no lo exporto
        }
        unset($cat);
        $asig['categorias'] = $categorias;
    }
    unset($asig);

    $datos = [
        'version'     => 1,
        'exportado_en' => date('Y-m-d H:i:s'),
        'titulacion'  => $titulacion['nombre'] ?? '',
        'curso'       => $curso['nombre'] ?? '',
        'trimestres'  => array_column($trimestres, 'nombre'),
        'asignaturas' => $asignaturas,
    ];

    $nombreFichero = 'notas-' . preg_replace('/[^a-z0-9]/i', '-', $curso['nombre'] ?? 'curso') . '.json';

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nombreFichero . '"');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// ── Importar curso desde JSON ─────────────────────────────────────────────────

if ($accion === 'importar-curso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $modo = $_POST['modo'] ?? ''; // 'nuevo' o 'existente'

    // Primero: si acabo de subir el fichero pero aún no se ha elegido destino,
    // guardo el JSON en sesión y muestro la vista de elección
    if (isset($_FILES['fichero']) && $_FILES['fichero']['error'] === UPLOAD_ERR_OK) {
        $contenido = file_get_contents($_FILES['fichero']['tmp_name']);
        $datos     = json_decode($contenido, true);

        if (!$datos || !isset($datos['asignaturas'], $datos['curso'])) {
            $_SESSION['error_importar'] = 'El fichero no tiene el formato esperado.';
            header('Location: index.php');
            exit;
        }

        $_SESSION['json_importar'] = $contenido;
        // Muestro vista de elección de destino
        $titulaciones = Titulacion::obtenerPorUsuario($usuarioId);
        $todosCursos  = [];
        foreach ($titulaciones as $t) {
            $cursos = Curso::obtenerPorTitulacion((int) $t['id']);
            foreach ($cursos as $c) {
                $todosCursos[] = ['id' => $c['id'], 'label' => $t['nombre'] . ' › ' . $c['nombre']];
            }
        }
        require __DIR__ . '/../views/importar.php';
        exit;
    }

    // Segundo paso: el usuario ya eligió qué hacer con el JSON guardado en sesión
    $contenido = $_SESSION['json_importar'] ?? '';
    if (!$contenido) {
        header('Location: index.php');
        exit;
    }
    $datos = json_decode($contenido, true);
    unset($_SESSION['json_importar']);

    $pdo = Gestor::getConexion();

    if ($modo === 'nuevo') {
        $titulacionId = (int) ($_POST['titulacion_id'] ?? 0);
        $nombreCurso  = trim($_POST['nombre_curso'] ?? $datos['curso']);
        if ($titulacionId <= 0 || $nombreCurso === '') {
            header('Location: index.php');
            exit;
        }
        $cursoId = Curso::crear($titulacionId, $nombreCurso);
        // Creo los trimestres con los nombres del JSON
        $nombresTrimestres = $datos['trimestres'] ?? [];
        foreach ($nombresTrimestres as $orden => $nombreT) {
            $stmt = $pdo->prepare(
                "INSERT INTO trimestres (curso_id, nombre, orden) VALUES (:curso_id, :nombre, :orden)"
            );
            $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombreT);
            $stmt->bindValue(':orden', $orden + 1, PDO::PARAM_INT);
            $stmt->execute();
        }
    } else {
        // Cargo en curso existente: borro asignaturas actuales y meto las del JSON
        $cursoId = (int) ($_POST['curso_id'] ?? 0);
        if ($cursoId <= 0) {
            header('Location: index.php');
            exit;
        }
        // El cascade se encarga de notas y categorías al borrar las asignaturas
        $stmt = $pdo->prepare("DELETE FROM asignaturas WHERE curso_id = :curso_id");
        $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Obtengo los trimestres del curso destino indexados por nombre
    $trimestresDestino = Trimestre::obtenerPorCurso($cursoId);
    $trimestreMap = [];
    foreach ($trimestresDestino as $t) {
        $trimestreMap[$t['nombre']] = (int) $t['id'];
    }

    // Inserto asignaturas, categorías y notas
    foreach ($datos['asignaturas'] as $asigData) {
        $asigId = Asignatura::crear(trim($asigData['nombre']), $cursoId);

        foreach ($asigData['categorias'] as $catData) {
            $stmt = $pdo->prepare(
                "INSERT INTO categorias (asignatura_id, nombre, peso, tipo) VALUES (:asig, :nombre, :peso, :tipo)"
            );
            $stmt->bindValue(':asig', $asigId, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $catData['nombre']);
            $stmt->bindValue(':peso', $catData['peso']);
            $stmt->bindValue(':tipo', $catData['tipo']);
            $stmt->execute();
            $catId = (int) $pdo->lastInsertId();

            foreach ($catData['notas_por_trimestre'] as $nombreT => $valores) {
                if (!isset($trimestreMap[$nombreT])) continue;
                $trimestreId = $trimestreMap[$nombreT];
                foreach ($valores as $valor) {
                    $stmt = $pdo->prepare(
                        "INSERT INTO notas (categoria_id, trimestre_id, valor) VALUES (:cat, :tri, :valor)"
                    );
                    $stmt->bindValue(':cat', $catId, PDO::PARAM_INT);
                    $stmt->bindValue(':tri', $trimestreId, PDO::PARAM_INT);
                    $stmt->bindValue(':valor', $valor);
                    $stmt->execute();
                }
            }
        }
    }

    // Dejo activo el curso importado
    $_SESSION['curso_id']     = $cursoId;
    $_SESSION['trimestre_id'] = !empty($trimestresDestino) ? (int) $trimestresDestino[0]['id'] : 0;

    header('Location: index.php');
    exit;
}

// Si llega aquí sin acción reconocida, vuelve al inicio
header('Location: index.php');
exit;
