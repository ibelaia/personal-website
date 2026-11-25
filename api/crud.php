<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../supabase.php';

$authFile = __DIR__ . '/../admin/auth.php';
if (file_exists($authFile)) {
    require_once $authFile;
}

function isAdminLoggedIn(): bool
{
    if (function_exists('is_admin_logged_in')) {
        return (bool) is_admin_logged_in();
    }
    if (function_exists('checkAdminAuth')) {
        return (bool) checkAdminAuth();
    }

    return !empty($_SESSION['admin_logged_in'])
        || !empty($_SESSION['admin_id'])
        || !empty($_SESSION['is_admin'])
        || !empty($_SESSION['user']);
}

function sendJson(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$rawInput = file_get_contents('php://input');
$bodyData = [];
if (is_string($rawInput) && trim($rawInput) !== '') {
    $decoded = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        sendJson(400, [
            'ok' => false,
            'message' => 'Payload JSON tidak valid.'
        ]);
    }
    $bodyData = $decoded;
}

$params = array_merge($_GET, $_POST, $bodyData);
$action = strtolower(trim((string) ($params['action'] ?? '')));
$table = trim((string) ($params['table'] ?? ''));

if ($action === '') {
    $action = match ($method) {
        'GET' => 'read',
        'POST' => 'create',
        'PATCH', 'PUT' => 'update',
        'DELETE' => 'delete',
        default => ''
    };
}

// Daftar tabel asli dari Supabase Anda
$allowedTables = [
    'about_settings',
    'achievements',
    'achievements_header',
    'appearance_settings',
    'articles',
    'certificates',
    'certificates_header',
    'contact_content',
    'contact_messages',
    'general_settings',
    'home_settings',
    'messages',
    'projects',
    'seo_settings',
    'services',
    'services_header',
    'translations',
    'visitor_logs'
];

if ($table === '' || !in_array($table, $allowedTables, true)) {
    sendJson(400, [
        'ok' => false,
        'message' => 'Tabel tidak valid atau tidak terdaftar.'
    ]);
}

// Operasi tulis (create, update, delete) wajib melewati cek sesi admin
$writeActions = ['create', 'insert', 'upsert', 'update', 'patch', 'delete'];
if (in_array($action, $writeActions, true) && !isAdminLoggedIn()) {
    sendJson(401, [
        'ok' => false,
        'message' => 'Unauthorized: silakan login admin terlebih dahulu.'
    ]);
}

$query = trim((string) ($params['query'] ?? ''));

switch ($action) {
    case 'read':
    case 'list':
    case 'get': {
        if ($query === '') {
            $query = 'select=*';
            if (!empty($params['id'])) {
                $query .= '&id=eq.' . rawurlencode((string) $params['id']);
            }
        }
        $result = supabase($table, 'GET', null, $query);
        break;
    }

    case 'create':
    case 'insert': {
        $data = $params['data'] ?? null;
        if (!is_array($data) || $data === []) {
            sendJson(400, [
                'ok' => false,
                'message' => 'Field "data" wajib diisi.'
            ]);
        }
        $result = supabase($table, 'POST', $data, $query);
        break;
    }

    case 'upsert': {
        $data = $params['data'] ?? null;
        if (!is_array($data) || $data === []) {
            sendJson(400, [
                'ok' => false,
                'message' => 'Field "data" wajib diisi untuk upsert.'
            ]);
        }
        $onConflict = trim((string) ($params['on_conflict'] ?? 'id'));
        $upsertQuery = $query !== '' ? $query : ('on_conflict=' . rawurlencode($onConflict));
        $result = supabase($table, 'POST', $data, $upsertQuery);
        break;
    }

    case 'update':
    case 'patch': {
        $data = $params['data'] ?? null;
        if (!is_array($data) || $data === []) {
            sendJson(400, [
                'ok' => false,
                'message' => 'Field "data" wajib diisi untuk update.'
            ]);
        }

        $filter = trim((string) ($params['filter'] ?? ''));
        if ($filter === '' && !empty($params['id'])) {
            $filter = 'id=eq.' . rawurlencode((string) $params['id']);
        }
        if ($filter === '') {
            sendJson(400, [
                'ok' => false,
                'message' => 'Parameter "id" atau "filter" wajib disertakan saat update.'
            ]);
        }

        $result = supabase($table, 'PATCH', $data, ltrim($filter, '&?'));
        break;
    }

    case 'delete': {
        $filter = trim((string) ($params['filter'] ?? ''));
        if ($filter === '' && !empty($params['id'])) {
            $filter = 'id=eq.' . rawurlencode((string) $params['id']);
        }
        if ($filter === '') {
            sendJson(400, [
                'ok' => false,
                'message' => 'Parameter "id" atau "filter" wajib disertakan saat delete.'
            ]);
        }

        $result = supabase($table, 'DELETE', null, ltrim($filter, '&?'));
        break;
    }

    default:
        sendJson(400, [
            'ok' => false,
            'message' => 'Action tidak didukung.'
        ]);
}

if (!empty($result['error'])) {
    sendJson(502, [
        'ok' => false,
        'message' => 'Koneksi cURL ke Supabase gagal: ' . $result['error'],
        'supabase' => $result
    ]);
}

$status = (int) ($result['status'] ?? 500);
$ok = $status >= 200 && $status < 300;

sendJson($ok ? 200 : $status, [
    'ok' => $ok,
    'status' => $status,
    'data' => $result['data'] ?? null
]);