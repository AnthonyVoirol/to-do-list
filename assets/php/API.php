<?php
require_once 'dbConfig.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupérer toutes les tâches de l'utilisateur connecté
        $stmt = $conn->prepare('SELECT id, task, description, importance, created_at, deadLine, isDone FROM tasks WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        $stmt->close();
        echo json_encode(['tasks' => $tasks]);
        break;

    case 'POST':
        // Ajouter une nouvelle tâche
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['task']) || empty($data['importance']) || empty($data['description']) || empty($data['deadLine'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields.']);
            exit;
        }

        $task = $data['task'];
        $description = $data['description'];
        $importance = $data['importance'];
        $deadLine = $data['deadLine'];

        $stmt = $conn->prepare('INSERT INTO tasks (user_id, task, description, importance, deadLine, created_at, isDone) VALUES (?, ?, ?, ?, ?, NOW(), 0)');
        $stmt->bind_param('issss', $user_id, $task, $description, $importance, $deadLine);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $response = ['message' => 'Task added successfully.'];
        } else {
            http_response_code(500);
            $response = ['error' => 'Failed to add task.'];
        }

        $stmt->close();
        echo json_encode($response);
        break;

    case 'PUT':
        // Mettre à jour une tâche existante
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['id']) || empty($data['task']) || empty($data['importance']) || empty($data['description']) || empty($data['deadLine'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields.']);
            exit;
        }

        $task_id = $data['id'];
        $task = $data['task'];
        $description = $data['description'];
        $importance = $data['importance'];
        $deadLine = $data['deadLine'];

        $stmt = $conn->prepare('UPDATE tasks SET task = ?, description = ?, importance = ?, deadLine = ? WHERE id = ? AND user_id = ?');
        $stmt->bind_param('sssisi', $task, $description, $importance, $deadLine, $task_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response = ['message' => 'Task updated successfully.'];
        } else {
            http_response_code(500);
            $response = ['error' => 'Failed to update task or task not found.'];
        }

        $stmt->close();
        echo json_encode($response);
        break;

    case 'DELETE':
        // Supprimer une tâche
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task ID is required.']);
            exit;
        }

        $task_id = $data['id'];

        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response = ['message' => 'Task deleted successfully.'];
        } else {
            http_response_code(500);
            $response = ['error' => 'Failed to delete task or task not found.'];
        }

        $stmt->close();
        echo json_encode($response);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>
