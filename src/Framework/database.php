<?

declare(strict_types=1);

namespace Framework;

use PDO, PDOException, Exception,  PDOStatement;

class Database
{
    private $connection;
    private $lastInsertedId = 0;

    public function __construct(string $driver, array $config, string $username, string $password)
    {
        $config = http_build_query(data: $config, arg_separator: ';');

        $dns = "{$driver}:{$config}";

        try {
            $this->connection = new PDO($dns, $username, $password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        } catch (PDOException $e) {
            die("Unable to connect to the DB");
        }
    }

    public function count(string $query, array $data = [])
    {
        try {
            $stmt = $this->query($query, $data);

            return $stmt->fetchColumn();
        } catch (Exception $e) {
            print("transaction failed");
            print($e->getMessage());
            throw new Exception("Error with database query counting rows");
        }
    }

    public function insert(string $query, array $data = [])
    {
        try {
            $this->lastInsertedId = 0;
            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare($query);

            $stmt = $this->bindValues($stmt, $data);

            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $this->lastInsertedId = $this->connection->lastInsertId();
            }

            $this->connection->commit();

            return $stmt->rowCount() > 0 ? $stmt->rowCount() : 0;
        } catch (Exception $e) {
            print("transaction failed");
            print($e->getMessage());
            throw new Exception("Error with database query inserting data");
        }
    }

    public function getLastInsertedId()
    {
        return $this->lastInsertedId;
    }

    public function select(string $query, array $data = [])
    {
        try {
            $stmt = $this->query($query, $data);

            return  $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            print("transaction failed");
            print($e->getMessage());
            throw new Exception("Error with database query getting fetch");
        }
    }

    public function selectAll(string $query, array $data = [])
    {
        try {
            $stmt = $this->query($query, $data);

            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            print("transaction failed");
            print($e->getMessage());
            throw new Exception("Error with database query getting fatch all");
        }
    }

    public function update($query, $data)
    {
        $stmt = $this->query($query, $data);
        return $stmt->rowCount();
    }

    public function delete($query, $data)
    {
        $this->query($query, $data);
    }

    private function bindValues($stmt, array $data = []): PDOStatement
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        return $stmt;
    }

    private function query(string $query, array $data = []): PDOStatement
    {
        try {
            $this->connection->beginTransaction();
            $stmt = $this->connection->prepare($query);

            $stmt = $this->bindValues($stmt, $data);

            $stmt->execute();

            $this->connection->commit();

            return $stmt;
        } catch (Exception $e) {
            print("transaction failed");
            print($e->getMessage());
            throw new Exception("Error with database query");
        }
    }
}