<?php
  class Database{
      private $hostname = "localhost";
      private $database = "metricasolution";
      private $username = "root";
      private $password = "";
      private $charset = "utf8";
  
      function conectar(){
          try{
              $conexion = "mysql:host=". $this->hostname. ";dbname=". $this->database. ";charset=". $this->charset; // Corregido
              $options =[
                  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                  PDO::ATTR_EMULATE_PREPARES => false
              ];
  
              $pdo = new PDO($conexion, $this->username, $this->password, $options); // Corregido
  
              return $pdo;
          } catch(PDOException $e){
              echo 'Error de conexion: '. $e->getMessage();
              exit;
          }
      }
  }
?>