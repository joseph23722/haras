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
            $cmd = $this->pdo->prepare("CALL spu_registrar_campo(?, ?, ?, ?)");

            $cmd->execute([
                $params['numeroCampo'],
                $params['tamanoCampo'],
                $params['idTipoSuelo'],
                $params['estado']
            ]);

            return $cmd->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() === '45000') {
                return -2;
            } else {
                error_log("Error: " . $e->getMessage());
                return -1;
            }
        }
    }

    // Método para editar un campo
    public function editarCampo($params = []): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_editar_campo(?, ?, ?, ?, ?)");
            $cmd->execute([
                $params['idCampo'],
                $params['numeroCampo'],
                $params['tamanoCampo'],
                $params['idTipoSuelo'],
                $params['estado']
            ]);
            return $cmd->rowCount();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }

    // Método para eliminar un campo
    public function eliminarCampo($idCampo): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_eliminar_campo(?)");
            $cmd->execute([$idCampo]);
            return $cmd->rowCount();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }

    public function obtenerCampoID($idCampo): array
    {
        try {
            // Preparamos la llamada al procedimiento almacenado
            $cmd = $this->pdo->prepare("CALL spu_obtener_campoID(?)");
            $cmd->execute([$idCampo]);

            // Devolver el resultado como un array asociativo
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return ["status" => "error", "message" => "Error al obtener el campo."];
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

    public function listarRotaciones(): array
    {
        return parent::getData("spu_listar_rotaciones");
    }

    public function listarTipoSuelo(): array
    {
        return parent::getData("spu_tiposuelo_listar");
    }
}
