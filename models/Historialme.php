<?php
require_once 'Conexion.php';

class Historialme extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar el historial médico
    public function registrarHistorial($params = [])
    {
        try {
            // Log para verificar los parámetros enviados al modelo
            error_log("Parámetros enviados al modelo: " . json_encode($params));

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Crear un array de campos obligatorios para verificar si alguno está vacío
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'idMedicamento' => $params['idMedicamento'] ?? null,
                'dosis' => $params['dosis'] ?? null,
                'frecuenciaAdministracion' => $params['frecuenciaAdministracion'] ?? null,
                'idViaAdministracion' => $params['idViaAdministracion'] ?? null,
                'fechaFin' => $params['fechaFin'] ?? null,
                'tipoTratamiento' => $params['tipoTratamiento'] ?? null
            ];

            foreach ($obligatorios as $campo => $valor) {
                if (empty($valor)) {
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }

            $query = $this->pdo->prepare("CALL spu_historial_medico_registrarMedi(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquino'],
                $idUsuario,
                $params['idMedicamento'],
                $params['dosis'],
                $params['frecuenciaAdministracion'],
                $params['idViaAdministracion'],
                $params['fechaFin'],
                $params['observaciones'] ?? null,
                $params['reaccionesAdversas'] ?? null,
                $params['tipoTratamiento']
            ]);

            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Historial médico registrado correctamente'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar el historial médico'];
            }
        } catch (Exception $e) {
            error_log("Error en registrarHistorial: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para listar equinos propios (sin propietario) para medicamentos
    public function listarEquinosPorTipo()
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_propiosMedi()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para consultar el historial médico de un equino
    public function consultarHistorialMedico()
    {
        $query = $this->pdo->prepare("CALL spu_consultar_historial_medicoMedi()");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los medicamentos
    public function getAllMedicamentos()
    {
        try {
            // Llamada al procedimiento almacenado para listar los medicamentos
            $query = "CALL spu_listar_medicamentosMedi()"; // Llamada directa al procedimiento almacenado

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver todos los registros como un array asociativo
        } catch (Exception $e) {
            error_log("Error al obtener medicamentos: " . $e->getMessage());
            return false;
        }
    }

    // Método para gestionar el estado de un tratamiento por ID en la tabla DetalleMedicamentos
    public function gestionarTratamiento($idDetalleMed, $accion)
    {
        try {
            error_log("Iniciando 'gestionarTratamiento' con ID: $idDetalleMed y Acción: $accion");

            if (!in_array($accion, ['pausar', 'continuar', 'eliminar'])) {
                error_log("Acción no válida en el método del modelo: $accion");
                throw new Exception("Acción no válida. Use 'pausar', 'continuar' o 'eliminar'.");
            }

            $query = "CALL spu_gestionar_tratamiento(:idDetalleMed, :accion)";
            $consulta = $this->pdo->prepare($query);
            $consulta->bindParam(':idDetalleMed', $idDetalleMed, PDO::PARAM_INT);
            $consulta->bindParam(':accion', $accion, PDO::PARAM_STR);
            $consulta->execute();

            error_log("Procedimiento almacenado ejecutado correctamente para la acción '$accion'.");
            return true;
        } catch (Exception $e) {
            error_log("Error en 'gestionarTratamiento': " . $e->getMessage());
            return false;
        }
    }

    //notificar al usuario que el tratamiento ha finalizado
    public function notificarTratamientosVeterinarios()
    {
        try {
            $query = $this->pdo->prepare("CALL spu_notificar_tratamientos_veterinarios()");
            $query->execute();

            $notificaciones = [];
            do {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if ($result) {
                    error_log("Resultados obtenidos: " . json_encode($result));
                    $notificaciones = array_merge($notificaciones, $result);
                }
            } while ($query->nextRowset());

            return ['status' => 'success', 'data' => $notificaciones];
        } catch (Exception $e) {
            error_log("Error en notificarTratamientosVeterinarios: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener notificaciones.'];
        }
    }

    // Método 1: Listar todas las vías de administración
    public function listarViasAdministracion()
    {
        try {
            // Preparar y ejecutar el procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_Listar_ViasAdministracion()");
            $query->execute();
        
            $vias = $query->fetchAll(PDO::FETCH_ASSOC); // Obtener todas las filas directamente
            error_log("Vías de administración obtenidas: " . json_encode($vias));
        
            return $vias; // Devolver las filas obtenidas
        } catch (Exception $e) {
            error_log("Error en listarViasAdministracion: " . $e->getMessage());
            return [];
        }
        
    }

    // Método 2: Agregar una nueva vía de administración
    public function agregarViaAdministracion($nombreVia, $descripcion = null)
    {
        try {
            // Preparar el procedimiento almacenado con parámetros
            $query = $this->pdo->prepare("CALL spu_Agregar_Via_Administracion(:nombreVia, :descripcion)");
            $query->bindParam(':nombreVia', $nombreVia, PDO::PARAM_STR);
            $query->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

            // Ejecutar el procedimiento almacenado
            $query->execute();

            return ['status' => 'success', 'message' => 'Vía de administración agregada correctamente.'];
        } catch (Exception $e) {
            // Registrar errores en los logs
            error_log("Error en agregarViaAdministracion: " . $e->getMessage());
            
            // Manejo de error en caso de duplicado o cualquier otro fallo
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => 'Ya existe una vía de administración con este nombre.'];
            }
            
            return ['status' => 'error', 'message' => 'Error al agregar vía de administración.'];
        }
    }
}