<?php
class Categoria
{
    public static function obtenerPorAsignatura(int $asignaturaId): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, nombre, peso, tipo FROM categorias WHERE asignatura_id = :asignatura_id ORDER BY orden, id");
        $stmt->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function crear(int $asignaturaId, string $nombre, float $peso): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare(
            "INSERT INTO categorias (asignatura_id, nombre, peso) VALUES (:asignatura_id, :nombre, :peso)"
        );
        $stmt->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':peso', $peso);
        $stmt->execute();
    }

    public static function editar(int $id, string $nombreNuevo, float $pesoNuevo): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = :nombre, peso = :peso WHERE id = :id");
        $stmt->bindValue(':nombre', $nombreNuevo, PDO::PARAM_STR);
        $stmt->bindValue(':peso', $pesoNuevo);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function obtenerUna(int $id)
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, asignatura_id, nombre, peso FROM categorias WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function borrar(int $id): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Suma los pesos de una asignatura, para poder avisar si no llegan a 100%.
    public static function sumaPesos(int $asignaturaId): float
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(peso), 0) AS total FROM categorias WHERE asignatura_id = :asignatura_id");
        $stmt->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
        $stmt->execute();
        return (float) $stmt->fetch()['total'];
    }
}