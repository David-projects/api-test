<?

declare(strict_types=1);

namespace Framework;

use Framework\Contracts\RuleInterface;
use Framework\Exceptions\ValidationException;

class Validator
{

    private array $rules = [];

    public function add(string $alias, RuleInterface $rule)
    {
        $this->rules[$alias] = $rule;
    }

    public function validate(array $formData, array $fields)
    {
        $errors = [];
        foreach ($fields as $fieldName => $rules) {
            foreach ($rules as $rule) {
                $ruelParams = [];

                if (str_contains($rule, ':')) {
                    [$rule, $ruelParams] = explode(':', $rule);
                    $ruelParams = explode(',', $ruelParams);
                }

                $ruleValidator = $this->rules[$rule];

                if ($ruleValidator->validate($formData, $fieldName, $ruelParams)) {
                    continue;
                }
                $errors[$fieldName][] = $ruleValidator->getMessage($formData, $fieldName, $ruelParams);
            }
        }

        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }
}