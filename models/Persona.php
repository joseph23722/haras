<?php

require_once 'Conexion.php';

class Personal extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    public function estadoUsuario($idUsuario) /* Parámetro idUsuario */
    {
        try {
            $query = $this->pdo->prepare("CALL spu_modificar_estado_user(:idUsuario)");
            $query->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $query->execute();

            /* Obtiene el mensaje del procedure creado */
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            return $resultado['mensaje'] ?? 'Operación realziada correctamente';
        } catch (Exception $e) {
            error_log("Error al modificar estado: " . $e->getMessage());
            return 'Ocurrió un error al intentar modificar el estado';
        }
    }

    // Método para registrar una persona
    public function add($params = []): int {
        try {
            $cmd = $this->pdo->prepare("CALL spu_personal_registrar(@idPersonal, ?, ?, ?, ?, ?, ?, ?)");
            $cmd->execute([
                $params['nombres'],
                $params['apellidos'],
                $params['direccion'],
                $params['tipodoc'],
                $params['nrodocumento'],
                $params['fechaIngreso'],
                $params['tipoContrato']
            ]);

            // Obtenemos el ID del registro insertado
            $response = $this->pdo->query("SELECT @idPersonal AS idPersonal")->fetch(PDO::FETCH_ASSOC);
            return (int) $response['idPersonal'];

        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }
 
    /**
     * Retorna una lista de personal
     */
    public function getAll(): array {
        return parent::getData("spu_personal_listar");
    }
}