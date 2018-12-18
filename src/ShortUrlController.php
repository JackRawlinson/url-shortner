
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
    private $table_name = "shorturl";

    public function __construct(){
        $db_manager = new DBManager();
        $this->connection = $db_manager->getConnection();
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    // read products
    function read(){
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

    function shorten($url, $host) {
        $data = [ 'url' => $url];
        $statement = $this->connection->prepare("INSERT INTO shorturl (url) VALUES (:url)");

        if($statement->execute($data)) {
            $last = $this->connection->lastInsertId();

            $hashids = new Hashids('', 8);
            $hashid = $hashids->encode($last);

            $update_data = [
                'hashid' => $hashid,
                'id' => $last
            ];
            $update_statement = $this->connection->prepare("UPDATE shorturl SET hashid=:hashid WHERE id=:id");
            if($update_statement->execute($update_data)) {
                return "http://" . $host . "/" .  $hashid;
            } else {
                echo "Error: " . $update_statement . "<br>" . $this->connection->error;
            }
        } else {
            echo "Error: " . $statement . "<br>" . $this->connection->error;
        }

        return null;
    }

}