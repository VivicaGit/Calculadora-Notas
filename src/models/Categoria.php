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

    public static function crear(int $asignaturaId, string $nombre, float $peso, string $tipo = 'lista'): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare(
            "INSERT INTO categorias (asignatura_id, nombre, peso, tipo) VALUES (:asignatura_id, :nombre, :peso, :tipo)"
        );
        $stmt->bindValue(':asignatura_id', $asignaturaId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':peso', $peso);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
    }

    public static function editar(int $id, string $nombreNuevo, float $pesoNuevo, string $tipoNuevo = 'lista'): void
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("UPDATE categorias SET nombre = :nombre, peso = :peso, tipo = :tipo WHERE id = :id");
        $stmt->bindValue(':nombre', $nombreNuevo, PDO::PARAM_STR);
        $stmt->bindValue(':peso', $pesoNuevo);
        $stmt->bindValue(':tipo', $tipoNuevo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function obtenerUna(int $id)
    {
        $pdo = Gestor::getConexion();
        $stmt = $pdo->prepare("SELECT id, asignatura_id, nombre, peso, tipo FROM categorias WHERE id = :id");
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

    // Calcula la media aritmética de una lista de notas
    public static function calcularMedia(array $notas)
    {
        if (empty($notas)) {
            return null;
        }

        $suma = 0;
        foreach ($notas as $n) {
            $suma += (float) $n['valor'];
        }

        return $suma / count($notas);
    }
}