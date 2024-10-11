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
            // Verificar si la sesión está activa
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Iniciar la sesión si no está iniciada
            }
    
            // Obtener el idUsuario de la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null; 
    
            // Verificar si el usuario está autenticado
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }
    
            // Verificar que los parámetros obligatorios estén presentes y válidos
            if (empty($params['nombreMedicamento']) || empty($params['lote']) || empty($params['presentacion']) || 
                empty($params['dosis']) || empty($params['tipo']) || $params['cantidad_stock'] <= 0) {
                throw new Exception('Faltan datos obligatorios o hay valores incorrectos.');
            }
    
            // Registrar el intento de ejecución del procedimiento en el log
            error_log("Intentando registrar el medicamento con los siguientes valores: " . json_encode($params));
    
            // Preparar y ejecutar el procedimiento almacenado para registrar un medicamento
            $query = $this->pdo->prepare("CALL spu_medicamentos_registrar(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['descripcion'],
                $params['lote'],
                $params['presentacion'],
                $params['dosis'],
                $params['tipo'],
                $params['cantidad_stock'],
                $params['stockMinimo'],
                $params['fecha_caducidad'],
                $params['precioUnitario'],
                $idUsuario
            ]);
    
            // Verificar si la transacción fue confirmada (si el procedimiento almacenado devuelve el mensaje de confirmación)
            $result = $query->fetch(PDO::FETCH_ASSOC);  // Obtener la fila con el mensaje de confirmación
    
            if ($result && isset($result['mensaje']) && $result['mensaje'] === 'Datos confirmados') {
                error_log("Medicamento registrado correctamente y verificado.");
                return true; // Devolver true si la inserción fue exitosa
            } else {
                error_log("No se pudo registrar el medicamento o no se confirmó correctamente.");
                return false; // Devolver false si no se confirmó
            }
        } catch (PDOException $e) {
            // Capturar errores específicos de PDO y registrarlos
            error_log("Error al registrar medicamento (PDOException): " . $e->getMessage());
            return false; // Devolver false en caso de error de base de datos
        } catch (Exception $e) {
            // Capturar cualquier otra excepción y registrarla
            error_log("Error al registrar medicamento (Exception): " . $e->getMessage());
            return false; // Devolver false en caso de otro tipo de error
        }
    }
    



    // Registrar entrada de medicamentos
    public function entradaMedicamento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Iniciar la sesión si no está iniciada
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null; // Obtener idUsuario de la sesión

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.'); // Si no hay usuario, lanzar error
            }

            // Ejecutar el procedimiento almacenado para registrar la entrada de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_entrada(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreMedicamento'],
                $params['lote'],
                $params['presentacion'],      // Presentación del medicamento
                $params['dosis'],             // Dosis del medicamento
                $params['tipo'],              // Tipo del medicamento
                $params['cantidad'],          // Cantidad de stock
                $params['stockMinimo'],       // Stock mínimo
                $params['fechaCaducidad'],    // Fecha de caducidad
                $params['nuevoPrecio']        // Nuevo precio unitario (si aplica)
            ]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error en entrada de medicamentos: " . $e->getMessage()); // Registrar error en el log
            return false; // Devolver false en caso de error
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

    // Validar presentación y dosis del medicamento
    public function validarPresentacionDosis($params = []) {
        try {
            // Ejecutar el procedimiento almacenado para validar la presentación y dosis
            $query = $this->pdo->prepare("CALL spu_validar_presentacion_dosis(?, ?, ?, ?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['presentacion'],
                $params['dosis'],
                $params['tipo']
            ]);

            return $query->rowCount() > 0; // Devolver true si la validación fue exitosa
        } catch (Exception $e) {
            error_log("Error al validar presentación y dosis: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

    // Registrar actividad de auditoría
    public function registrarActividad($params = []) {
        try {
            // Ejecutar el procedimiento almacenado para registrar una actividad de auditoría
            $query = $this->pdo->prepare("CALL spu_registrar_actividad(?, ?, ?)");
            $query->execute([
                $params['idUsuario'],
                $params['accion'],
                $params['detalles']
            ]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            error_log("Error al registrar actividad de auditoría: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

     // Listar los tipos de movimeinto
    public function listarTiposMedicamentos() {
        try {
            // Ejecutar el procedimiento almacenado para listar los tipos de medicamentos
            $query = $this->pdo->prepare("CALL spu_listar_tipos_medicamentos()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Devolver todos los tipos de medicamentos
        } catch (Exception $e) {
            error_log("Error al listar tipos de medicamentos: " . $e->getMessage());
            return false;
        }
    }


    


    

    
}
