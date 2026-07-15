<?

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class TransactionService
{
    public function __construct(private Database $db)
    {
    }

    public function create(array $formData)
    {

        $query = "INSERT INTO transaction(user_id, description, amount, date)
        VALUES(:user_id, :description, :amount, :date)";

        $data = [
            'user_id' => $_SESSION['user'],
            'description' => $formData['description'],
            'amount' => $formData['amount'],
            'date' => "{$formData['date']} 00:00:00"
        ];

        $this->db->insert($query, $data);
    }

    public function getuserTransactions(int $length, int $offset)
    {
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_');

        $query = "SELECT *, DATE_FORMAT(date, '%d-%m-%Y') as formatted_date
        FROM transaction
        WHERE user_id = :user_id 
        AND description LIKE :description
        LIMIT {$length} OFFSET {$offset}";

        $data = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%"
        ];

        $transactions = $this->db->selectAll($query, $data);


        $transactions = array_map(function (array $transaction) {
            $query = "SELECT * FROM receipts WHERE transaction_id = :transactionId";

            $data = [
                ':transactionId' => $transaction['id']
            ];

            $transaction['receipts'] = $this->db->selectAll($query, $data);

            return $transaction;
        }, $transactions);

        $transactionCountQuery = "SELECT COUNT(*) 
        FROM transaction 
        WHERE user_id = :user_id 
        AND description LIKE :description";

        $amount = $this->db->count($transactionCountQuery, $data);

        return [$transactions, $amount];
    }

    public function getUserTransaction(string $id)
    {
        $query = "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date FROM transaction WHERE id = :id AND user_id = :user_id";

        $data = [
            'id' => $id,
            'user_id' => $_SESSION['user']
        ];

        return $this->db->select($query, $data);
    }

    public function update(array $formData, int $id)
    {
        $query = "UPDATE transaction
        SET description = :description,
        amount = :amount, 
        date = :date
        WHERE id = :id
        AND user_id = :user_id";

        $data = [
            'user_id' => $_SESSION['user'],
            'description' => $formData['description'],
            'amount' => $formData['amount'],
            'date' => "{$formData['date']} 00:00:00",
            'id' => $id
        ];

        $this->db->update($query, $data);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM transaction WHERE id = :id AND user_id = :user_id";

        $data = [
            'user_id' => $_SESSION['user'],
            'id' => $id
        ];

        $this->db->delete($query, $data);
    }
}