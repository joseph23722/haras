<?php

require_once 'Conexion.php';

class ServicioPropio extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Registrar un nuevo servicio propio
    public function registrarServicioPropio($params = [])
    {
        try {
            error_log("Llamando a procedimiento almacenado con: " . print_r($params, true));

            $query = $this->pdo->prepare("CALL registrarServicio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquinoMacho'],
                $params['idEquinoHembra'],
                null,
                null,
                $params['fechaServicio'],
                'propio',
                $params['detalles'],
                null,
                $params['horaEntrada'],
                $params['horaSalida'],
                null
            ]);

            return ['status' => 'success', 'message' => 'Servicio propio registrado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error al registrar servicio propio: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al registrar el servicio propio.'];
        }
    }

    // Listar equinos propios filtrando por tipo
    public function listarEquinosPropios($tipoEquino)
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_propios()");
            $query->execute();
            $equinos = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_filter($equinos, function($equino) use ($tipoEquino) {
                return $equino['idTipoEquino'] == $tipoEquino;
            });
        } catch (PDOException $e) {
            error_log("Error al listar equinos propios: " . $e->getMessage());
            return [];
        }
    }

    // Listar medicamentos
    public function listarMedicamentos()
    {
        try {
            $query = $this->pdo->prepare("CALL ListarMedicamentos()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar medicamentos: " . $e->getMessage());
            return [];
        }
    }
}
?>
