<?php

require_once 'Conexion.php';

class RotacionCampos extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function rotacionCampos($params = []): int
    {
        try {
            // Preparamos la llamada al procedimiento almacenado
            $cmd = $this->pdo->prepare("CALL spu_registrar_rotacion_campos(?, ?, ?, ?)");

            // Ejecutamos el procedimiento con los parámetros correspondientes
            $cmd->execute([
                $params['idCampo'],
                $params['idTipoRotacion'],
                $params['fechaRotacion'], 
                $params['detalleRotacion']
            ]);

            return $cmd->rowCount();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }

    public function registrarCampo($params = []): int
    {
        try {
            // Preparamos la llamada al procedimiento almacenado
            $cmd = $this->pdo->prepare("CALL spu_registrar_campo(?, ?, ?, ?)");

            // Ejecutamos el procedimiento con los parámetros correspondientes
            $cmd->execute([
                $params['numeroCampo'],
                $params['tamanoCampo'],
                $params['tipoSuelo'],
                $params['estado']
            ]);

            return $cmd->rowCount(); // Retorna la cantidad de filas afectadas

        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1; // Retorna -1 en caso de error
        }
    }

    public function listarCampos(): array
    {
        return parent::getData("spu_campos_listar");
    }

    public function listarTipoRotaciones(): array
    {
        return parent::getData("spu_tipos_rotaciones_listar");
    }

    public function obtenerUltimaAccion($idCampo): array
    {
        try {
            // Preparamos la llamada al procedimiento almacenado
            $cmd = $this->pdo->prepare("CALL spu_obtener_ultima_accion(?)");
            $cmd->execute([$idCampo]);

            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return ["status" => "error", "message" => "Error al obtener la última acción."];
        }
    }
}
