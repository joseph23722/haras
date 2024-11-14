<?php

require_once 'Conexion.php';

class Dashboard extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function ObtenerResumenStockMedicamentos(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerResumenStockMedicamentos()");
            $cmd->execute();
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ObtenerResumenStockMedicamentos: " . $e->getMessage());
            return [];
        }
    }

    public function ObtenerResumenStockAlimentos(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerResumenStockAlimentos()");
            $cmd->execute();
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ObtenerResumenStockAlimentos: " . $e->getMessage());
            return [];
        }
    }

    public function ObtenerTotalEquinosRegistrados(): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerTotalEquinosRegistrados()");
            $cmd->execute();
            return $cmd->fetchColumn();
        } catch (Exception $e) {
            error_log("Error en ObtenerTotalEquinosRegistrados: " . $e->getMessage());
            return 0;
        }
    }

    public function ObtenerServiciosSemanaActual(): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerServiciosSemanaActual()");
            $cmd->execute();
            return $cmd->fetchColumn();
        } catch (Exception $e) {
            error_log("Error en ObtenerServiciosSemanaActual: " . $e->getMessage());
            return 0;
        }
    }

    public function ObtenerMedicamentosEnStock(): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerMedicamentosEnStock()");
            $cmd->execute();
            return $cmd->fetchColumn();
        } catch (Exception $e) {
            error_log("Error en ObtenerMedicamentosEnStock: " . $e->getMessage());
            return 0;
        }
    }

    public function ObtenerAlimentosEnStock(): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerAlimentosEnStock()");
            $cmd->execute();
            return $cmd->fetchColumn();
        } catch (Exception $e) {
            error_log("Error en ObtenerAlimentosEnStock: " . $e->getMessage());
            return 0;
        }
    }

    public function ObtenerResumenServicios(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerResumenServicios()");
            $cmd->execute();
            $result = $cmd->fetchAll(PDO::FETCH_ASSOC);

            return [
                "totalServicios" => $result[0]['totalServicios'] ?? 0,
                "totalServiciosPropios" => $result[1]['totalServiciosPropios'] ?? 0,
                "totalServiciosMixtos" => $result[2]['totalServiciosMixtos'] ?? 0
            ];
        } catch (Exception $e) {
            error_log("Error en ObtenerResumenServicios: " . $e->getMessage());
            return [
                "totalServicios" => 0,
                "totalServiciosPropios" => 0,
                "totalServiciosMixtos" => 0
            ];
        }
    }

    public function ObtenerServiciosRealizadosMensual(int $meta): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerServiciosRealizadosMensual(?)");
            $cmd->execute([$meta]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ObtenerServiciosRealizadosMensual: " . $e->getMessage());
            return [];
        }
    }
}
