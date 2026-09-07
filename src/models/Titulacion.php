<?php
class Titulacion
{
    public static function obtenerPorUsuario(int $usuarioId): array
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM titulaciones WHERE usuario_id = :usuario_id ORDER BY nombre");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function obtenerUna(int $id): array
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre, usuario_id FROM titulaciones WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch();
        return $fila ?: [];
    }

    public static function crear(int $usuarioId, string $nombre): int
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("INSERT INTO titulaciones (usuario_id, nombre) VALUES (:usuario_id, :nombre)");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    public static function borrar(int $id): void
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM titulaciones WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

