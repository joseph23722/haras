<?php

require_once 'Conexion.php';

class Editarequino extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Método para mapear valores de texto a IDs solo si están presentes
    private function mapValues($params)
    {
        $estadoMap = [
            'Vivo' => 1,
            'Muerto' => 0
        ];

        // Procesar propietario solo si está presente y no es 'Haras Rancho Sur'
        if (isset($params['idPropietario']) && $params['idPropietario'] !== null && strtolower($params['idPropietario']) !== strtolower('Haras Rancho Sur')) {
            if (!is_numeric($params['idPropietario'])) {
                $cmd = $this->pdo->prepare("SELECT idPropietario FROM propietarios WHERE nombreharas = ?");
                $cmd->execute([$params['idPropietario']]);
                $propietarioID = $cmd->fetchColumn();

                if (!$propietarioID) {
                    throw new Exception("El propietario '{$params['idPropietario']}' no existe.");
                }
                $params['idPropietario'] = $propietarioID;
            }
        } else {
            // Si no hay propietario o es "Haras Rancho Sur", dejar como NULL
            $params['idPropietario'] = null;
        }

        // Procesar estado de monta solo si está presente
        if (isset($params['idEstadoMonta']) && $params['idEstadoMonta'] !== null) {
            if (!is_numeric($params['idEstadoMonta'])) {
                $cmd = $this->pdo->prepare("SELECT idEstadoMonta FROM EstadoMonta WHERE nombreEstado = ? AND genero = ?");
                $cmd->execute([$params['idEstadoMonta'], $params['sexo'] ?? 'Macho']);
                $estadoMontaID = $cmd->fetchColumn();

                if (!$estadoMontaID) {
                    throw new Exception("El estado de monta '{$params['idEstadoMonta']}' no es válido para el sexo '{$params['sexo']}'");
                }
                $params['idEstadoMonta'] = $estadoMontaID;
            }
        }

        // Procesar estado solo si está presente
        if (!empty($params['estado'])) {
            if (isset($estadoMap[$params['estado']])) {
                $params['estado'] = $estadoMap[$params['estado']];
            } else {
                $params['estado'] = null; // Si no hay un estado válido, asignar null
            }
        }


        return $params;
    }

    public function editarEquino($params = []): int
    {
        try {
            // Mapear valores de texto a IDs solo para campos enviados
            $params = $this->mapValues($params);

            error_log("Llamando al procedimiento almacenado con: " . json_encode($params));

            // Llamar al procedimiento almacenado con los parámetros
            $cmd = $this->pdo->prepare("CALL spu_equino_editar(?, ?, ?, ?, ?)");
            $cmd->execute([
                $params['idEquino'], // Obligatorio
                $params['idPropietario'] ?? null, // Opcional
                $params['pesokg'] ?? null, // Opcional
                $params['idEstadoMonta'] ?? null, // Opcional
                $params['estado'] ?? null // Opcional
            ]);

            return 1; // Operación exitosa
        } catch (PDOException $e) {
            error_log("Error de base de datos al ejecutar spu_equino_editar: " . $e->getMessage());
            // Limpiar el mensaje de error para ocultar la parte específica del error SQL
            $mensajeError = preg_replace('/SQLSTATE\[.*?\]:.*?:\s*\d*\s*/', '', $e->getMessage());
            return 0; // Operación fallida
        } catch (Exception $e) {
            error_log("Error al ejecutar spu_equino_editar: " . $e->getMessage());
            throw new Exception("Error al editar el equino: " . $e->getMessage());
        }
    }
}
