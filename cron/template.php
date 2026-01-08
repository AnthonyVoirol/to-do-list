<?php
require_once __DIR__ . '/../config/dbConfig.php';

$APP_ID = "";
$REST_API_KEY = "";

$tomorrow = date('Y-m-d', strtotime('+1 day'));

$query = $conn->prepare("
    SELECT tasks.id, tasks.task, tasks.deadLine, users.id AS user_id
    FROM tasks
    JOIN users ON tasks.user_id = users.id
    WHERE DATE(tasks.deadLine) = ?
      AND tasks.isDone = 0
");

$query->bind_param("s", $tomorrow);
$query->execute();
$result = $query->get_result();

$count = 0;
$successCount = 0;
$errorCount = 0;

while ($task = $result->fetch_assoc()) {
    $count++;
    $userId = (string)$task['user_id'];
    $taskName = $task['task'];

    $payload = [
        "app_id" => $APP_ID,
        "include_aliases" => [
            "external_id" => [$userId]
        ],
        "target_channel" => "push",
        "headings" => [
            "en" => "Task reminder",
            "fr" => "Rappel de tâche"
        ],
        "contents" => [
            "en" => "Your task \"$taskName\" is due tomorrow!",
            "fr" => "Votre tâche \"$taskName\" arrive à échéance demain !"
        ],
        "priority" => 10,
        "ios_interruption_level" => "time_sensitive"
    ];

    $ch = curl_init("https://api.onesignal.com/notifications?c=push");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json; charset=utf-8",
        "Authorization: Key " . $REST_API_KEY
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        $errorCount++;
        file_put_contents(__DIR__ . "/log.txt",
            "[ERROR] CURL Error: $curlError\n",
            FILE_APPEND
        );
    } else {
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            if (isset($responseData['id']) && !empty($responseData['id'])) {
                $successCount++;
            } else {
                $errorCount++;
                file_put_contents(__DIR__ . "/log.txt",
                    "[WARNING] User $userId not subscribed to push notifications\n",
                    FILE_APPEND
                );
            }
        } else {
            $errorCount++;
            file_put_contents(__DIR__ . "/log.txt",
                "[ERROR] HTTP $httpCode - Response: " . json_encode($responseData) . "\n",
                FILE_APPEND
            );
        }
    }
}

$query->close();
$conn->close();

echo "CRON OK - $count task(s) processed - $successCount success - $errorCount error(s)";
?>