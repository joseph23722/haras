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

            $query = $this->pdo->prepare("CALL registrarServicio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

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
                $params['horaSalida'],
                $params['costoServicio'] ?? null,
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



    public function registrarDosisAplicada($idMedicamento, $idEquino, $cantidadAplicada, $unidadAplicada)
    {
        try {
            // Validar y obtener el idUsuario desde la sesión
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Iniciar la sesión si no está activa
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado. No se puede registrar la dosis aplicada.');
            }

            // Preparar la consulta para llamar al procedimiento
            $query = $this->pdo->prepare("CALL spu_registrar_dosis_aplicada(?, ?, ?, ?, ?)");

            // Ejecutar el procedimiento con los parámetros proporcionados
            $query->execute([$idMedicamento, $idEquino, $cantidadAplicada, $idUsuario, $unidadAplicada]);

            // Retornar éxito o una confirmación
            return true;
        } catch (PDOException $e) {
            // Registrar el error en el log
            error_log("Error al registrar dosis aplicada: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            // Manejar errores generales (como la validación del usuario)
            error_log("Error general al registrar dosis aplicada: " . $e->getMessage());
            return false;
        }
    }




    public function obtenerHistorialDosisAplicadas()
    {
        try {
            // Preparar la consulta para llamar al procedimiento
            $query = $this->pdo->prepare("CALL spu_ObtenerHistorialDosisAplicadas()");

            // Ejecutar el procedimiento sin parámetros
            $query->execute();

            // Retornar el resultado como un arreglo asociativo
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registrar el error en el log
            error_log("Error al obtener historial de dosis aplicadas: " . $e->getMessage());
            return [];
        }
    }

    public function listarUnidadesPorMedicamento($idMedicamento)
    {
        try {
            $query = $this->pdo->prepare("
                SELECT u.idUnidad, u.unidad AS nombreUnidad
                FROM Medicamentos m
                JOIN CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
                JOIN UnidadesMedida u ON c.idUnidad = u.idUnidad
                WHERE m.idMedicamento = ?
            ");
            $query->execute([$idMedicamento]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar unidades: " . $e->getMessage());
            return [];
        }
    }
}
