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

    // Genera N trimestres automáticamente al crear un curso
    public static function crearVarios(int $cursoId, int $cantidad): void
    {
        $nombres = ['1er trimestre', '2º trimestre', '3er trimestre', '4º trimestre', '5º trimestre'];
        $pdo     = Gestor::getConexion();
        $stmt    = $pdo->prepare(
            "INSERT INTO trimestres (curso_id, nombre, orden) VALUES (:curso_id, :nombre, :orden)"
        );

        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = isset($nombres[$i]) ? $nombres[$i] : ($i + 1) . 'º trimestre';
            $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':orden', $i + 1, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}

