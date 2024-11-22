<?php

require_once 'Conexion.php';

class Editarequino extends Conexion
{
  private $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  // Método para editar un equino
  public function editarEquino($params = []): int
  {
    try {
      // Preparar la llamada al procedimiento almacenado
      $cmd = $this->pdo->prepare("CALL spu_equino_editar(?, ?, ?, ?, ?, ?)");

      $cmd->execute([
        $params['idEquino'],
        $params['nombreEquino'],
        $params['idPropietario'],
        $params['pesokg'],
        $params['idEstadoMonta'],
        $params['estado']
      ]);

      return 1; // Retorna 1 si la operación fue exitosa
    } catch (Exception $e) {
      error_log("Error al editar equino: " . $e->getMessage());
      return -1; // Retorna -1 si ocurre un error
    }
  }
}
