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

    public function ObtenerResumenServicios(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL ObtenerResumenServicios()");
            $cmd->execute();
            $result = $cmd->fetch(PDO::FETCH_ASSOC);  // Cambié fetchAll() por fetch()

            return [
                "totalServicios" => $result['totalServicios'] ?? 0,
                "totalServiciosPropios" => $result['totalServiciosPropios'] ?? 0,
                "totalServiciosMixtos" => $result['totalServiciosMixtos'] ?? 0
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

    public function ObtenerFotografiasEquinos(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_listar_fotografia_dashboard()");
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ObtenerFotografiasEquinos: " . $e->getMessage());
            return [];
        }
    }
}
