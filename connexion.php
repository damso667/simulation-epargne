<?php
class connecte
{
    private $serveur;
    private $login;
    private $dbname;
    private $pass;

    public function  __construct()
    {
        $this->serveur = "sql309.infinityfree.com";
        $this->login  = "if0_41145457";
        $this->dbname = "if0_41145457_simulationbd";
        $this->pass = "t3SUbChEaWs";
    }

    public function conexion()
    {
        try {
            $connection = new PDO("mysql:host=$this->serveur;dbname=$this->dbname;charset=utf8;", $this->login, $this->pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (exception $e) {
            echo "erreur : " . $e->getmessage();
        }
        return $connection;
    }
}
