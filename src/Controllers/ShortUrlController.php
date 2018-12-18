<?php
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once dirname(__FILE__) . '/./DBManager.php';

use Hashids\Hashids;

/**
 * Class ShortUrlController
 */
class ShortUrlController
{
    private $connection;
    private $hashids;

    public function __construct(){
        $db_manager = new DBManager();
        $this->connection = $db_manager->getConnection();
        if ($this->connection->connect_error) {
            die("Cannot connect to Database: " . $this->connection->connect_error);
        }
        $this->hashids = new Hashids('', 8);
    }

    function get(){
        $data = null;
        $query = "SELECT * FROM shorturl";
        $statement = $this->connection->prepare($query);

        if($statement->execute()) {
            while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }
        return "Failed";
    }

    function getForHashId($hashid) {
        $id = $this->hashids->decode($hashid)[0];
        $data = [ 'id' => $id ];
        $statement = $this->connection->prepare("SELECT * FROM shorturl WHERE id=:id");

        if($statement->execute($data)) {
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if($result !== null) {
                $this->increment($result['id'], $result['visit_count']);
                return $result['url'];
            }
        } else {
            echo "Error: " . var_dump($statement) . "<br>" . $this->connection->error;
        }
        return null;
    }

    function shorten($url, $host) {
        $data = [ 'url' => $url];
        $statement = $this->connection->prepare("INSERT INTO shorturl (url) VALUES (:url)");

        if($statement->execute($data)) {
            $last = $this->connection->lastInsertId();
            $hashid = $this->hashids->encode($last);
            return "http://" . $host . "/" .  $hashid;
        } else {
            echo "Error: " . var_dump($statement). "<br>" . $this->connection->error;
        }
        return null;
    }

    /**
     * Increments the visited count of a url. Basic usage tracking of created URL's
     * @param $id
     * @param $count
     */
    private function increment($id, $count) {
        $data = [
            'id' => $id,
            'visit_count' => $count + 1
        ];
        $statement = $this->connection->prepare("UPDATE shorturl SET visit_count=:visit_count WHERE id=:id");
        if(!$statement->execute($data)) {
            echo "Failed to increment counter" . "<br>" . $this->connection->error;
        }
    }

}