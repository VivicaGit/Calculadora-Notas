<?php
class Plantilla
{
    // Devuelve todas las plantillas creadas por el usuario
    public static function obtenerPorUsuario(int $usuarioId): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM plantillas WHERE usuario_id = :usuario_id ORDER BY nombre");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Devuelve las categorías que componen una plantilla
    public static function obtenerCategorias(int $plantillaId): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre, peso, tipo FROM plantilla_categorias WHERE plantilla_id = :plantilla_id ORDER BY id");
        $stmt->bindValue(':plantilla_id', $plantillaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Guarda la configuración de categorías de una asignatura como una nueva plantilla
    public static function crearDesdeAsignatura(int $usuarioId, string $nombre, int $asignaturaId): void
    {
        $pdo = Gestor::getConexion();

        // 1. Insertamos la cabecera de la plantilla
        $stmt = $pdo->prepare("INSERT INTO plantillas (usuario_id, nombre) VALUES (:usuario_id, :nombre)");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $plantillaId = (int) $pdo->lastInsertId();

        // 2. Leemos las categorías actuales de la asignatura
        $stmtCats = $pdo->prepare("SELECT nombre, peso, tipo FROM categorias WHERE asignatura_id = :asignatura_id");
        $stmtCats->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
        $stmtCats->execute();
        $categorias = $stmtCats->fetchAll();

        // 3. Las copiamos en la tabla plantilla_categorias
        $stmtInsert = $pdo->prepare(
            "INSERT INTO plantilla_categorias (plantilla_id, nombre, peso, tipo) VALUES (:plantilla_id, :nombre, :peso, :tipo)"
        );

        foreach ($categorias as $cat) {
            $stmtInsert->bindValue(':plantilla_id', $plantillaId, PDO::PARAM_INT);
            $stmtInsert->bindValue(':nombre', $cat['nombre'], PDO::PARAM_STR);
            $stmtInsert->bindValue(':peso', $cat['peso']);
            $stmtInsert->bindValue(':tipo', $cat['tipo'], PDO::PARAM_STR);
            $stmtInsert->execute();
        }
    }

    // Copia las categorías de una plantilla dentro de una asignatura
    public static function aplicarAAsignatura(int $plantillaId, int $asignaturaId): void
    {
        $pdo = Gestor::getConexion();

        // Leemos las categorías de la plantilla
        $stmt = $pdo->prepare("SELECT nombre, peso, tipo FROM plantilla_categorias WHERE plantilla_id = :plantilla_id");
        $stmt->bindValue(':plantilla_id', $plantillaId, PDO::PARAM_INT);
        $stmt->execute();
        $categoriasPlantilla = $stmt->fetchAll();

        // Las insertamos en la asignatura
        $stmtInsert = $pdo->prepare(
            "INSERT INTO categorias (asignatura_id, nombre, peso, tipo) VALUES (:asignatura_id, :nombre, :peso, :tipo)"
        );

        foreach ($categoriasPlantilla as $cat) {
            $stmtInsert->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
            $stmtInsert->bindValue(':nombre', $cat['nombre'], PDO::PARAM_STR);
            $stmtInsert->bindValue(':peso', $cat['peso']);
            $stmtInsert->bindValue(':tipo', $cat['tipo'], PDO::PARAM_STR);
            $stmtInsert->execute();
        }
    }

    // Borra una plantilla
    public static function borrar(int $id): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM plantillas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
