<?

/**
 * This class is for storing paths that will be needed thoughout the framework
 */

declare(strict_types=1);

namespace App\Config;

class Paths
{
    //views folder
    public const VIEW = __DIR__ . "/../views";
    public const SOURCE = __DIR__ . "/../../";
    public const ROOT = __DIR__ . "/../../../";

    public const STORAGE_UPLOADS = __DIR__ . "/../../../storage/uploads";
}