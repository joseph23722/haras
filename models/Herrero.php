<?php
require_once 'Conexion.php';

class Herrero extends Conexion {
    private $pdo;

    // Constructor para establecer la conexión con la base de datos
    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para insertar un nuevo trabajo en el historial del herrero
    // Método para insertar un nuevo trabajo en el historial del herrero
    public function insertarHistorialHerrero($params = []) {
        try {
            // Verificar y manejar la sesión
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;
            if ($idUsuario === null) {
                error_log("Usuario no autenticado: idUsuario no definido en la sesión.");
                return ['status' => 'error', 'message' => 'Usuario no autenticado.'];
            }

            // Validar los campos obligatorios
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'fecha' => $params['fecha'] ?? null,
                'idTrabajo' => $params['idTrabajo'] ?? null,  // Cambiado a ID del trabajo
                'idHerramienta' => $params['idHerramienta'] ?? null, // Cambiado a ID de la herramienta
                'observaciones' => $params['observaciones'] ?? null
            ];

            foreach ($obligatorios as $campo => $valor) {
                if (empty($valor)) {
                    error_log("Campo obligatorio faltante: $campo.");
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }

            // Ejecutar el procedimiento almacenado
            $stmt = $this->pdo->prepare("CALL InsertarHistorialHerrero(?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['idEquino'],
                $idUsuario,
                $params['fecha'],
                $params['idTrabajo'], // Pasar el ID del trabajo
                $params['idHerramienta'], // Pasar el ID de la herramienta
                $params['observaciones']
            ]);

            // Verificar si se insertó correctamente
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Trabajo registrado en el historial correctamente.'];
            } else {
                error_log("No se afectaron filas al insertar historial del herrero.");
                return ['status' => 'error', 'message' => 'No se pudo registrar el trabajo en el historial.'];
            }
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            error_log("Error PDO al insertar historial del herrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos al registrar el historial.'];
        } catch (Exception $e) {
            // Manejar otros errores
            error_log("Error en insertarHistorialHerrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    
    
    
    

    // Método para consultar el historial completo de un equino
    // Método para consultar el historial completo de un equino
    public function consultarHistorialEquino($idEquino) {
        try {
            // Validar que el ID del equino no esté vacío
            if (empty($idEquino)) {
                throw new Exception("El ID del equino es obligatorio.");
            }

            // Preparar y ejecutar la llamada al procedimiento almacenado
            $stmt = $this->pdo->prepare("CALL ConsultarHistorialEquino(?)");
            $stmt->execute([$idEquino]);

            // Recuperar los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return ['status' => 'info', 'message' => 'No se encontraron registros para el equino seleccionado.', 'data' => []];
            }

            // Retornar los resultados en formato esperado
            return ['status' => 'success', 'data' => $resultados];
        } catch (PDOException $e) {
            // Manejar errores de la base de datos
            error_log("Error al consultar historial de equino (PDO): " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar el historial del equino.'];
        } catch (Exception $e) {
            // Manejar otros errores
            error_log("Error en consultarHistorialEquino: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



    // Método para obtener los tipos de equinos
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

    //Listar Tipos de Trabajos
    public function listarTiposTrabajos() {
        try {
            $stmt = $this->pdo->prepare("CALL spu_listar_tipos_trabajos()");
            $stmt->execute();
            return ['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            error_log("Error al listar tipos de trabajos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al listar los tipos de trabajos.'];
        }
    }

    //Listar Herramientas
    public function listarHerramientas() {
        try {
            $stmt = $this->pdo->prepare("CALL spu_listar_herramientas()");
            $stmt->execute();
            return ['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            error_log("Error al listar herramientas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al listar las herramientas.'];
        }
    }

    
    //Agregar un Nuevo Tipo de Trabajo
    



    
}
