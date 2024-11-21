<?php
require_once 'Conexion.php';

class HistorialEquino extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Registrar contenido de Quill en Historial de equinos
    public function registrarHistorialEquino($data)
    {
        try {
            $sql = "CALL spu_registrar_historial_equinos(?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $idEquino = $data['idEquino'];
            $descripcion = $data['descripcion'];

            $stmt->bindParam(1, $idEquino);
            $stmt->bindParam(2, $descripcion);

            $stmt->execute();

            return ["status" => "success", "message" => "Historial registrado exitosamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al registrar el historial: " . $e->getMessage()];
        }
    }
}
