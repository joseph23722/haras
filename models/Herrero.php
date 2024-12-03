<?php
require_once 'Conexion.php';

class Herrero extends Conexion {
    private $pdo;

    // Constructor para establecer la conexión con la base de datos
    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

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
    public function consultarHistorialEquino() {
        try {
            // Preparar la llamada al procedimiento almacenado sin parámetros
            $stmt = $this->pdo->prepare("CALL ConsultarHistorialEquino()");
    
            // Ejecutar la consulta
            $stmt->execute();
    
            // Recuperar los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Si no hay resultados, retornar mensaje informativo
            if (empty($resultados)) {
                return ['status' => 'info', 'message' => 'No se encontraron registros.', 'data' => []];
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

    // Listar Tipos de Trabajos
    public function listarTiposTrabajos() {
        try {
            $result = $this->pdo->query("CALL spu_listar_tipos_trabajos()");
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'message' => 'Tipos de trabajo listados correctamente.',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error al listar tipos de trabajos: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al listar los tipos de trabajos.',
                'data' => []
            ];
        }
    }

    // Listar Herramientas
    public function listarHerramientas() {
        try {
            $result = $this->pdo->query("CALL spu_listar_herramientas()");
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'message' => 'Herramientas listadas correctamente.',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error al listar herramientas: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al listar las herramientas.',
                'data' => []
            ];
        }
    }

    // Agregar un Nuevo Tipo de Trabajo
    public function agregarTipoTrabajo($nombreTrabajo) {
        try {
            // Verificar que el parámetro no esté vacío
            if (empty($nombreTrabajo)) {
                throw new Exception("El nombre del trabajo es obligatorio.");
            }

            $this->pdo->query("CALL spu_agregar_tipo_trabajo('$nombreTrabajo')");
            return ['status' => 'success', 'message' => 'Tipo de trabajo agregado exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error al agregar tipo de trabajo: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al agregar el tipo de trabajo.'];
        } catch (Exception $e) {
            error_log("Error en agregarTipoTrabajo: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Agregar una Nueva Herramienta
    public function agregarHerramienta($nombreHerramienta) {
        try {
            // Verificar que el parámetro no esté vacío
            if (empty($nombreHerramienta)) {
                throw new Exception("El nombre de la herramienta es obligatorio.");
            }

            $this->pdo->query("CALL spu_agregar_herramienta('$nombreHerramienta')");
            return ['status' => 'success', 'message' => 'Herramienta agregada exitosamente.'];
        } catch (PDOException $e) {
            error_log("Error al agregar herramienta: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al agregar la herramienta.'];
        } catch (Exception $e) {
            error_log("Error en agregarHerramienta: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Función para listar tipos y herramientas
    public function listarTiposYHerramientas() {
        try {
            $query = $this->pdo->prepare("CALL spu_ListarTiposYHerramientas()");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
    
            // Log para verificar los datos obtenidos de la base de datos
            error_log("Datos obtenidos de la base de datos: " . json_encode($result));
    
            return $result;
        } catch (Exception $e) {
            error_log("Error al listar tipos y herramientas: " . $e->getMessage());
            return false;
        }
    }
    
    

    // Función para editar tipo o herramienta
    // Función para editar tipo o herramienta
    public function editarTipoOHerramienta($id, $nombre, $tipo) {
        try {
            // Determinar la tabla a actualizar según el tipo
            if ($tipo === 'Tipo de Trabajo') {
                $query = $this->pdo->prepare("UPDATE TiposTrabajos SET nombreTrabajo = :nombre WHERE idTipoTrabajo = :id");
            } else if ($tipo === 'Herramienta') {
                $query = $this->pdo->prepare("UPDATE Herramientas SET nombreHerramienta = :nombre WHERE idHerramienta = :id");
            } else {
                throw new Exception("Tipo no válido.");
            }

            // Ejecutar la consulta
            $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_STR);
            $query->execute();

            // Verificar si se actualizó alguna fila
            if ($query->rowCount() === 0) {
                error_log("No se actualizaron filas: id=$id, nombre=$nombre, tipo=$tipo");
            }

            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error en editarTipoOHerramienta: " . $e->getMessage());
            return false;
        }
    }

    
    
    


    

    



    
}
