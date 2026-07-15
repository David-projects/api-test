<?

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

class ReceiptService
{
    public function __construct(private Database $db)
    {
    }

    public function validateFile(?array $file)
    {

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException([
                'receipt' => ['Failed to upload file.']
            ]);
        }

        $maxFileSizeMB = 3 * 1024 * 1024;

        if ($file['size'] > $maxFileSizeMB) {
            throw new ValidationException([
                'receipt' => ['File upload is to large.']
            ]);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationException([
                'receipt' => ['Invalid file name.']
            ]);
        }

        $clientmineType = $file['type'];
        $allowedMineTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if (!in_array($clientmineType, $allowedMineTypes)) {
            throw new ValidationException([
                'receipt' => ['Invalid file type.']
            ]);
        }
    }

    public function upload(array $file, int $transactionId)
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = bin2hex(random_bytes(16)) . ".{$fileExtension}";
        $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new ValidationException([
                'receipt' => ['Failed to upload the file.']
            ]);
        }

        $query = "INSERT INTO receipts(original_filename, storage_filename, media_type, transaction_id)
        VALUES(:original_filename, :storage_filename, :media_type, :transaction_id)";

        $data = [
            ':original_filename' => $file['name'],
            ':storage_filename' => $newFilename,
            ':media_type' => $file['type'],
            ':transaction_id' => $transactionId,
        ];

        $this->db->insert($query, $data);
    }

    public function getReceipt(string $id)
    {
        $query = "SELECT * FROM receipts WHERE id = :id";
        $data = [
            ':id' => $id
        ];

        return $this->db->select($query, $data);
    }

    public function delete(array $receipt)
    {
        $query = "DELETE FROM receipts WHERE id = :id";
        $data = [
            ':id' => $receipt['id']
        ];

        $this->db->delete($query, $data);

        $filePath = Paths::STORAGE_UPLOADS . "/" . $receipt['storage_filename'];
        unlink($filePath);
    }

    public function read(array $receipt)
    {
        $filePath = Paths::STORAGE_UPLOADS . "/" . $receipt['storage_filename'];

        if (!file_exists($filePath)) {
            redirectTo('/');
        }

        header("Content-Disposition: inline:filesname={$receipt['original_filename']}");
        header("Content-type:{$receipt['media_type']}");

        readfile($filePath);
    }
}