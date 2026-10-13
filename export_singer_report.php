<?php

// Configuration
$sourceDb = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'auso_crm_demo',
    'username' => 'viraj',
    'password' => '1155335577',
    'charset' => 'utf8mb4'
];

$targetDb = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'auso_data_syncdb',
    'username' => 'viraj',
    'password' => '1155335577',
    'charset' => 'utf8mb4'
];

date_default_timezone_set('Asia/Colombo');

$statusMap = [
    '1' => 'Answered',
    '2' => 'Not Answered',
    '3' => 'Skipped',
    '4' => 'Canceled',
    '5' => 'Change Request',
    '6' => 'Not in Use',
    '22' => 'Not Answered',
    '41' => 'Canceled Answered',
    '42' => 'Canceled Not Answered',
    '51' => 'Change Request Answered',
    '52' => 'Change Request Not Answered',
    '222' => 'Not Answered',
];

$sourcePdo = new PDO(
    "mysql:host={$sourceDb['host']};port={$sourceDb['port']};dbname={$sourceDb['database']};charset={$sourceDb['charset']}",
    $sourceDb['username'],
    $sourceDb['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
);

$targetPdo = new PDO(
    "mysql:host={$targetDb['host']};port={$targetDb['port']};dbname={$targetDb['database']};charset={$targetDb['charset']}",
    $targetDb['username'],
    $targetDb['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
);

$yesterday = date('Y-m-d', strtotime('-1 day'));
$today = date('Y-m-d');

$query = "
    SELECT 
        id,
        priority_field AS work_order,
        status,
        call_status_option_id,
        comments,
        updated_at
    FROM ac_feed_contact_valids_report
    WHERE DATE(updated_at) = :yesterday
";

$stmt = $sourcePdo->prepare($query);
$stmt->execute(['yesterday' => $yesterday]);
$rows = $stmt->fetchAll();

if (empty($rows)) {
    // echo "No records found for {$yesterday}\n";
    exit;
}

$targetPdo->beginTransaction();

try {
    $insertQuery = "
        INSERT INTO to_singer (id, date, work_order, feedback, comment, updated_at)
        VALUES (:id, :date, :work_order, :feedback, :comment, :updated_at)
    ";
    $insertStmt = $targetPdo->prepare($insertQuery);
    
    $count = 0;
    foreach ($rows as $row) {
        $statusKey = (string)$row['status'];
        if (!empty($row['call_status_option_id'])) {
            $feedback = $row['call_status_option_id'];
        } else {
            $feedback = $statusMap[$statusKey] ?? $statusKey;
        }
        $comment = $row['comments'] ?? null;
        
        $insertStmt->execute([
            'id' => $row['id'],
            'date' => $yesterday,
            'work_order' => $row['work_order'],
            'feedback' => $feedback,
            'comment' => $comment,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $count++;
    }
    
    $targetPdo->commit();
    // echo "Successfully copied {$count} records from {$yesterday}\n";
} catch (Exception $e) {
    $targetPdo->rollBack();
    // echo "Error: " . $e->getMessage() . "\n";
}
