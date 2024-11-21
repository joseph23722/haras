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

    // Obtener todas las fotografías de un equino por su idEquino
    public function ObtenerFotografiasEquino($idEquino)
    {
        try {
            // Llamar al procedimiento almacenado para obtener las fotografías del equino
            $sql = "CALL spu_listar_fotografias_equinos(?)";
            $stmt = $this->pdo->prepare($sql);

            // Vincular el parámetro (idEquino) con el procedimiento almacenado
            $stmt->bindParam(1, $idEquino, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt->execute();

            // Recuperar todas las filas (fotografías) asociadas al idEquino
            $fotografias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar si se encontraron resultados
            if ($fotografias) {
                return ["status" => "success", "fotografias" => $fotografias];
            } else {
                return ["status" => "error", "message" => "No se encontraron fotografías para este equino."];
            }
        } catch (PDOException $e) {
            // En caso de error, devolver el mensaje
            return ["status" => "error", "message" => "Error al obtener las fotografías: " . $e->getMessage()];
        }
    }
}
