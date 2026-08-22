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
}
