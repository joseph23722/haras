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
                null,
                null,
                null
            ]);

            return ['status' => 'success', 'message' => 'Servicio propio registrado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error al registrar servicio propio: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
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

    public function listarMedicamentos(): array
    {
        return parent::getData("ListarMedicamentos");
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

    




    public function listarServiciosPorTipo($tipoServicio)
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listarServiciosPorTipo(?)");
            $query->execute([$tipoServicio]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar servicios: " . $e->getMessage());
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

    
}
