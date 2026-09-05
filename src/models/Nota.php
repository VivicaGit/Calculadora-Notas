<?php
class Nota
{
    public static function obtenerPorCategoria(int $categoriaId, int $trimestreId = 1): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, categoria_id, trimestre_id, valor, fecha FROM notas WHERE categoria_id = :categoria_id AND trimestre_id = :trimestre_id ORDER BY id");
        $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':trimestre_id', $trimestreId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function crear(int $categoriaId, float $valor, int $trimestreId = 1): int
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("INSERT INTO notas (categoria_id, trimestre_id, valor) VALUES (:categoria_id, :trimestre_id, :valor)");
        $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':trimestre_id', $trimestreId, PDO::PARAM_INT);
        $stmt->bindValue(':valor', $valor);
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    public static function borrar(int $id): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM notas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
