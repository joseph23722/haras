<?php
require_once 'Conexion.php';

class RegistrarPropietario extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Función para registrar un nuevo propietario
    public function registrarPropietario($nombreHaras)
    {
        try {
            // Llamar al procedimiento para registrar al propietario
            $sql = "CALL spu_registrar_propietario(@idPropietario, :nombreHaras)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombreHaras', $nombreHaras);
            $stmt->execute();

            // Obtener el id del propietario recién registrado
            $sql = "SELECT @idPropietario AS idPropietario";
            $stmt = $this->pdo->query($sql);
            $idPropietario = $stmt->fetchColumn();

            // Verificar el resultado del procedimiento
            if ($idPropietario == -2) {
                return ["status" => "error", "message" => "El propietario con este nombre ya está registrado."];
            } elseif ($idPropietario == -1) {
                return ["status" => "error", "message" => "Hubo un error al registrar el propietario."];
            }

            return ["status" => "success", "idPropietario" => $idPropietario]; // Devuelve el id del propietario

        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al registrar el propietario: " . $e->getMessage()];
        }
    }
}