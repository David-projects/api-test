<?

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    ValidatorService,
    UserService
};

class LoginController
{

    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validatorService,
        private UserService $userService
    ) {
    }

    public function index()
    {
        echo $this->view->render("/login.php");
    }

    public function auth()
    {
        $this->validatorService->vaildateLogin($_POST);
        $this->userService->authUser($_POST);
        redirectTo('/');
    }

    public function logout()
    {
        $this->userService->logout();
        redirectTo('/login');
    }
}
