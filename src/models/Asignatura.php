<?php
class Asignatura
{
    // trimestre_id fijo a 1 de momento (el que creamos por SQL); el selector
    // real de trimestre en la UI lo añadimos más adelante.
    public static function obtenerTodas(int $trimestreId = 1): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM asignaturas WHERE trimestre_id = :trimestre_id ORDER BY nombre");
        $stmt->bindValue(':trimestre_id', $trimestreId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function crear(string $nombre, int $trimestreId = 1): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("INSERT INTO asignaturas (trimestre_id, nombre) VALUES (:trimestre_id, :nombre)");
        $stmt->bindValue(':trimestre_id', $trimestreId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
    }

    public static function obtenerUna(int $id)
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre FROM asignaturas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function editar(int $id, string $nombreNuevo): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("UPDATE asignaturas SET nombre = :nombre WHERE id = :id");
        $stmt->bindValue(':nombre', $nombreNuevo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function borrar(int $id): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM asignaturas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Calcula la media ponderada de la asignatura según las categorías evaluadas
    public static function calcularMedia(array $categorias)
    {
        $sumaPonderada = 0;
        $pesoTotalEvaluado = 0;

        foreach ($categorias as $cat) {
            if (isset($cat['media']) && $cat['media'] !== null) {
                $sumaPonderada += (float) $cat['media'] * (float) $cat['peso'];
                $pesoTotalEvaluado += (float) $cat['peso'];
            }
        }

        if ($pesoTotalEvaluado == 0) {
            return null;
        }

        return $sumaPonderada / $pesoTotalEvaluado;
    }
}