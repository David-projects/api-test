<?

declare(strict_types=1);

use Framework\Http;

function d($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}

function dd($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    die();
}


function e(mixed $value): string
{
    return htmlspecialchars((string) $value);
}

function redirectTo(string $path)
{
    header("Location: {$path}");
    http_response_code(Http::REDIRECT_STATUS_CODE);
    exit;
}