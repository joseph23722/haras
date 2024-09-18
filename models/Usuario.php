<?php

require_once 'Conexion.php';

class Usuario extends Conexion{

  private $pdo;

  public function __CONSTRUCT(){
      $this->pdo = parent::getConexion();
  }

  public function login($params = []){
    try {
      $query = $this->pdo->prepare("CALL spu_usuarios_login(?)");
      $query->execute(array($params['correo']));
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function add($params = []): int {
    $idUsuario = null;
    try {
      $query = $this->pdo->prepare("CALL spu_usuarios_registrar(?,?,?,?)");
      $query->execute(
        array(
          $params['idPersonal'],
          $params['idRol'],  // Cambiado para usar 'idRol' en lugar de 'rol'
          $params['correo'],
          password_hash($params['clave'], PASSWORD_BCRYPT)
        )
      );
      $row = $query->fetch(PDO::FETCH_ASSOC);
      $idUsuario = $row['idUsuario'];
    } catch (Exception $e) {
      $idUsuario = -1;
    }

    return $idUsuario;
  }
}
