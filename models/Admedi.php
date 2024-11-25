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
            if (empty($params['nombreMedicamento']) || $params['cantidad'] <= 0 || empty($params['idEquino']) || empty($params['motivo'])) {
                throw new Exception('Faltan datos obligatorios o valores incorrectos.');
            }

            // Ejecutar el procedimiento almacenado para registrar la salida de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_salida(?, ?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreMedicamento'],
                $params['cantidad'],
                $params['idEquino'], // Cambiado a idEquino
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

            // Validar que se haya proporcionado un filtro válido
            $filtroFecha = $params['filtroFecha'] ?? 'hoy';
            if (!in_array($filtroFecha, ['hoy', 'ultimaSemana', 'ultimoMes', 'todos'])) {
                throw new Exception('Filtro de fecha no válido.');
            }

            // Preparar la llamada al procedimiento almacenado con los parámetros
            $query = $this->pdo->prepare("CALL spu_historial_completo_medicamentos(?, ?, ?, ?, ?)");

            // Ejecutar el procedimiento almacenado pasando los parámetros necesarios
            $query->execute([
                $tipoMovimiento,                         // Tipo de movimiento (Entrada/Salida)
                $filtroFecha,                            // Filtro de fecha (hoy, ultimaSemana, ultimoMes, todos)
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
                return ['status' => 'info', 'message' => 'No se encontraron movimientos en el rango seleccionado.'];
            }

        } catch (PDOException $e) {
            // Capturar y registrar cualquier error SQL
            error_log("Error en la base de datos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            // Capturar y registrar cualquier otro tipo de error
            error_log("Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



    // Método para agregar una nueva combinación de medicamento
    public function agregarCombinacionMedicamento($tipo, $presentacion, $unidad, $dosis)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_agregar_nueva_combinacion_medicamento(?, ?, ?, ?, @mensaje)");

            // Ejecutar el procedimiento con los valores de entrada
            $query->execute([$tipo, $presentacion, $unidad, $dosis]);

            // Obtener el mensaje de salida directamente
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['mensaje'] ?? 'Combinación agregada exitosamente.';

        } catch (Exception $e) {
            // Registrar el error en los logs y devolver un mensaje de error amigable
            error_log("Error al agregar combinación de medicamento: " . $e->getMessage());
            return "Error al agregar la combinación de medicamento: " . $e->getMessage();
        }
    }


    // editar segurencia de medicamento
    public function editarCombinacionCompleta($idCombinacion, $nuevoTipo, $nuevaPresentacion, $nuevaUnidad) {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();
            
            // Paso 1: Verificar o insertar el nuevo tipo
            $queryTipo = $this->pdo->prepare("
                SELECT idTipo FROM TiposMedicamentos WHERE tipo = ?
            ");
            $queryTipo->execute([$nuevoTipo]);
            $idTipo = $queryTipo->fetchColumn();
    
            if (!$idTipo) {
                $insertTipo = $this->pdo->prepare("
                    INSERT INTO TiposMedicamentos (tipo) VALUES (?)
                ");
                $insertTipo->execute([$nuevoTipo]);
                $idTipo = $this->pdo->lastInsertId();
            }
    
            // Paso 2: Verificar o insertar la nueva presentación
            $queryPresentacion = $this->pdo->prepare("
                SELECT idPresentacion FROM PresentacionesMedicamentos WHERE presentacion = ?
            ");
            $queryPresentacion->execute([$nuevaPresentacion]);
            $idPresentacion = $queryPresentacion->fetchColumn();
    
            if (!$idPresentacion) {
                $insertPresentacion = $this->pdo->prepare("
                    INSERT INTO PresentacionesMedicamentos (presentacion) VALUES (?)
                ");
                $insertPresentacion->execute([$nuevaPresentacion]);
                $idPresentacion = $this->pdo->lastInsertId();
            }
    
            // Paso 3: Verificar o insertar la nueva unidad
            $queryUnidad = $this->pdo->prepare("
                SELECT idUnidad FROM UnidadesMedida WHERE unidad = ?
            ");
            $queryUnidad->execute([$nuevaUnidad]);
            $idUnidad = $queryUnidad->fetchColumn();
    
            if (!$idUnidad) {
                $insertUnidad = $this->pdo->prepare("
                    INSERT INTO UnidadesMedida (unidad) VALUES (?)
                ");
                $insertUnidad->execute([$nuevaUnidad]);
                $idUnidad = $this->pdo->lastInsertId();
            }
    
            // Paso 4: Actualizar la combinación específica con los nuevos IDs
            $queryUpdateCombinacion = $this->pdo->prepare("
                UPDATE CombinacionesMedicamentos 
                SET idTipo = ?, idPresentacion = ?, idUnidad = ? 
                WHERE idCombinacion = ?
            ");
            $queryUpdateCombinacion->execute([$idTipo, $idPresentacion, $idUnidad, $idCombinacion]);
    
            // Confirmar transacción si todo es exitoso
            $this->pdo->commit();
    
            return true; // Retorna true si la transacción fue exitosa
        } catch (Exception $e) {
            // Deshacer cambios si hay un error
            $this->pdo->rollBack();
            error_log("Error al editar combinación completa de medicamento: " . $e->getMessage());
            return false;
        }
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

            // Verificar si la combinación fue registrada o validada correctamente
            if ($result && isset($result['mensaje'])) {
                if (strpos($result['mensaje'], 'combinación exacta') !== false || strpos($result['mensaje'], 'nueva combinación') !== false) {
                    // Devuelve éxito y el id de la combinación
                    return [
                        'status' => 'success', 
                        'message' => $result['mensaje'], 
                        'data' => [
                            'idCombinacion' => $result['idCombinacion']
                        ]
                    ];
                }
            }

            // Si no se obtiene el resultado esperado
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

    
     // Listar los tipos de medicamentos
    public function listarTiposMedicamentos() {
        try {
            // Ejecutar el procedimiento almacenado para listar los tipos de medicamentos
            $query = $this->pdo->prepare("CALL spu_listar_tipos_unicos()");
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

    // Listar las presentaciones de medicamentos según el tipo
    public function listarPresentacionesPorTipo($idTipo) {
        try {
            // Ejecutar el procedimiento almacenado para listar las presentaciones según el tipo de medicamento
            $query = $this->pdo->prepare("CALL spu_listar_presentaciones_por_tipo(?)");
            $query->execute([$idTipo]);
            return $query->fetchAll(PDO::FETCH_ASSOC); // Devolver las presentaciones filtradas por tipo
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
    // Método para listar lotes de medicamentos por nombre de medicamento
    public function listarLotesMedicamentosPorNombre($nombreMedicamento) {
        try {
            // Llama al procedimiento almacenado con el nombre del medicamento
            $query = $this->pdo->prepare("CALL spu_listar_lotes_medicamentos_por_nombre(:nombreMedicamento)");
            $query->bindParam(':nombreMedicamento', $nombreMedicamento, PDO::PARAM_STR);
            $query->execute();
            
            // Obtiene los resultados
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log(print_r($result, true)); // Log para depuración

            if ($result) {
                return ['status' => 'success', 'data' => $result];
            } else {
                return ['status' => 'error', 'message' => 'No se encontraron lotes para el medicamento especificado.'];
            }
        } catch (PDOException $e) {
            // Procesar el mensaje de error y registrar
            $errorMessage = preg_replace('/SQLSTATE\[\w+\]:/', '', $e->getMessage());
            $errorMessage = trim($errorMessage);
            error_log("Error al listar los lotes de medicamentos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $errorMessage];
        } catch (Exception $e) {
            error_log("Error inesperado al listar los lotes de medicamentos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    
    // Método para obtener la cantidad de equinos por categoría
    public function getEquinosPorCategoria() {
        try {
            // Preparar la llamada al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_contar_equinos_por_categoria()");
            $query->execute();

            // Obtener los resultados y devolverlos en formato asociativo
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Verificar si se encontraron resultados
            if (!empty($resultados)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No se encontraron equinos en las categorías especificadas.'];
            }

        } catch (PDOException $e) {
            // Registrar el error en los logs
            error_log("Error en la base de datos: " . $e->getMessage());

            // Devolver un mensaje de error al usuario
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    


    
}


