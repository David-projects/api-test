<?

/**
 * load and configurer  the files needed to run the application
 */

declare(strict_types=1);

/**
 * Autoloader setup, update this file up updating composer.json
 * then run composer dump-autoload. this will create a new 
 * vendor/composer/autoload_psr4.php file
 * 
 * PSR are the standards used by this project please read for the standards
 * https://www.php-fig.org/
 * 
 */
require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use App\Config\Paths;
use App\Config\Routes;
use Dotenv\Dotenv;
use function App\Config\registerMiddleware;

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$app = new App(Paths::SOURCE . "App/container-definitions.php");
//setup routes for the framework
Routes::registerRouters($app);
registerMiddleware($app);

return $app;

#dependencies injection