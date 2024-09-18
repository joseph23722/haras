<?php

require_once 'Conexion.php';

class ServicioMixto extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Registrar un nuevo servicio mixto
    public function registrarServicioMixto($params = []) {
        try {
            // Verificar si viene idPadrillo o idYegua y establecerlo como idEquinoSeleccionado
            if (isset($params['idPadrillo'])) {
                $idEquinoSeleccionado = $params['idPadrillo'];
            } elseif (isset($params['idYegua'])) {
                $idEquinoSeleccionado = $params['idYegua'];
            } else {
                throw new Exception("Faltan parámetros necesarios: ni Padrillo ni Yegua seleccionados.");
            }
    
            // Verificar si se seleccionó un haras existente o si se está creando uno nuevo
            if (!empty($params['idHaras'])) {
                // Si se selecciona un haras existente, usamos su ID y dejamos nombreHaras como null
                $nombreHaras = null;
                $idHaras = $params['idHaras'];
            } elseif (!empty($params['nombreHaras'])) {
                // Si se está creando un nuevo haras
                $nombreHaras = $params['nombreHaras'];
                $idHaras = null;
            } else {
                throw new Exception("Faltan parámetros: debe seleccionar o registrar un Haras.");
            }
    
            // Verificar que todos los demás parámetros necesarios estén presentes
            if (empty($params['nombreNuevoEquino']) || empty($params['idTipoEquino']) || empty($params['fechaServicio']) || empty($params['detalles']) || empty($params['horaEntrada']) || empty($params['horaSalida'])) {
                throw new Exception("Faltan parámetros necesarios para registrar el servicio.");
            }
    
            // Preparar la consulta SQL con los 9 parámetros que espera el procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_registrar_servicio_mixto(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
            // Ejecutar la consulta con los 9 parámetros
            $query->execute([
                $idEquinoSeleccionado,         // 1. ID del equino seleccionado (Padrillo o Yegua)
                $params['nombreNuevoEquino'],  // 2. Nombre del nuevo equino
                $params['idTipoEquino'],       // 3. Tipo del nuevo equino (1 = Yegua, 2 = Padrillo)
                $nombreHaras,                  // 4. Nombre del nuevo haras (NULL si se selecciona uno existente)
                $idHaras,                      // 5. ID del haras existente (NULL si se crea uno nuevo)
                $params['fechaServicio'],      // 6. Fecha del servicio
                $params['detalles'],           // 7. Detalles del servicio
                $params['horaEntrada'],        // 8. Hora de entrada
                $params['horaSalida']          // 9. Hora de salida
            ]);
    
            // Verificar si la consulta fue exitosa
            if ($query->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Servicio mixto registrado exitosamente."
                ];
            } else {
                throw new Exception("Error al registrar el servicio mixto.");
            }
        } catch (PDOException $e) {
            error_log("Error en la base de datos: " . $e->getMessage());
            return [
                "status" => "error",
                "message" => "Error en la base de datos: " . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
    
    

    // Listar equinos por tipo (yegua o padrillo)
    public function listarEquinosPorTipo($tipoEquino) {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_por_tipo(?)");
            $query->execute([$tipoEquino]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar equinos por tipo: " . $e->getMessage());
            return [];
        }
    }

    // Listar haras (propietarios)
    public function listarHaras() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_haras()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar haras: " . $e->getMessage());
            return [];
        }
    }

    // Listar medicamentos con sus detalles
    public function listarMedicamentosConDetalles() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_medicamentos_con_detalles()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar medicamentos con detalles: " . $e->getMessage());
            return [];
        }
    }
}
