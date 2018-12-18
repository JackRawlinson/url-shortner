<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 17/12/18
 * Time: 11:54
 */

class DBManager
{
    private $host = "db";
    private $username = "shorturl";
    private $password = "password";
    private $database = "shorturl";

    private $connection;

    public function getConnection(){
        $this->connection = null;

        if(!empty($_ENV["DB_HOST"])) {
            $this->host = $_ENV["DB_HOST"];
        }
        if(!empty($_ENV["DB_SCHEMA"])) {
            $this->database = $_ENV["DB_SCHEMA"];
        }
        if(!empty($_ENV["DB_USERNAME"])) {
            $this->username = $_ENV["DB_USERNAME"];
        }
        if(!empty($_ENV["DB_PASSWORD"])) {
            $this->password = $_ENV["DB_PASSWORD"];
        }

        try{
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
        }catch(PDOException $exception){
            die("Connection error: " . $exception->getMessage());
        }

        return $this->connection;
    }

}