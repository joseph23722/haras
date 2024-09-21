<?php

require_once 'Conexion.php';

class ServicioMixto extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Registrar un nuevo servicio mixto
    public function registrarServicioMixto($params = [])
    {
        try {
            error_log("Llamando a procedimiento almacenado con: " . print_r($params, true));

            $query = $this->pdo->prepare("CALL registrarServicio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $query->execute([
                $params['idEquinoMacho'] ?? null,
                $params['idEquinoHembra'] ?? null,
                $params['idPropietario'],
                $params['idEquinoExterno'] ?? null,
                $params['fechaServicio'],
                'mixto',
                $params['detalles'],
                null,
                $params['horaEntrada'],
                $params['horaSalida']
            ]);

            return ['status' => 'success', 'message' => 'Servicio mixto registrado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error al registrar servicio mixto: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al registrar el servicio mixto: ' . $e->getMessage()];
        }
    }

    // Listar equinos propios filtrando por tipo
    public function listarEquinosPropios($tipoEquino)
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_propios()");
            $query->execute();
            $equinos = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_filter($equinos, function ($equino) use ($tipoEquino) {
                return $equino['idTipoEquino'] == $tipoEquino;
            });
        } catch (PDOException $e) {
            error_log("Error al listar equinos propios: " . $e->getMessage());
            return [];
        }
    }

    // Listar propietarios (haras)
    public function listarPropietarios()
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_haras()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al listar propietarios: " . $e->getMessage());
            return [];
        }
    }

    // Listar medicamentos
    public function listarMedicamentos()
    {
        try {
            $query = $this->pdo->prepare("CALL ListarMedicamentos()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al listar medicamentos: " . $e->getMessage());
            return [];
        }
    }

    // Listar equinos externos por propietario
    // Listar equinos externos por propietario y género
    public function listarEquinosExternosPorPropietarioYGenero($idPropietario, $genero)
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_por_propietario(?, ?)");
            $query->execute([$idPropietario, $genero]);
            return $query->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al listar equinos externos por propietario y género: " . $e->getMessage());
            return [];
        }
    }
}
