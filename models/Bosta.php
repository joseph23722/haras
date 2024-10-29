<?php

require_once 'Conexion.php';

class Bosta extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function RegistroBostas($params = []): int
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_registrar_bosta(?, ?, ?)");
            $cmd->execute([
                $params['fecha'],
                $params['cantidadsacos'],
                $params['pesoaprox'],
            ]);

            return 1;

        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }

    public function ObtenerPesos(): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_obtener_pesos()");
            $cmd->execute();
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }
}