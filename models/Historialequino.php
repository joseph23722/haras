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

            // Si todo fue bien
            return ["status" => "success", "message" => "Historial registrado exitosamente."];
        } catch (PDOException $e) {
            // Verificamos si el error es el lanzado por MySQL con SQLSTATE 45000
            if ($e->getCode() == '45000') {
                // Captura el mensaje lanzado por el procedimiento almacenado
                return ["status" => "error", "message" => $e->getMessage()];
            } else {
                return ["status" => "error", "message" => "Error al registrar el historial: " . $e->getMessage()];
            }
        }
    }
}
