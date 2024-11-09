<?php
require_once 'Conexion.php';

class Alimento extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar un nuevo alimento
    public function registrarNuevoAlimento($params = [])
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            if (empty($params['nombreAlimento']) || $params['stockActual'] <= 0 || $params['costo'] <= 0 || $params['stockMinimo'] < 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, stock actual, stock mínimo y costo.');
            }

            // Verificar si el lote ya existe con la unidad de medida
            $verificacionLote = $this->verificarLote($params['lote'], $params['unidadMedida']);
            if ($verificacionLote['status'] === 'error') {
                throw new Exception($verificacionLote['message']);
            }

            $idLote = $verificacionLote['idLote'];
            if ($idLote === null) {
                // Si el lote no existe, crearlo
                $query = $this->pdo->prepare("INSERT INTO LotesAlimento (lote, unidadMedida, fechaCaducidad, fechaIngreso) VALUES (?, ?, ?, NOW())");
                $query->execute([$params['lote'], $params['unidadMedida'], $params['fechaCaducidad']]);
                $idLote = $this->pdo->lastInsertId(); // Obtener el id del nuevo lote
            }

            // Registrar el alimento utilizando el idLote correcto
            $query = $this->pdo->prepare("CALL spu_alimentos_nuevo(?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],
                $params['unidadMedida'],
                $params['lote'],           // Este es el número de lote que se envió en el formulario
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

    // Método para registrar una entrada de alimento
    public function registrarEntradaAlimento($params = [])
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || empty($params['lote']) || empty($params['unidadMedida'])) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, el lote, la unidad de medida y la cantidad.');
            }

            // Registrar en el log los datos enviados para depuración
            error_log("Llamada al procedimiento spu_alimentos_entrada con parámetros: idUsuario=$idUsuario, nombreAlimento={$params['nombreAlimento']}, lote={$params['lote']}, unidadMedida={$params['unidadMedida']}, cantidad={$params['cantidad']}");

            $query = $this->pdo->prepare("CALL spu_alimentos_entrada(?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['unidadMedida'],
                $params['lote'],      // Enviar el valor exacto de `lote`
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
    public function registrarSalidaAlimento($params = [])
    {
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
            if (
                empty($params['nombreAlimento']) || !is_numeric($params['cantidad']) || $params['cantidad'] <= 0 ||
                empty($params['idTipoEquino']) || empty($params['unidadMedida'])
            ) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, la cantidad, la unidad de medida y el tipo de equino.');
            }

            // Asignar valores opcionales (lote y merma)
            $lote = !empty($params['lote']) ? $params['lote'] : null;
            $merma = isset($params['merma']) && is_numeric($params['merma']) ? $params['merma'] : 0;

            // Validar que la merma sea numérica y mayor o igual a cero
            if ($merma < 0) {
                throw new Exception('La merma debe ser un valor numérico mayor o igual a 0.');
            }

            // Registrar en el log los datos enviados para depuración
            error_log("Llamada al procedimiento almacenado: spu_alimentos_salida con parámetros: idUsuario=$idUsuario, nombreAlimento={$params['nombreAlimento']}, unidadMedida={$params['unidadMedida']}, cantidad={$params['cantidad']}, idTipoEquino={$params['idTipoEquino']}, lote={$params['lote']}, merma={$merma}");

            // Llamar al procedimiento almacenado para registrar la salida de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_salida(?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['unidadMedida'],
                $params['cantidad'],
                $params['idTipoEquino'],
                $params['lote'],   // Usar el valor exacto de lote
                $merma
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

            return ['status' => 'success', 'message' => 'Salida registrada exitosamente.'];
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
    public function notificarStockBajo()
    {
        try {
            // Preparar la consulta para llamar al procedimiento almacenado sin parámetros
            $query = $this->pdo->prepare("CALL spu_notificar_stock_bajo_alimentos()");

            // Ejecutar el procedimiento
            $query->execute();

            // Obtener los resultados de la primera consulta (alimentos agotados)
            $agotados = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->nextRowset(); // Mover al siguiente conjunto de resultados

            // Obtener los resultados de la segunda consulta (alimentos con stock bajo)
            $bajoStock = $query->fetchAll(PDO::FETCH_ASSOC);

            // Combinar ambos resultados en un solo arreglo para devolver
            $resultados = [
                'agotados' => $agotados,
                'bajoStock' => $bajoStock
            ];

            // Devolver los resultados si se encuentran notificaciones
            if (!empty($agotados) || !empty($bajoStock)) {
                return ['status' => 'success', 'data' => $resultados];
            } else {
                return ['status' => 'info', 'message' => 'No hay productos con stock bajo o agotados.'];
            }
        } catch (PDOException $e) {
            // Capturar errores específicos de la base de datos
            error_log("Error en la base de datos (PDO): " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Capturar y registrar otros errores generales
            error_log("Error general: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para obtener el historial de movimientos de alimentos
    public function obtenerHistorialMovimientos($params = [])
    {
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
            $query = $this->pdo->prepare("CALL spu_historial_completo(?, ?, ?, ?, ?, ?)");

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

    // Método para obtener todos los alimentos
    public function getAllAlimentos()
    {
        try {
            $query = $this->pdo->prepare("Call spu_obtenerAlimentosConLote()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para eliminar un alimento
    public function eliminarAlimento($idAlimento)
    {
        try {
            $query = $this->pdo->prepare("DELETE FROM Alimentos WHERE idAlimento = ?");
            $query->execute([$idAlimento]);
            return ['status' => 'success', 'message' => 'Alimento eliminado exitosamente.'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error al eliminar el alimento.'];
        }
    }

    // Método para obtener los tipos de equinos
    public function getTipoEquinos()
    {
        try {
            // Cambiamos la llamada al nuevo procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_obtener_tipo_equino_alimento()");
            $query->execute();

            // Devolvemos los resultados como un arreglo asociativo
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registramos el error en el log
            error_log("Error en getTipoEquinos: " . $e->getMessage());
            return [];
        }
    }

    public function getUnidadesMedida($nombreAlimento)
    {
        $query = $this->pdo->prepare("SELECT unidadMedida FROM Alimentos WHERE nombreAlimento = :nombreAlimento");
        $query->execute(['nombreAlimento' => $nombreAlimento]);

        // Asumimos que se puede devolver más de una unidad de medida
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    // Listar todos los lotes registrados
    public function listarLotes()
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_lotes_alimentos()");
            $query->execute();

            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            // Verificar lo que se obtiene del procedimiento
            error_log(print_r($result, true));

            if ($result) {
                return ['status' => 'success', 'data' => $result];
            } else {
                return ['status' => 'error', 'message' => 'No se encontraron lotes registrados.'];
            }
        } catch (PDOException $e) {
            error_log("Error al listar los lotes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("Error inesperado al listar los lotes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    // Verificar si un lote ya está registrado con una unidad de medida específica
    public function verificarLote($lote, $unidadMedida)
    {
        try {
            // Prepara la consulta para verificar si el lote y la unidad de medida ya existen en la tabla de lotes
            $query = $this->pdo->prepare("
                SELECT idLote, unidadMedida 
                FROM LotesAlimento 
                WHERE lote = :lote AND unidadMedida = :unidadMedida
            ");
            $query->bindParam(':lote', $lote, PDO::PARAM_STR);
            $query->bindParam(':unidadMedida', $unidadMedida, PDO::PARAM_STR);
            $query->execute();

            $loteResult = $query->fetch(PDO::FETCH_ASSOC);

            // Si el lote ya existe, devolver el idLote
            if ($loteResult) {
                return ['status' => 'success', 'idLote' => $loteResult['idLote']];
            }

            // Si el lote no existe, devolver éxito para que se cree un nuevo lote
            return ['status' => 'success', 'idLote' => null];
        } catch (PDOException $e) {
            error_log("Error al verificar el lote: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            error_log("Error inesperado al verificar el lote: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }
}