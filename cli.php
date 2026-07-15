<?

include __DIR__ . "/src/Framework/database.php";
require __DIR__ . "/../../vendor/autoload.php";

use Framework\Database;
use App\Config\Paths;
use DOTenv\Dotenv;

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$db = new Database($_ENV['DB_DRIVER'], [
    "host"  => $_ENV['DB_HOST'],
    "port" => $_ENV['DB_PORT'],
    "dbname" => $_ENV['DB_NAME'],
], $_ENV['DB_USER'], $_ENV['DB_PASS']);

try {
    //SQL Injection fix
    #$search = "Hats' OR 1=1 -- ";
    $search = 'Hats';

    #$query = "SELECT * FROM products";

    $db->query("INSERT INTO products VALUES(null,'Glovers')");

    $query = "SELECT * FROM products WHERE name=:name";

    $result = $db->select($query, ['name' => 'Glovers']);

    print_r($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
