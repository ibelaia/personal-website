<?php

$envUrl = getenv('SUPABASE_URL') ?: ($_ENV['SUPABASE_URL'] ?? '');
$envKey = getenv('SUPABASE_KEY') ?: ($_ENV['SUPABASE_KEY'] ?? '');

define('SUPABASE_URL', $envUrl !== '' ? rtrim($envUrl, '/') : 'https://zuvlvslccrtalsbaukqi.supabase.co');
define('SUPABASE_KEY', $envKey !== '' ? $envKey : 'sb_publishable_rIzePKk5iQp_N7eZgOtKng_ODNpaCJN');

/**
 * Fungsi helper untuk mengirim request ke Supabase REST API
 *
 * @param string $table Nama tabel (contoh: 'articles', 'messages', 'translations')
 * @param string $method Metode HTTP: 'GET', 'POST', 'PATCH', 'DELETE'
 * @param array|null $data Data payload untuk POST/PATCH
 * @param string $query Query string URL (contoh: 'select=*&order=id.desc')
 * @return array Response status & data
 */
function supabase(string $table, string $method = 'GET', ?array $data = null, string $query = ''): array
{
    $url = SUPABASE_URL . '/rest/v1/' . ltrim($table, '/');
    if ($query !== '') {
        $url .= '?' . ltrim($query, '?');
    }

    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    $rawResponse = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
        'status' => $statusCode,
        'data' => json_decode((string) $rawResponse, true),
        'error' => $curlError,
    ];
}