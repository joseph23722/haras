<?php
require_once 'Conexion.php';

class Nuevafoto extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Registrar una nueva fotografía de equino
    public function registrarNuevasFotos($data)
    {
        try {
            // Llamar al procedimiento almacenado para insertar una nueva fotografía
            $sql = "CALL spu_nuevas_fotografias_equinos(?, ?)";
            $stmt = $this->pdo->prepare($sql);

            // Recuperar los valores pasados
            $idEquino = $data['idEquino'];
            $publicId = $data['public_id'];

            // Bindear los parámetros para la ejecución
            $stmt->bindParam(1, $idEquino, PDO::PARAM_INT);
            $stmt->bindParam(2, $publicId, PDO::PARAM_STR);

            // Ejecutar la consulta
            $stmt->execute();

            return ["status" => "success", "message" => "Fotografía registrada exitosamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al registrar la fotografía: " . $e->getMessage()];
        }
    }
}