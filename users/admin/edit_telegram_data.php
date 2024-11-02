<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include session and database connection
include('../../include/session_admin.php');
include('../../include/sidebar.php');

// Database connection function
function getConnection()
{
    $host = 'localhost';
    $dbname = 'lms';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Could not connect to the database: " . $e->getMessage());
    }
}

// Get Telegram data by ID
function getTelegramDataById($id)
{
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM telegram_data WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update Telegram data
function updateTelegramData($id, $token, $chat_id)
{
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE telegram_data SET token = :token, chat_id = :chat_id WHERE id = :id");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $telegramData = getTelegramDataById($id);
} else {
    $telegramData = null; // In case the ID is not set
}
?>

<div class="container">
    <div class="page-inner"><br>

        <!-- Edit Telegram Data Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2>Edit Telegram Data</h2>
                        <?php if ($telegramData): ?>
                            <form method="POST" action="proccess_edit_telegram.php?id=<?php echo $id; ?>">

                                <div class="form-group">
                                    <label for="token">Token</label>
                                    <input type="text" name="token" class="form-control" value="<?php echo htmlspecialchars($telegramData['token']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="chat_id">Telegram Chat ID</label>
                                    <input type="text" name="chat_id" class="form-control" value="<?php echo htmlspecialchars($telegramData['chat_id']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        <?php else: ?>
                            <p>No data found for the specified ID.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>