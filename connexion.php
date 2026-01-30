<?php
class connecte{
    private $serveur;
    private $login;
    private $dbname;
    private $pass;

    public function  __construct(){
        $this->serveur = "localhost";
        $this->login  = "root";
        $this->dbname = "gestion_qalf";
        $this->pass = "";
    }

    public function conexion(){
        try{
            $connection = new PDO("mysql:host=$this->serveur;dbname=$this->dbname;charset=utf8;",$this->login,$this->pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(exception $e){
            echo "erreur : " .$e->getmessage();
        }
       return $connection;
    }
}

