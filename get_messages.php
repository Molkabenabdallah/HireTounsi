<?php
session_start();
include "config.php";
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

$current_user = $_SESSION["user_id"];
$other_user = intval($_GET['user_id'] ?? 0);
$last_id = intval($_GET['last_id'] ?? 0);

if ($other_user <= 0) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur invalide']);
    exit();
}

try {
    // Mark messages as read
    $readStmt = $pdo->prepare("
        UPDATE messages SET is_read = 1 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ");
    $readStmt->execute([$other_user, $current_user]);

    // Get messages
    if ($last_id > 0) {
        $stmt = $pdo->prepare("
            SELECT m.*, u.name as sender_name, u.avatar_path as sender_avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.id > ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$current_user, $other_user, $other_user, $current_user, $last_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, u.name as sender_name, u.avatar_path as sender_avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
            LIMIT 100
        ");
        $stmt->execute([$current_user, $other_user, $other_user, $current_user]);
    }

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format messages
    foreach ($messages as &$msg) {
        $msg['is_mine'] = ($msg['sender_id'] == $current_user);
        $msg['content'] = htmlspecialchars($msg['content']);
        $msg['time'] = date('H:i', strtotime($msg['created_at']));
        $msg['date'] = date('d/m/Y', strtotime($msg['created_at']));
    }

    echo json_encode(['success' => true, 'messages' => $messages]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>