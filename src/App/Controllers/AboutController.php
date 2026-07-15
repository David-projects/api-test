<?

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Config\Paths;

class AboutController
{
    #private TemplateEngine $view;

    public function __construct(private TemplateEngine $view)
    {
        #$this->view = new TemplateEngine(Paths::VIEW);
    }

    public function index()
    {
        echo $this->view->render("/about.php", [
            "dangerousData" => "<script>alert(123)</script>"
        ]);
    }
}
