<?php include_once('../../include/session_admin.php');; ?>
<?php include('../../include/sidebar.php'); ?>

<?php
// មុខងារតភ្ជាប់ទៅមូលដ្ឋានទិន្នន័យ
function getConnection()
{
    $host = 'localhost';
    $dbname = 'lms';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("មិនអាចតភ្ជាប់ទៅមូលដ្ឋានទិន្នន័យបានទេ: " . $e->getMessage());
    }
}

// ទាញយកទិន្នន័យពីមូលដ្ឋានទិន្នន័យ
function fetchTelegramData()
{
    $pdo = getConnection();
    try {
        $sql = "SELECT * FROM telegram_data"; // អ្នកអាចបន្ថែម LIMIT ឬលក្ខខណ្ឌផ្សេងៗបានប្រសិនបើចាំបាច់
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "មានបញ្ហា​ក្នុងការទាញយកទិន្នន័យ: " . $e->getMessage();
        return [];
    }
}

$telegramData = fetchTelegramData();
?>

<div class="container"><br><br>
    <div class="page-inner">
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>" role="alert">
                <?php echo $_SESSION['alert']['message']; ?>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <!-- តារាងអ្នកប្រើប្រាស់ -->
        <div class="row">

            <div class="col-md-12">

                <div class="card">

                    <div class="card-header">
                        <p class="card-title fs-15">Telegram_bot</p>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Token</th>
                                    <th>Telegram_Chat_ID</th>
                                    <th>សកម្មភាព</th>
                                </tr>
                                <?php foreach ($telegramData as $data): ?>
                                    <tr>
                                        <td><?php echo $data['id']; ?></td>
                                        <td><?php echo htmlspecialchars($data['token']); ?></td>
                                        <td><?php echo htmlspecialchars($data['chat_id']); ?></td>
                                        <td>
                                            <a href="edit_telegram_data.php?id=<?php echo $data['id']; ?>"> <i class="fas fa-edit"></i>&nbsp;កែប្រែ</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>
<?php include_once('../../include/footer.html'); ?>