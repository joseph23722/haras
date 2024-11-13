<?php
require_once 'Conexion.php';

class Alimento extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para obtener todos los tipos de alimento
    public function obtenerTiposAlimento() {
        try {
            $query = $this->pdo->prepare("CALL spu_obtenerTiposAlimento()");
            $query->execute();
            $tipos = $query->fetchAll(PDO::FETCH_ASSOC);
            return $tipos ?: []; // Retornar un array vacío si no hay resultados
        } catch (PDOException $e) {
            error_log("Error en obtenerTiposAlimento: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // Método para obtener las unidades de medida asociadas a un tipo de alimento específico
    public function obtenerUnidadesPorTipoAlimento($idTipoAlimento) {
        try {
            $query = $this->pdo->prepare("CALL spu_obtenerUnidadesPorTipoAlimento(?)");
            $query->execute([$idTipoAlimento]);
            $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
            return $unidades ?: []; // Retornar un array vacío si no hay resultados
        } catch (PDOException $e) {
            error_log("Error en obtenerUnidadesPorTipoAlimento: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    public function agregarTipoUnidadMedidaNuevo($tipoAlimento, $nombreUnidad) {
        try {
            // Preparar la llamada al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_agregarTipoUnidadMedidaNuevo(?, ?)");
    
            // Ejecutar el procedimiento con los parámetros para tipo de alimento y unidad de medida
            $query->execute([$tipoAlimento, $nombreUnidad]);
    
            // Retornar una respuesta de éxito si la operación se realizó sin errores
            return ['status' => 'success', 'message' => 'Tipo de alimento y unidad de medida agregados correctamente.'];
    
        } catch (PDOException $e) {
            // Capturar el error y registrar en el log si ocurre una excepción
            error_log("Error en agregarTipoUnidadMedidaNuevo: " . $e->getMessage());
    
            // Retornar un mensaje de error en caso de fallo
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    

    // Método para registrar un nuevo alimento
    public function registrarNuevoAlimento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar campos de entrada
            if (empty($params['nombreAlimento']) || $params['stockActual'] <= 0 || $params['costo'] <= 0 || $params['stockMinimo'] < 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, stock actual, stock mínimo y costo.');
            }

            // Validar el tipo de alimento
            $idTipoAlimento = $params['tipoAlimento'];
            $tiposAlimento = $this->obtenerTiposAlimento();
            if (isset($tiposAlimento['status']) || !$this->validarIdTipo($tiposAlimento, $idTipoAlimento)) {
                throw new Exception('Tipo de alimento no válido.');
            }

            // Validar la unidad de medida
            $unidadesMedida = $this->obtenerUnidadesPorTipoAlimento($idTipoAlimento);
            $idUnidadMedida = $params['unidadMedida'];
            if (isset($unidadesMedida['status']) || !$this->validarIdUnidad($unidadesMedida, $idUnidadMedida)) {
                throw new Exception('Unidad de medida no válida.');
            }

            // Verificar si el lote ya existe
            $verificacionLote = $this->verificarLote($params['lote'], $idUnidadMedida);
            if ($verificacionLote['status'] === 'error') {
                throw new Exception($verificacionLote['message']);
            }
            $idLote = $verificacionLote['idLote'];
            if ($idLote === null) {
                // Insertar lote si no existe
                $query = $this->pdo->prepare("INSERT INTO LotesAlimento (lote, fechaCaducidad, fechaIngreso) VALUES (?, ?, NOW())");
                $query->execute([$params['lote'], $params['fechaCaducidad']]);
                $idLote = $this->pdo->lastInsertId();
            }

            // Registrar el alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_nuevo(?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $idTipoAlimento,
                $idUnidadMedida,
                $params['lote'],
                $params['costo'],
                $params['fechaCaducidad'],
                $params['stockActual'],
                $params['stockMinimo']
            ]);

            return ['status' => 'success', 'message' => 'Alimento registrado exitosamente.'];

        } catch (PDOException $e) {
            error_log("Error en la base de datos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("Error inesperado: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Función para validar si un ID de tipo de alimento es válido
    private function validarIdTipo($tiposAlimento, $idTipoAlimento) {
        foreach ($tiposAlimento as $tipo) {
            if ($tipo['idTipoAlimento'] == $idTipoAlimento) {
                return true;
            }
        }
        return false;
    }

    // Función para validar si un ID de unidad de medida es válido para un tipo de alimento
    private function validarIdUnidad($unidadesMedida, $idUnidadMedida) {
        foreach ($unidadesMedida as $unidad) {
            if ($unidad['idUnidadMedida'] == $idUnidadMedida) {
                return true;
            }
        }
        return false;
    }


    
    
    

    // Método para registrar una entrada de alimento
    // Método para registrar una entrada de alimento
    public function registrarEntradaAlimento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $idUsuario = $_SESSION['idUsuario'] ?? null;
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }
    
            // Validaciones para nombre del alimento, cantidad, lote y unidad de medida
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || empty($params['lote']) || empty($params['unidadMedida'])) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, el lote, la unidad de medida y la cantidad.');
            }
    
            // Obtener el idUnidadMedida correspondiente a la unidad de medida asociada al alimento
            $unidadesMedida = $this->obtenerUnidadesPorAlimento($params['nombreAlimento']);
            
            // Validar que el ID de unidad de medida es correcto
            $idUnidadMedida = null;
            foreach ($unidadesMedida as $unidad) {
                if ($unidad['idUnidadMedida'] == $params['unidadMedida']) {  // Aquí se compara el ID
                    $idUnidadMedida = $unidad['idUnidadMedida'];
                    break;
                }
            }
    
            if (!$idUnidadMedida) {
                throw new Exception('La unidad de medida especificada no existe.');
            }
    
            // Log para depuración
            error_log("Llamada al procedimiento spu_alimentos_entrada con parámetros: idUsuario=$idUsuario, nombreAlimento={$params['nombreAlimento']}, lote={$params['lote']}, idUnidadMedida=$idUnidadMedida, cantidad={$params['cantidad']}");
    
            // Llamada al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_alimentos_entrada(?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $idUnidadMedida,
                $params['lote'],
                $params['cantidad']
            ]);
    
            return ['status' => 'success', 'message' => 'Entrada registrada exitosamente.'];
    
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log("Error en la base de datos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log("Error general: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    


    // Método para registrar una salida de alimento
    public function registrarSalidaAlimento($params = []) {
        try {
            // Iniciar sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el ID de usuario desde la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            // Verificar que el usuario esté autenticado
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los datos necesarios
            if (empty($params['nombreAlimento']) || !is_numeric($params['cantidad']) || $params['cantidad'] <= 0 || 
                empty($params['idEquino']) || empty($params['unidadMedida']) || 
                !isset($params['uso']) || !isset($params['merma'])) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, la cantidad, el uso, la merma, la unidad de medida y el tipo de equino.');
            }

            // Verificar que la suma de uso y merma sea igual a la cantidad total
            if ($params['cantidad'] != ($params['uso'] + $params['merma'])) {
                throw new Exception('La cantidad de uso y merma deben sumar el total de la salida.');
            }

            // Asignar valores opcionales (lote)
            $lote = !empty($params['lote']) ? $params['lote'] : null;

            // Validar que la merma y el uso sean numéricos y mayores o iguales a cero
            if ($params['merma'] < 0 || $params['uso'] < 0) {
                throw new Exception('La merma y el uso deben ser valores numéricos mayores o iguales a 0.');
            }

            // `unidadMedida` ya debería ser el ID, por lo que no necesitamos hacer una búsqueda
            $idUnidadMedida = $params['unidadMedida'];
            
            // Registrar en el log los datos enviados para depuración
            error_log("Llamada al procedimiento almacenado: spu_alimentos_salida con parámetros: idUsuario=$idUsuario, nombreAlimento={$params['nombreAlimento']}, unidadMedida={$idUnidadMedida}, cantidad={$params['cantidad']}, uso={$params['uso']}, merma={$params['merma']}, idEquino={$params['idEquino']}, lote={$lote}");

            // Llamar al procedimiento almacenado para registrar la salida de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_salida(?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $idUnidadMedida,  // Directamente usa el ID de la unidad de medida
                $params['cantidad'],
                $params['uso'],
                $params['merma'],
                $params['idEquino'],
                $lote   // Usar el valor exacto de lote
            ]);

            // Cerrar el cursor después de ejecutar la consulta
            $query->closeCursor();

            // Capturar y registrar cualquier warning que haya ocurrido
            $warnings = $this->pdo->query("SHOW WARNINGS")->fetchAll();
            if ($warnings) {
                foreach ($warnings as $warning) {
                    error_log("Warning de MySQL: " . implode(", ", $warning));
                }
            }

            return ['status' => 'success', 'message' => 'Salida registrada exitosamente con desglose de uso y merma.'];

        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log("Error en la base de datos (PDO): " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("Error general: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }




    // Método para notificar stock bajo
    public function notificarStockBajo() {
        try {
            // Preparar la consulta para llamar al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_notificar_stock_bajo_alimentos()");

            // Ejecutar el procedimiento sin parámetros
            $query->execute();

            // Recuperar los resultados
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Devolver los resultados si se encuentran notificaciones
            if (!empty($resultados)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No hay productos con stock bajo o agotados.'];
            }

        } catch (PDOException $e) {
            // Capturar errores SQL específicos del procedimiento almacenado (45000)
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }

            // Registrar el error exacto de PDO en los logs
            error_log("Error en la base de datos (PDO): " . $e->getMessage());

            // Devolver un mensaje genérico de error de base de datos
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Capturar y registrar otros errores generales
            error_log("Error general: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    // Método para obtener el historial de movimientos de alimentos
    public function obtenerHistorialMovimientos($params = []) {
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
    
            // Validar límite y desplazamiento (paginación)
            $limite = isset($params['limit']) && is_numeric($params['limit']) ? (int)$params['limit'] : 10;
            $desplazamiento = isset($params['offset']) && is_numeric($params['offset']) ? (int)$params['offset'] : 0;
            
            // ID del usuario: 0 para todos o un ID específico
            $idUsuario = isset($params['idUsuario']) && is_numeric($params['idUsuario']) ? (int)$params['idUsuario'] : 0;
    
            // Preparar la llamada al procedimiento almacenado con los parámetros necesarios
            $query = $this->pdo->prepare("CALL spu_historial_completo(?, ?, ?, ?, ?, ?)");
    
            // Ejecutar el procedimiento almacenado pasando los parámetros
            $query->execute([
                $tipoMovimiento,  // Tipo de movimiento (Entrada/Salida)
                $fechaInicio,     // Fecha de inicio
                $fechaFin,        // Fecha de fin
                $idUsuario,       // ID del usuario (0 para todos los usuarios)
                $limite,          // Límite de resultados
                $desplazamiento   // Desplazamiento para paginación
            ]);
    
            // Obtener los resultados y devolverlos
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
    
            // Verificar si hay resultados
            if (!empty($resultados)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No se encontraron movimientos en el rango de fechas seleccionado.'];
            }
    
        } catch (PDOException $e) {
            // Capturar y registrar cualquier error SQL
            error_log("Error en la base de datos: " . $e->getMessage());
    
            // Devolver un mensaje de error en caso de problemas en la base de datos
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            // Capturar y registrar cualquier otro tipo de error
            error_log("Error: " . $e->getMessage());
    
            // Devolver un mensaje de error general
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    

    // Método para obtener las unidades de medida asociadas a un alimento específico por nombre
    public function obtenerUnidadesPorAlimento($nombreAlimento) {
        try {
            // Preparar la consulta para obtener las unidades de medida asociadas a un alimento específico
            $query = $this->pdo->prepare("
                SELECT um.idUnidadMedida, um.nombreUnidad
                FROM Alimentos a
                JOIN UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida
                WHERE LOWER(a.nombreAlimento) = LOWER(:nombreAlimento)
                GROUP BY um.idUnidadMedida, um.nombreUnidad
            ");

            // Asignar el parámetro a la consulta
            $query->bindParam(':nombreAlimento', $nombreAlimento, PDO::PARAM_STR);

            // Ejecutar la consulta
            $query->execute();

            // Obtener los resultados
            $unidades = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retornar las unidades en un array
            return $unidades ?: [];

        } catch (PDOException $e) {
            // Registrar el error y devolver un mensaje de error
            error_log("Error en obtenerUnidadesPorAlimento: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }



    // Método para obtener alimentos y lotes, con opción de filtrar por un alimento específico
    public function getAllAlimentos($idAlimento = null) {
        try {
            // Preparar la llamada al procedimiento almacenado con el parámetro opcional
            $query = $this->pdo->prepare("CALL spu_obtenerAlimentosConLote(?)");
            $query->execute([$idAlimento]);

            // Obtener los resultados y devolverlos en formato asociativo
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Verificar si se encontraron resultados
            if (!empty($resultados)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No se encontraron alimentos registrados.'];
            }

        } catch (PDOException $e) {
            // Registrar el error en los logs
            error_log("Error en la base de datos: " . $e->getMessage());

            // Devolver un mensaje de error al usuario
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // Método para eliminar un alimento
    public function eliminarAlimento($idAlimento) {
        try {
            // Preparar la llamada al procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_eliminarAlimento(?)");
            
            // Ejecutar el procedimiento pasando el idAlimento
            $query->execute([$idAlimento]);

            return ['status' => 'success', 'message' => 'Alimento eliminado exitosamente.'];

        } catch (PDOException $e) {
            // Capturar y registrar errores específicos de SQL (como el error 45000 del procedimiento)
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }

            // Registrar cualquier otro error en los logs
            error_log("Error en la base de datos al eliminar alimento: " . $e->getMessage());

            // Devolver un mensaje de error al usuario
            return ['status' => 'error', 'message' => 'Error al eliminar el alimento.'];
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


    // Método para listar todos los lotes registrados
    public function listarLotes() {
        try {
            // Preparar la llamada al procedimiento almacenado para listar los lotes
            $query = $this->pdo->prepare("CALL spu_listar_lotes_alimentos()");
            $query->execute();
            
            // Obtener los resultados en formato asociativo
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para verificar el contenido de los resultados (útil para depuración)
            error_log(print_r($result, true));

            // Verificar si se obtuvieron resultados y retornar la respuesta
            if (!empty($result)) {
                return ['status' => 'success', 'data' => $result];
            } else {
                return ['status' => 'info', 'message' => 'No se encontraron lotes registrados.'];
            }

        } catch (PDOException $e) {
            // Registrar el error en los logs para diagnóstico
            error_log("Error al listar los lotes: " . $e->getMessage());
            // Devolver mensaje de error de base de datos
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Registrar cualquier otro error en los logs
            error_log("Error inesperado al listar los lotes: " . $e->getMessage());
            // Devolver mensaje de error inesperado
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }


    // Método para verificar si un lote ya está registrado con una unidad de medida específica
    public function verificarLote($lote, $idUnidadMedida) {
        try {
            // Verificar si el lote ya existe en combinación con la unidad de medida en la tabla Alimentos
            $query = $this->pdo->prepare("
                SELECT a.idLote 
                FROM Alimentos a
                JOIN LotesAlimento l ON a.idLote = l.idLote
                WHERE l.lote = :lote AND a.idUnidadMedida = :idUnidadMedida
            ");

            // Asignar valores a los parámetros de la consulta
            $query->bindParam(':lote', $lote, PDO::PARAM_STR);
            $query->bindParam(':idUnidadMedida', $idUnidadMedida, PDO::PARAM_INT);

            // Ejecutar la consulta
            $query->execute();

            // Obtener el resultado en formato asociativo
            $loteResult = $query->fetch(PDO::FETCH_ASSOC);

            // Si el lote ya existe, devolver el idLote
            if ($loteResult) {
                return ['status' => 'success', 'idLote' => $loteResult['idLote']];
            }

            // Si el lote no existe, devolver éxito con idLote como null
            return ['status' => 'success', 'idLote' => null];

        } catch (PDOException $e) {
            // Registrar error en el log y devolver mensaje de error
            error_log("Error al verificar el lote: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Manejar cualquier otro error inesperado
            error_log("Error inesperado al verificar el lote: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    //original


}
