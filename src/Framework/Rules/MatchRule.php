<?

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class MatchRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        $filedOne = $data[$field];
        $filedTwo = $data[$params[0]];

        return $filedOne === $filedTwo;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Does not match {$params[0]}.";
    }
}
