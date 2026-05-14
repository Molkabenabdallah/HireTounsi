<?php
session_start();
include "config.php";
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

$sender_id = $_SESSION["user_id"];
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$job_id = intval($_POST['job_id'] ?? 0);

if ($receiver_id <= 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, job_id, content, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$sender_id, $receiver_id, $job_id ?: null, $content]);
    $message_id = $pdo->lastInsertId();

    // Update or create conversation
    $user1 = min($sender_id, $receiver_id);
    $user2 = max($sender_id, $receiver_id);

    // Check if conversation exists
    $convStmt = $pdo->prepare("
        SELECT id FROM conversations 
        WHERE user1_id = ? AND user2_id = ?
    ");
    $convStmt->execute([$user1, $user2]);
    $conversation = $convStmt->fetch();

    if ($conversation) {
        // Update existing conversation
        $updateStmt = $pdo->prepare("
            UPDATE conversations 
            SET last_message = ?, last_message_at = NOW()
            WHERE id = ?
        ");
        $updateStmt->execute([$content, $conversation['id']]);
    } else {
        // Create new conversation
        $insertConv = $pdo->prepare("
            INSERT INTO conversations (user1_id, user2_id, last_message, last_message_at, created_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $insertConv->execute([$user1, $user2, $content]);
    }

    $pdo->commit();

    // Get sender info
    $senderStmt = $pdo->prepare("SELECT name, avatar_path FROM users WHERE id = ?");
    $senderStmt->execute([$sender_id]);
    $sender = $senderStmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => [
            'id' => $message_id,
            'sender_id' => $sender_id,
            'sender_name' => $sender['name'] ?? 'Utilisateur',
            'sender_avatar' => $sender['avatar_path'] ?? null,
            'content' => htmlspecialchars($content),
            'created_at' => date('Y-m-d H:i:s'),
            'is_mine' => true
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>