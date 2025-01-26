<?php
require_once('./controllers/db_connectie.php');

$conn = maakVerbinding();

$query = "SELECT COUNT(*) FROM [User] WHERE password NOT LIKE '%$2y$%'";
$stmt = $conn->prepare($query);
$stmt->execute();
$plainPasswordCount = $stmt->fetchColumn();

if ($plainPasswordCount > 0) {
    $query = "SELECT username, password FROM [User]";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $username = $user['username'];
        $plainPassword = $user['password'];

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $updateQuery = "UPDATE [User] SET password = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$hashedPassword, $username]);
    }

    echo "All passwords have been hashed and updated successfully.";
} else {
    echo "No plain passwords found. No updates needed.";
}
?>
