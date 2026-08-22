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
}
