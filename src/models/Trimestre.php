<?php
class Trimestre
{
    public static function obtenerPorCurso(int $cursoId): array
    {
        $pdo  = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM trimestres WHERE curso_id = :curso_id ORDER BY orden, nombre");
        $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
