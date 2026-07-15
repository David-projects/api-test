<?

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{

    public function __construct(private Database $db)
    {
    }

    public function isEmailTaken(string $email)
    {
        $data = [
            ':email' => $email
        ];

        $sql = "SELECT count(*) FROM users WHERE email = :email";

        $emailCount = $this->db->count($sql, $data);

        if ($emailCount > 0) {
            throw new ValidationException(['email' => ['Email taken']]);
        }
    }

    public function createUser(array $data)
    {
        $query = "INSERT INTO users(email,password,age,country,media) VALUES (:email, :password, :age, :country, :media)";
        $data = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'age' => $data['age'],
            'country' => $data['country'],
            'media' => $data['media'],
        ];

        $count = $this->db->insert($query, $data);

        session_regenerate_id();
        $_SESSION['user'] = $this->db->getLastInsertedId();
    }

    public function authUser(array $formData)
    {
        $query = "SELECT * FROM users WHERE email = :email";

        $data = [
            'email' => $formData['email'],
        ];

        $user = $this->db->select($query, $data);

        if (isset($user)) {
            if (!password_verify($formData['password'], $user['password'])) {
                throw new ValidationException(['email' => ['Email or password is worng'], 'password' => ['Email or password is worng']]);
            }
        } else {
            throw new ValidationException(['email' => ['Email or password is worng'], 'password' => ['Email or password is worng']]);
        }

        session_regenerate_id();
        $_SESSION['user'] = $user['id'];
    }

    public function logout()
    {
        //unset($_SESSION['user']);
        session_destroy();
        $params = session_get_cookie_params();
        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly'],
        );
    }
}