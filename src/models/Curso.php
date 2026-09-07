<?php
class Curso
{
    public static function obtenerPorTitulacion(int $titulacionId): array
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM cursos WHERE titulacion_id = :titulacion_id ORDER BY orden, nombre");
        $stmt->bindValue(':titulacion_id', $titulacionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerUno(int $id): array
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre, titulacion_id FROM cursos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ?: [];
    }

    public static function crear(int $titulacionId, string $nombre): int
    {
        $pdo = Gestor::getConexion();

        // Calculo el siguiente orden automáticamente
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) + 1 FROM cursos WHERE titulacion_id = :titulacion_id");
        $stmt->bindValue(':titulacion_id', $titulacionId, PDO::PARAM_INT);
        $stmt->execute();
        $orden = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO cursos (titulacion_id, nombre, orden) VALUES (:titulacion_id, :nombre, :orden)");
        $stmt->bindValue(':titulacion_id', $titulacionId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':orden', $orden, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    public static function borrar(int $id): void
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM cursos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

