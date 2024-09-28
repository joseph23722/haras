<?php

class Conexion{

  //1. Almacenamos los datos de conexión
  private $servidor = "localhost";
  private $puerto = "3306";
  private $baseDatos = "HarasDB";
  private $usuario = "root";
  private $clave = "";

  public function getConexion(){

    try{
      $pdo = new PDO(
        "mysql:host={$this->servidor};
        port={$this->puerto};
        dbname={$this->baseDatos};
        charset=UTF8", 
        $this->usuario, 
        $this->clave
      );

      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    }
    catch(Exception $e){
      die($e->getMessage());
    }
  }

  public function getData($spuName = ""):array{
    try{
      $cmd = $this->getConexion()->prepare("call {$spuName}()");
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){
      error_log("Error: " . $e->getMessage());
    }
  }

}