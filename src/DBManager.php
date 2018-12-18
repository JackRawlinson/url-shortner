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

        try{
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->connection;
    }

}