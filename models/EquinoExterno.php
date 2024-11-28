<?php
require_once 'Conexion.php';

class EquinoExterno extends Conexion
{
  private $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  // Listar todos los equinos que tengan un propietario que no sea NULL
  public function listarEquinosExternos()
  {
    try {
      // Preparar la llamada al procedimiento almacenado
      $query = $this->pdo->prepare("CALL spu_listar_equinos_externos()");
      $query->execute();
      $equinos = $query->fetchAll(PDO::FETCH_ASSOC);
      return $equinos ?: [];
    } catch (PDOException $e) {
      // Capturar cualquier excepción y retornar un error
      error_log("Error en listarEquinosExternos: " . $e->getMessage());
      return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
    }
  }
}
