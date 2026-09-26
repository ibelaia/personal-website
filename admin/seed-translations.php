<?php
require_once 'auth.php';
require_once __DIR__ . '/../supabase.php';

// Baca kedua file kamus
$enPath = __DIR__ . '/../lang/en.json';
$idPath = __DIR__ . '/../lang/id.json';

if (!file_exists($enPath) || !file_exists($idPath)) {
    die("File lang/en.json atau lang/id.json tidak ditemukan.");
}

$enData = json_decode(file_get_contents($enPath), true) ?? [];
$idData = json_decode(file_get_contents($idPath), true) ?? [];

// Gabungkan semua key unik
$allKeys = array_unique(array_merge(array_keys($enData), array_keys($idData)));
sort($allKeys);

$rows = [];
foreach ($allKeys as $key) {
    $rows[] = [
        'key'        => $key,
        'id_text'    => $idData[$key] ?? '',
        'en_text'    => $enData[$key] ?? '',
        'updated_at' => date('c')
    ];
}

// Kirim data ke Supabase per batch (50 baris per kirim) agar ringan
$chunks = array_chunk($rows, 50);
$totalSuccess = 0;

foreach ($chunks as $chunk) {
    $res = supabase('translations', 'POST', $chunk, 'on_conflict=key');
    if ($res['status'] >= 200 && $res['status'] < 300) {
        $totalSuccess += count($chunk);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Migrasi Terjemahan</title>
    <style>
        body {
            font-family: sans-serif;
            background: #0d1117;
            color: #c9d1d9;
            padding: 40px;
            text-align: center;
        }
        .card {
            max-width: 500px;
            margin: 0 auto;
            background: #161b22;
            padding: 24px;
            border-radius: 8px;
            border: 1px solid #30363d;
        }
        h2 { color: #3fb950; margin-top: 0; }
        a {
            display: inline-block;
            margin-top: 20px;
            background: #238636;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Migrasi Selesai!</h2>
        <p>Berhasil mengunggah <strong><?= $totalSuccess ?></strong> baris terjemahan ke tabel <code>translations</code> Supabase.</p>
        <a href="translations.php">Buka Editor Terjemahan →</a>
    </div>
</body>
</html>