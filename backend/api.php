<?php
// Handle CORS
$allowedOrigins = ['http://localhost:5173', 'http://localhost:5174']; // Allowed origins
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);  // End preflight request
}

// Include required files
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$method = $_SERVER['REQUEST_METHOD'];

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/Library-Management/backend/api.php/';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$request = explode('/', trim($path, '/'));

$input = json_decode(file_get_contents('php://input'), true);

switch ($request[0]) {
    case 'books':
        if ($method == 'GET') {
            getBooks($pdo);
        } elseif ($method == 'POST') {
            addBook($pdo, $input);
        }
        break;
    case 'borrow':
        if ($method == 'POST') {
            borrowBook($pdo, $input);
        }
        break;
    case 'return':
        if ($method == 'POST') {
            returnBook($pdo, $input);
        }
        break;
    case 'borrowed':
        if ($method == 'GET') {
            getBorrowedBooks($pdo);
        }
        break;
    case 'register':
        if ($method == 'POST') {
            registerBorrower($pdo, $input);
        }
        break;
    case 'login':
        if ($method == 'POST') {
            loginUser($pdo, $input);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}

function getBooks($pdo) {
    $stmt = $pdo->query('SELECT * FROM books');
    echo json_encode($stmt->fetchAll());
}

function addBook($pdo, $input) {
    $stmt = $pdo->prepare('INSERT INTO books (title, author, isbn) VALUES (?, ?, ?)');
    $stmt->execute([$input['title'], $input['author'], $input['isbn']]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

function borrowBook($pdo, $input) {
    $stmt = $pdo->prepare('INSERT INTO borrowings (book_id, user_id, borrow_date, due_date) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))');
    $stmt->execute([$input['book_id'], $input['user_id']]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

function returnBook($pdo, $input) {
    $stmt = $pdo->prepare('UPDATE borrowings SET return_date = CURDATE() WHERE id = ? AND return_date IS NULL');
    $stmt->execute([$input['borrow_id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0]);
}

function getBorrowedBooks($pdo) {
    $stmt = $pdo->query('SELECT bb.*, b.title, b.author, u.name AS borrower_name FROM borrowings bb JOIN books b ON bb.book_id = b.id JOIN users u ON bb.user_id = u.id WHERE bb.return_date IS NULL');
    echo json_encode($stmt->fetchAll());
}

function registerBorrower($pdo, $input) {
    if (!isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, email and password are required']);
        return;
    }

    try {
        $user_id = register($pdo, $input['name'], $input['email'], $input['password'], 'borrower');
        echo json_encode(['id' => $user_id, 'message' => 'Borrower registered successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Registration failed. Email may already exist.']);
    }
}

function loginUser($pdo, $input) {
    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    $user = login($pdo, $input['email'], $input['password']);
    if ($user) {
        echo json_encode(['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}
