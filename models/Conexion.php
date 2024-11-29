<?php

class Conexion
{

  //1. Almacenamos los datos de conexión
  private $servidor = "localhost";
  private $puerto = "3306";
  private $baseDatos = "HarasDB";
  private $usuario = "root";
  private $clave = "";

  public function getConexion()
  {

    try {
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
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   * Listar Equinos :Retorna una colección de datos de la fuente (SPU) especificada
   */
  public function getData($spuName = ""): array
  {
    try {
      $cmd = $this->getConexion()->prepare("call {$spuName}()");
      $cmd->execute();
      return $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_log("Error: " . $e->getMessage());
      return []; // Devuelve un array vacío o un valor predeterminado
    }
  }

  /**
   * Ejecutar un archivo SQL completo (sentencias separadas por ;)
   * @param string $archivoSQL Ruta del archivo SQL a ejecutar
   * @return bool Retorna true si todo se ejecuta correctamente, false en caso contrario
   */
  // En tu clase Conexion

  public function ejecutarSQLCompletoDesdeArchivo($rutaArchivo)
  {
    // Obtener la conexión
    $conexion = $this->getConexion();

    // Asegúrate de que la conexión esté activa
    if (!$conexion) {
      echo "No se pudo establecer la conexión a la base de datos.";
      return false;
    }

    // Leer el contenido del archivo SQL
    $sql = file_get_contents($rutaArchivo);

    // Dividir el contenido del archivo por el delimitador ';'
    $consultas = explode(";", $sql);

    // Iniciar una transacción
    try {
      // Iniciar la transacción
      $conexion->beginTransaction();

      foreach ($consultas as $consulta) {
        $consulta = trim($consulta); // Limpiar posibles espacios en blanco

        // Si encontramos el comando DELIMITER, lo ignoramos
        if (stripos($consulta, 'DELIMITER') !== false) {
          continue;
        }

        // Asegúrate de que la consulta no esté vacía
        if (!empty($consulta)) {
          // Ejecutar cada consulta SQL
          $conexion->exec($consulta);
        }
      }

      // Si todo salió bien, hacer commit de la transacción
      $conexion->commit();
      echo "Todos los archivos se ejecutaron correctamente.";
      return true;
    } catch (PDOException $e) {
      // Si hay un error, hacer rollback para revertir la transacción
      if ($conexion->inTransaction()) {
        $conexion->rollBack();
      }

      // Registrar el error con más detalles
      error_log("Error al ejecutar el archivo SQL: " . $e->getMessage());
      error_log("Archivo: " . $rutaArchivo);
      error_log("Consulta fallida: " . $consulta);  // Mostrar la consulta que falló

      // Imprimir el error completo para la depuración
      echo "Error en la consulta SQL: " . $e->getMessage() . "<br>";
      echo "Consulta: " . $consulta . "<br>";
      echo "Archivo: " . $rutaArchivo . "<br>";

      return false;
    }
  }

  /**
   * Evita intentos de ataque a través de campos INPUT
   */
  public static function limpiarCadena($cadena): string
  {
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    $cadena = str_ireplace("<script>", "", $cadena);
    $cadena = str_ireplace("</script>", "", $cadena);
    $cadena = str_ireplace("<script src", "", $cadena);
    $cadena = str_ireplace("<script type", "", $cadena);

    $cadena = str_ireplace("SELECT * FROM", "", $cadena);
    $cadena = str_ireplace("DELETE FROM", "", $cadena);
    $cadena = str_ireplace("INSERT INTO", "", $cadena);

    $cadena = str_ireplace("DROP TABLE", "", $cadena);
    $cadena = str_ireplace("DROP DATABASE", "", $cadena);
    $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
    $cadena = str_ireplace("SHOW TABLES", "", $cadena);
    $cadena = str_ireplace("SHOW DATABASES", "", $cadena);

    $cadena = str_ireplace("<?php", "", $cadena);
    $cadena = str_ireplace("?>", "", $cadena);
    $cadena = str_ireplace("--", "", $cadena);
    $cadena = str_ireplace(">", "", $cadena);
    $cadena = str_ireplace("<", "", $cadena);
    $cadena = str_ireplace("[", "", $cadena);
    $cadena = str_ireplace("]", "", $cadena);

    $cadena = str_ireplace("^", "", $cadena); //ALT + 94
    $cadena = str_ireplace("==", "", $cadena);
    $cadena = str_ireplace("===", "", $cadena);
    $cadena = str_ireplace(";", "", $cadena);
    $cadena = str_ireplace("::", "", $cadena);
    $cadena = str_ireplace("('", "", $cadena);
    $cadena = str_ireplace("')", "", $cadena);

    $cadena = stripslashes($cadena);
    $cadena = trim($cadena);

    return $cadena;
  }
}