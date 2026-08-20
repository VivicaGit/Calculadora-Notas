<?php
class Nota
{
    public static function obtenerPorCategoria(int $categoriaId): array
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, categoria_id, valor, fecha FROM notas WHERE categoria_id = :categoria_id ORDER BY id");
        $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function crear(int $categoriaId, float $valor): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("INSERT INTO notas (categoria_id, valor) VALUES (:categoria_id, :valor)");
        $stmt->bindValue(':categoria_id', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':valor', $valor);
        $stmt->execute();
    }

    public static function borrar(int $id): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("DELETE FROM notas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
