<?php
session_start();

require_once '../models/Usuario.php';
$Usuario = new Usuario();

if (isset($_GET['operation'])){

  switch($_GET['operation']){
    case 'login':
      $login = [
        "permitido" => false,
        "apellidos" => "",
        "nombres"   => "",
        "idUsuario" => "",
        "idRol" => "",  // Actualizado para incluir 'idRol'
        "status"  => ""
      ];

      $row = $Usuario->login(['correo' => $_GET['correo']]);

      if (count($row) == 0){
        $login["status"] = "no existe el usuario";
      } else {
        $claveEncriptada = $row[0]['clave']; // de la BD
        $claveIngreso = $_GET['clave'];   // del formulario

        if(password_verify($claveIngreso , $claveEncriptada)){
          $login["permitido"] = true;
          $login["apellidos"] = $row[0]["apellidos"];
          $login["nombres"] = $row[0]["nombres"];
          $login["idUsuario"] = $row[0]["idUsuario"];
          $login["idRol"] = $row[0]["idRol"];  // Actualizado para incluir 'idRol'
        } else {
          $login["status"] = "contraseña incorrecta";
        }
      }

      $_SESSION['login'] = $login;
      $_SESSION['idUsuario'] = $login["idUsuario"]; // Agrega esta línea

      echo json_encode($login);
    break;

    case 'destroy':
      session_unset();
      session_destroy();
      header('Location:http://localhost/haras');
    break;
  }
}

if (isset($_POST['operation'])) {
  switch($_POST['operation']) {
    case 'add':
      $datos = [
        "idPersonal"   => $_POST['idPersonal'],
        "correo"       => $_POST['correo'],
        "clave"        => $_POST['clave'],
        "idRol"        => $_POST['idRol']  // Cambiado para usar 'idRol' en lugar de 'rol'
      ];
      $idobtenido = $Usuario->add($datos);
      echo json_encode(["idUsuario" => $idobtenido]); // Devuelve el valor a la vista como JSON
      
      break;
  }
}
