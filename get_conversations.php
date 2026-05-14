<?php
session_start();
include "config.php";
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

$current_user = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            CASE 
                WHEN c.user1_id = ? THEN c.user2_id 
                ELSE c.user1_id 
            END as other_user_id,
            u.name as other_user_name,
            u.avatar_path as other_user_avatar,
            u.role as other_user_role,
            c.last_message,
            c.last_message_at,
            CASE 
                WHEN c.user1_id = ? THEN c.unread_count_user1 
                ELSE c.unread_count_user2 
            END as unread_count
        FROM conversations c
        JOIN users u ON (
            CASE 
                WHEN c.user1_id = ? THEN c.user2_id 
                ELSE c.user1_id 
            END
        ) = u.id
        WHERE c.user1_id = ? OR c.user2_id = ?
        ORDER BY c.last_message_at DESC
    ");
    $stmt->execute([$current_user, $current_user, $current_user, $current_user, $current_user]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format conversations
    foreach ($conversations as &$conv) {
        $conv['last_message'] = htmlspecialchars($conv['last_message'] ?? '');
        $conv['time_ago'] = timeAgo($conv['last_message_at']);
        $conv['unread_count'] = intval($conv['unread_count'] ?? 0);

        // Get initials
        $name_parts = explode(' ', $conv['other_user_name']);
        $initials = '';
        foreach ($name_parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        $conv['initials'] = substr($initials, 0, 2);
    }

    echo json_encode(['success' => true, 'conversations' => $conversations]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

function timeAgo($datetime) {
    if (!$datetime) return '';
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) return "À l'instant";
    if ($diff < 3600) return floor($diff / 60) . ' min';
    if ($diff < 86400) return floor($diff / 3600) . 'h';
    if ($diff < 604800) return floor($diff / 86400) . 'j';
    return date('d/m/Y', $time);
}
?>