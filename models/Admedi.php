<?php
require_once 'Conexion.php';

class Admi extends Conexion {
    private $pdo;

    // Constructor para establecer conexión con la base de datos
    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }
    
    // Obtener todos los medicamentos
    public function getAllMedicamentos() {
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
    
    
    // Registrar un nuevo medicamento
    public function registrarMedicamento($params = []) {
        try {
            // Iniciar sesión si no está activa
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el idUsuario de la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            // Verificar si el usuario está autenticado
            if ($idUsuario === null) {
                return ['status' => 'error', 'message' => 'Usuario no autenticado.'];
            }

            // Verificar que los parámetros obligatorios estén presentes y sean válidos
            if (empty($params['nombreMedicamento']) || empty($params['lote']) || empty($params['presentacion']) || 
                empty($params['dosis']) || empty($params['tipo']) || $params['cantidad_stock'] <= 0) {
                return ['status' => 'error', 'message' => 'Faltan datos obligatorios o valores incorrectos.'];
            }

            // Paso 1: Validar combinación de tipo, presentación y dosis
            $validacion = $this->validarRegistrarCombinacion([
                'tipoMedicamento' => $params['tipo'],
                'presentacionMedicamento' => $params['presentacion'],
                'dosisMedicamento' => $params['dosis']
            ]);

            if (!$validacion) {
                return ['status' => 'error', 'message' => 'Error: La combinación de tipo, presentación y dosis no es válida.'];
            }

            // Paso 2: Proceder con el registro del medicamento si la combinación es válida
            $query = $this->pdo->prepare("CALL spu_medicamentos_registrar(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['descripcion'] ?? null,  // Puede ser nulo si no se proporciona
                $params['lote'],
                $params['presentacion'],
                $params['dosis'],
                $params['tipo'],
                $params['cantidad_stock'],
                $params['stockMinimo'] ?? 0,  // Valor por defecto si no se proporciona
                $params['fecha_caducidad'],
                $params['precioUnitario'] ?? 0.0,  // Valor por defecto si no se proporciona
                $idUsuario
            ]);

            // Obtener los resultados del procedimiento
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Verificar si el procedimiento confirmó la transacción
            if ($result && isset($result['mensaje']) && $result['mensaje'] === 'Datos confirmados') {
                return ['status' => 'success', 'message' => 'Medicamento registrado correctamente.'];
            }

            // Si no se confirmó el registro, devolver error
            return ['status' => 'error', 'message' => 'No se pudo confirmar el registro del medicamento.'];

        } catch (PDOException $e) {
            // Verificar si es un error relacionado con la dosis mal escrita
            if (strpos($e->getMessage(), 'Dosis mal escrita o inexistente') !== false) {
                return ['status' => 'error', 'message' => 'Error: La dosis ingresada es incorrecta o no existe en el sistema.'];
            }
            // Capturar y devolver otros errores de la base de datos
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Capturar cualquier otro error
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }


    // Registrar entrada de medicamentos
    public function entradaMedicamento($params = []) {
        try {
            // Iniciar la sesión si no está activa
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Iniciar la sesión si no está iniciada
            }

            // Obtener el idUsuario de la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            // Verificar si el usuario está autenticado
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.'); // Si no hay usuario, lanzar error
            }

            // Verificar que los parámetros obligatorios estén presentes y sean válidos
            if (empty($params['nombreMedicamento']) || empty($params['lote']) || $params['cantidad'] <= 0) {
                throw new Exception('Faltan datos obligatorios o valores incorrectos.');
            }

            // Ejecutar el procedimiento almacenado para registrar la entrada de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_entrada(?, ?, ?, ?)");
            $query->execute([
                $idUsuario,                     // Usuario que realiza la operación
                $params['nombreMedicamento'],    // Nombre del medicamento
                $params['lote'],                // Número de lote del medicamento
                $params['cantidad']             // Cantidad de stock a ingresar
            ]);

            // Verificar si la inserción fue exitosa
            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Entrada de medicamento registrada correctamente.'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar la entrada del medicamento.'];
            }
        } catch (Exception $e) {
            // Registrar el error en el log del servidor
            error_log("Error en entrada de medicamentos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }



    // Registrar salida de medicamentos
    public function salidaMedicamento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Iniciar la sesión si no está iniciada
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null; // Obtener idUsuario de la sesión

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.'); // Si no hay usuario, lanzar error
            }

            // Ejecutar el procedimiento almacenado para registrar la salida de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_salida(?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreMedicamento'],
                $params['cantidad']
            ]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error en salida de medicamentos: " . $e->getMessage()); // Registrar error en el log
            return false; // Devolver false en caso de error
        }
    }

    // Notificar medicamentos con stock bajo
    public function notificarStockBajo() {
        try {
            $query = $this->pdo->prepare("CALL spu_notificar_stock_bajo_medicamentos()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Devolver los medicamentos con stock bajo
        } catch (Exception $e) {
            error_log("Error en notificación de stock bajo: " . $e->getMessage());
            return [];
        }
    }

    // Registrar historial de movimientos de medicamentos
    public function registrarHistorialMedicamento($params = []) {
        try {
            // Ejecutar el procedimiento almacenado para registrar el historial
            $query = $this->pdo->prepare("CALL spu_historial_medicamentos_movimientosMedi(?, ?, ?, ?)");
            $query->execute([
                $params['idMedicamento'],
                $params['accion'],
                $params['tipoMovimiento'],
                $params['cantidad']
            ]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error al registrar historial de medicamentos: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

    // Agregar un nuevo tipo de medicamento
    public function agregarTipoMedicamento($tipo) {
        try {
            // Ejecutar el procedimiento almacenado para agregar un tipo de medicamento
            $query = $this->pdo->prepare("CALL spu_agregar_tipo_medicamento(?)");
            $query->execute([$tipo]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error al agregar tipo de medicamento: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

    // Agregar una nueva presentación de medicamento
    public function agregarPresentacionMedicamento($presentacion) {
        try {
            // Ejecutar el procedimiento almacenado para agregar una presentación de medicamento
            $query = $this->pdo->prepare("CALL spu_agregar_presentacion_medicamento(?)");
            $query->execute([$presentacion]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error al agregar presentación de medicamento: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }


    // Validar presentación y dosis del medicamento
    public function validarRegistrarCombinacion($params = []) {
        try {
            // Ejecutar el procedimiento almacenado para validar y registrar la combinación
            $query = $this->pdo->prepare("CALL spu_validar_registrar_combinacion(?, ?, ?)");
            $query->execute([
                $params['tipoMedicamento'],         // Tipo de medicamento
                $params['presentacionMedicamento'], // Presentación del medicamento
                $params['dosisMedicamento']         // Dosis del medicamento
            ]);

            // Verificar si el procedimiento arrojó resultados (combinación válida)
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Si el resultado contiene la clave 'mensaje', devolver éxito
            if ($result && isset($result['mensaje']) && $result['mensaje'] === 'Combinación válida') {
                return true; // Combinación validada correctamente
            }

            return false; // Si no se validó la combinación, devolver false

        } catch (PDOException $e) {
            // Verificar si es un error relacionado con la dosis mal escrita o no válida
            if (strpos($e->getMessage(), 'La dosis está mal escrita o no es válida') !== false) {
                return ['status' => 'error', 'message' => 'Error: La dosis ingresada es incorrecta o no válida.'];
            }

            // Registrar otros errores en el log y devolver false
            error_log("Error al validar o registrar la combinación: " . $e->getMessage());
            return false; // Devolver false en caso de error
        } catch (Exception $e) {
            // Capturar cualquier otro error inesperado
            error_log("Error inesperado al validar la combinación: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

    
     // Listar los tipos de movimeinto
    public function listarTiposMedicamentos() {
        try {
            // Ejecutar el procedimiento almacenado para listar los tipos de medicamentos
            $query = $this->pdo->prepare("CALL spu_listar_tipos_presentaciones_dosis()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Devolver todos los tipos de medicamentos
        } catch (Exception $e) {
            error_log("Error al listar tipos de medicamentos: " . $e->getMessage());
            return false;
        }
    }

    // Listar las presentaciones de medicamentos
    public function listarPresentacionesMedicamentos() {
        try {
            // Ejecutar el procedimiento almacenado para listar las presentaciones de medicamentos
            $query = $this->pdo->prepare("CALL spu_listar_presentaciones_medicamentos()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Devolver todas las presentaciones de medicamentos
        } catch (Exception $e) {
            error_log("Error al listar presentaciones de medicamentos: " . $e->getMessage());
            return false;
        }
    }

    // Función para listar las sugerencias de medicamentos
    public function listarSugerenciasMedicamentos() {
    try {
        // Ejecutar el procedimiento almacenado para listar las sugerencias
        $query = $this->pdo->prepare("CALL spu_listar_tipos_presentaciones_dosis()");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al listar sugerencias de medicamentos: " . $e->getMessage());
        return false;
    }

    
    }

    // Función pública para eliminar un medicamento por ID
    public function borrarMedicamento($idMedicamento) {
        try {
            // Verificar que el ID del medicamento es válido
            if ($idMedicamento <= 0) {
                return false; // ID no válido
            }

            // Preparar la consulta para eliminar el medicamento
            $query = $this->pdo->prepare("DELETE FROM Medicamentos WHERE idMedicamento = ?");
            
            // Ejecutar la consulta
            $query->execute([$idMedicamento]);
            
            // Verificar si se eliminó algún registro
            return $query->rowCount() > 0;

        } catch (PDOException $e) {
            // Manejar errores de base de datos
            return false;
        } 
    }

    // Listar todos los lotes registrados
    public function listarLotes() {
        try {
            // Ejecutar el procedimiento almacenado para listar los lotes
            $query = $this->pdo->prepare("CALL spu_listar_lotes()");
            $query->execute();

            // Obtener los resultados como un array asociativo
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            // Devolver los resultados si existen
            if ($result) {
                return ['status' => 'success', 'data' => $result];
            } else {
                return ['status' => 'error', 'message' => 'No se encontraron lotes registrados.'];
            }

        } catch (PDOException $e) {
            // Capturar y devolver el error de la base de datos
            error_log("Error al listar los lotes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Capturar cualquier otro error
            error_log("Error inesperado al listar los lotes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    



    
}
