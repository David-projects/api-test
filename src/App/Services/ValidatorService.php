<?

declare(strict_types=1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{
    RequiredRule,
    EmailRule,
    MinRule,
    InRule,
    UrlRule,
    MatchRule,
    LengthMaxRule,
    NumericRule,
    DateFormatRule
};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        // Add new vaildators here when creatding a new form.
        $this->validator = new Validator();
        $this->validator->add('required', new RequiredRule());
    }

    /**
     * Register from. This form is to register the user
     * This will need to be recreated for the next page
     * 
     * @param array $formData: data from the from on the page
     */
    public function validateRegister(array $formData)
    {
        // Add new vaildators here when creatding a new form.
        $this->validator->add('email', new EmailRule());
        $this->validator->add('min', new MinRule());
        $this->validator->add('in', new InRule());
        $this->validator->add('url', new UrlRule());
        $this->validator->add('match', new MatchRule());

        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'age' => ['required', 'min:18'],
            'country' => ['required', 'in:USA,Canada,Mexico'],
            'media' => ['required', 'url'],
            'password' => ['required'],
            'confirmPassword' => ['required', 'match:password'],
            'tos' => ['required'],
        ]);
    }

    /**
     * login from. This form is to login the user
     *  
     * @param array $formData: data from the from on the page
     */
    public function vaildateLogin(array $formData)
    {
        // Add new vaildators here when creatding a new form.
        $this->validator->add('email', new EmailRule());

        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    }

    /**
     * Transaction from. This form is for transactions the user enters
     *  
     * @param array $formData: data from the from on the page
     */
    public function validateTransaction(array $formData)
    {
        // Add new vaildators here when creatding a new form.
        $this->validator->add('lengthMax', new LengthMaxRule());
        $this->validator->add('numeric', new NumericRule());
        $this->validator->add('dateFormat', new DateFormatRule());

        $this->validator->validate($formData, [
            'description' => ['required', 'lengthMax:255'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'dateFormat:Y-m-d'],
        ]);
    }
}
