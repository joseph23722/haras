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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error al obtener medicamentos: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Método para registrar un nuevo medicamento
    // Método para registrar un nuevo medicamento
    public function registrarMedicamento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $idUsuario = $_SESSION['idUsuario'] ?? null;
    
            if (empty($params['nombreMedicamento']) || empty($params['lote']) || empty($params['presentacion']) || 
                empty($params['dosisCompleta']) || empty($params['tipo']) || 
                $params['cantidad_stock'] <= 0 || $params['precioUnitario'] <= 0) {
                throw new Exception('Datos inválidos. Verifique los datos obligatorios, stock y precio unitario.');
            }
    
            error_log("Parámetros recibidos en registrarMedicamento:");
            foreach ($params as $key => $value) {
                error_log("$key => $value");
            }
    
            $stmt = $this->pdo->prepare("CALL spu_medicamentos_registrar(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['nombreMedicamento'],
                $params['descripcion'] ?? null,
                $params['lote'],
                $params['presentacion'],
                $params['dosisCompleta'],
                $params['tipo'],
                $params['cantidad_stock'],
                $params['stockMinimo'] ?? 0,
                $params['fechaCaducidad'],
                $params['precioUnitario'],
                $idUsuario
            ]);
    
            return ['status' => 'success', 'message' => 'Medicamento registrado correctamente.'];
        } catch (PDOException $e) {
            // Extrae solo el mensaje relevante usando una expresión regular
            $errorMessage = $e->getMessage();
            if (preg_match("/: ([^:]+)$/", $errorMessage, $matches)) {
                $errorMessage = trim($matches[1]); // Extrae solo el mensaje de error
            }
            error_log("Error en la base de datos: " . $errorMessage);
            return ['status' => 'error', 'message' => $errorMessage]; // Muestra solo el mensaje sin el código de error
        } catch (Exception $e) {
            $errorMessage = trim($e->getMessage());
            error_log("Error inesperado: " . $errorMessage);
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $errorMessage];
        }
    }
    
    
    

    
    

    // Registrar entrada de medicamentos
    public function entradaMedicamento($params = []) {
        try {
            // Iniciar la sesión si no está activa
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el idUsuario de la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            // Verificar si el usuario está autenticado
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
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
                $params['lote'],                 // Número de lote del medicamento
                $params['cantidad']              // Cantidad de stock a ingresar
            ]);

            // Verificar si la inserción fue exitosa
            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Entrada de medicamento registrada correctamente.'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar la entrada del medicamento.'];
            }
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
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
    
            // Verificar que los parámetros obligatorios estén presentes y sean válidos
            if (empty($params['nombreMedicamento']) || $params['cantidad'] <= 0 || empty($params['idTipoEquino']) || empty($params['motivo'])) {
                throw new Exception('Faltan datos obligatorios o valores incorrectos.');
            }
    
            // Ejecutar el procedimiento almacenado para registrar la salida de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_salida(?, ?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreMedicamento'],
                $params['cantidad'],
                $params['idTipoEquino'],
                $params['lote'] ?? null, // Permitir que 'lote' sea NULL si no se proporciona
                $params['motivo']
            ]);
    
            // Verificar si la operación fue exitosa
            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Salida de medicamento registrada correctamente.'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar la salida del medicamento.'];
            }
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            // Registrar el error en el log del servidor
            error_log("Error en salida de medicamentos: " . $errorMessage);
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $errorMessage];
        }
    }
    


    // Método para verificar si un lote de medicamento ya existe
    public function verificarLoteMedicamento($lote) {
        try {
            $query = $this->pdo->prepare("SELECT idLoteMedicamento FROM LotesMedicamento WHERE lote = ?");
            $query->execute([$lote]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 'success',
                    'idLoteMedicamento' => $result['idLoteMedicamento']
                ];
            } else {
                return [
                    'status' => 'success',
                    'idLoteMedicamento' => null  // Indica que el lote no existe
                ];
            }
        } catch (PDOException $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            return [
                'status' => 'error',
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }


    // Notificar medicamentos con stock bajo
    public function notificarStockBajo() {
        try {
            $query = $this->pdo->prepare("CALL spu_notificar_stock_bajo_medicamentos()");
            $query->execute();

            // Primer conjunto de resultados: Medicamentos agotados
            $agotados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Mover al siguiente conjunto de resultados
            $query->nextRowset();

            // Segundo conjunto de resultados: Medicamentos con stock bajo
            $bajoStock = $query->fetchAll(PDO::FETCH_ASSOC);

            // Devolver ambos conjuntos de resultados en un array
            return [
                'status' => 'success',
                'data' => [
                    'agotados' => $agotados,
                    'bajoStock' => $bajoStock
                ]
            ];
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error en notificación de stock bajo: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al obtener notificaciones de stock bajo.'
            ];
        }
    }


    // Método para obtener el historial de movimientos de medicamentos
    public function obtenerHistorialMovimientosMedicamentos($params = []) {
        try {
            // Validar que se haya proporcionado un tipo de movimiento válido
            $tipoMovimiento = $params['tipoMovimiento'] ?? 'Entrada';
            if (!in_array($tipoMovimiento, ['Entrada', 'Salida'])) {
                throw new Exception('Tipo de movimiento no válido. Debe ser "Entrada" o "Salida".');
            }

            // Validar que las fechas sean correctas
            $fechaInicio = $params['fechaInicio'] ?? '1900-01-01';
            $fechaFin = $params['fechaFin'] ?? date('Y-m-d');
            if (strtotime($fechaInicio) > strtotime($fechaFin)) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha de fin.');
            }

            // Preparar la llamada al procedimiento almacenado con los 6 parámetros
            $query = $this->pdo->prepare("CALL spu_historial_completo_medicamentos(?, ?, ?, ?, ?, ?)");

            // Ejecutar el procedimiento almacenado pasando los parámetros necesarios
            $query->execute([
                $tipoMovimiento,                         // Tipo de movimiento (Entrada/Salida)
                $fechaInicio,                            // Fecha de inicio
                $fechaFin,                               // Fecha de fin
                $params['idUsuario'] ?? 0,               // ID del usuario (0 para todos los usuarios)
                $params['limit'] ?? 10,                  // Límite de resultados
                $params['offset'] ?? 0                   // Desplazamiento para paginación
            ]);

            // Obtener los resultados y devolverlos
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Incluir el campo 'motivo' solo si el tipo de movimiento es 'Salida'
            if ($tipoMovimiento === 'Salida') {
                foreach ($resultados as &$resultado) {
                    $resultado['motivo'] = $resultado['motivo'] ?? 'No especificado';  // Manejo de motivo
                }
            }

            // Verificar si hay resultados
            if (!empty($resultados)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No se encontraron movimientos en el rango de fechas seleccionado.'];
            }

        } catch (PDOException $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            // Capturar y registrar cualquier error SQL
            error_log("Error en la base de datos: " . $e->getMessage());

            // Devolver un mensaje de error en caso de problemas en la base de datos
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            // Capturar y registrar cualquier otro tipo de error
            error_log("Error: " . $e->getMessage());

            // Devolver un mensaje de error general
            return ['status' => 'error', 'message' => $e->getMessage()];
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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error al agregar presentación de medicamento: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }

    // Agregar una nueva unidad de medida
    public function agregarUnidadMedida($unidad) {
        try {
            // Ejecutar el procedimiento almacenado para agregar una unidad de medida
            $query = $this->pdo->prepare("CALL spu_agregar_unidad_medida(?)");
            $query->execute([$unidad]);

            return $query->rowCount() > 0; // Devolver true si la inserción fue exitosa
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error al agregar unidad de medida: " . $e->getMessage());
            return false; // Devolver false en caso de error
        }
    }
 
    // editar segurencia de medicamento
    public function editarCombinacionCompleta($idCombinacion, $nuevoTipo, $nuevaPresentacion, $nuevaUnidad) {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();
            
            // Actualizar el tipo de medicamento
            $queryTipo = $this->pdo->prepare("
                UPDATE TiposMedicamentos 
                SET tipo = ? 
                WHERE idTipo = (SELECT idTipo FROM CombinacionesMedicamentos WHERE idCombinacion = ?)
            ");
            $queryTipo->execute([$nuevoTipo, $idCombinacion]);
    
            // Actualizar la presentación del medicamento
            $queryPresentacion = $this->pdo->prepare("
                UPDATE PresentacionesMedicamentos 
                SET presentacion = ? 
                WHERE idPresentacion = (SELECT idPresentacion FROM CombinacionesMedicamentos WHERE idCombinacion = ?)
            ");
            $queryPresentacion->execute([$nuevaPresentacion, $idCombinacion]);
    
            // Intentar actualizar la unidad de medida sin buscarla previamente
            $queryUnidad = $this->pdo->prepare("
                UPDATE UnidadesMedida 
                SET unidad = ? 
                WHERE idUnidad = (SELECT idUnidad FROM CombinacionesMedicamentos WHERE idCombinacion = ?)
            ");
            $queryUnidad->execute([$nuevaUnidad, $idCombinacion]);
    
            // Confirmar transacción si todas las actualizaciones son exitosas
            $this->pdo->commit();
    
            return true; // Retorna true si la transacción fue exitosa
        } catch (Exception $e) {
            // Deshacer cambios si hay un error
            $this->pdo->rollBack();
            error_log("Error al editar combinación completa de medicamento: " . $e->getMessage());
            return false;
        }
    }
    

    // Función para validar si la unidad existe en la tabla UnidadesMedida
    public function validarUnidad($unidad) {
        $query = $this->pdo->prepare("SELECT idUnidad FROM UnidadesMedida WHERE unidad = ?");
        $query->execute([$unidad]);
        return $query->fetchColumn() !== false;
    }

    
    
    
    
    
    
    



    // Validar presentación y dosis del medicamento
    public function validarRegistrarCombinacion($params = []) {
        try {
            // Llamada al procedimiento almacenado para validar la combinación
            $query = $this->pdo->prepare("CALL spu_validar_registrar_combinacion(?, ?, ?, ?)");
            $query->execute([
                $params['tipoMedicamento'],         
                $params['presentacionMedicamento'], 
                $params['dosisMedicamento'], // Dosis como DECIMAL
                $params['unidadMedida'] // Nueva unidad de medida
            ]);
    
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['mensaje']) && strpos($result['mensaje'], 'válida') !== false) {
                // Si se obtiene un mensaje de combinación válida, devuelve el ID de la combinación reutilizada o nueva
                return [
                    'status' => 'success', 
                    'message' => 'Validación y registro de combinación exitoso.', 
                    'data' => [
                        'idCombinacion' => $result['idCombinacion']
                    ]
                ];
            }
    
            return ['status' => 'error', 'message' => 'Combinación inválida de tipo, presentación y dosis.'];
        } catch (PDOException $e) {
            // Captura el mensaje de error y extrae solo el mensaje personalizado
            $errorMessage = $e->getMessage();
            
            // Extraer solo el mensaje de error personalizado eliminando prefijos no deseados
            if (preg_match('/: (\d+)?\s?(.*)$/', $errorMessage, $matches)) {
                // $matches[2] contendrá solo el mensaje personalizado sin el código de error
                $customError = trim($matches[2]);
                return ['status' => 'error', 'message' => $customError];
            } else {
                // En caso de un error genérico
                return ['status' => 'error', 'message' => 'Error en la validación de la combinación.'];
            }
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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
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
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error al listar sugerencias de medicamentos: " . $e->getMessage());
            return false;
        }

    
    }

    // Función pública para eliminar un medicamento por ID
    // Función pública para eliminar un medicamento por ID
    public function borrarMedicamento($idMedicamento) {
        try {
            // Verificar que el ID del medicamento es válido
            if ($idMedicamento <= 0) {
                return ['status' => 'error', 'message' => 'ID de medicamento no válido'];
            }
    
            // Iniciar transacción para asegurar consistencia
            $this->pdo->beginTransaction();
    
            // Eliminar registros relacionados en historialmovimientosmedicamentos
            $queryHistorial = $this->pdo->prepare("DELETE FROM historialmovimientosmedicamentos WHERE idMedicamento = ?");
            $queryHistorial->execute([$idMedicamento]);
    
            // Eliminar el medicamento en la tabla Medicamentos
            $queryMedicamento = $this->pdo->prepare("DELETE FROM Medicamentos WHERE idMedicamento = ?");
            $queryMedicamento->execute([$idMedicamento]);
    
            // Confirmar la transacción si ambas eliminaciones fueron exitosas
            $this->pdo->commit();
    
            // Verificar si se eliminó algún registro en Medicamentos
            if ($queryMedicamento->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Medicamento eliminado correctamente'];
            } else {
                return ['status' => 'error', 'message' => 'El medicamento no fue encontrado o ya fue eliminado'];
            }
    
        } catch (PDOException $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            // Si hay un error, revertir la transacción
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    

    // Método para listar todos los lotes de medicamentos registrados
    public function listarLotesMedicamentos() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_lotes_medicamentos()");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
    
            // Imprimir los resultados para depuración
            error_log(print_r($result, true));
    
            if ($result) {
                return ['status' => 'success', 'data' => $result];
            } else {
                return ['status' => 'error', 'message' => 'No se encontraron lotes de medicamentos registrados.'];
            }
    
        } catch (PDOException $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error al listar los lotes de medicamentos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log("Error inesperado al listar los lotes de medicamentos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }
    
    // Método para obtener los tipos de equinos
    public function getTipoEquinos() {
        try {
            $query = $this->pdo->prepare("CALL spu_obtener_tipo_equino_alimento()");
             $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Procesar el mensaje de error para eliminar 'SQLSTATE' y cualquier texto adicional
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage); // Limpiar espacios adicionales
            error_log($e->getMessage());
            return [];
        }
    }

    
}


