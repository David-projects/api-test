<?

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExcptionMiddleware implements MiddlewareInterface
{
    /**
     * This files is used to set the session data so the template/views have access to
     * the form data and errors, as also remove the un-needed keys from the data here.
     * 
     * @param callable $next: next function to be called.
     */
    public function process(callable $next)
    {
        try {
            $next();
        } catch (ValidationException $e) {
            $oldFormData  = $_POST;
            $excludedkeys = array_merge(array_flip(array_keys($e->errors)), array_flip(['password', 'confirmPassword']));

            $formattedFormData = array_diff_key($oldFormData, $excludedkeys);

            $_SESSION['errors'] = $e->errors;
            $_SESSION['oldFormData'] = $formattedFormData;

            $referer = $_SERVER['HTTP_REFERER'];
            redirectTo($referer);
        }
    }
}
