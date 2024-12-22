<?php
// Aumentar o limite de memória
ini_set('memory_limit', '512M'); // Aumente conforme necessário

$file_path = "/storage/emulated/0/Download/Telegram/site_search_logs/logs.txt";
$logins = [];

if (file_exists($file_path)) {
    $lines = file($file_path, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        // Encontrando a posição do último e penúltimo ":"
        $lastColonPos = strrpos($line, ':');
        $secondLastColonPos = strrpos(substr($line, 0, $lastColonPos), ':');

        if ($lastColonPos !== false && $secondLastColonPos !== false) {
            $url = trim(substr($line, 0, $secondLastColonPos));
            $user = trim(substr($line, $secondLastColonPos + 1, $lastColonPos - $secondLastColonPos - 1));
            $password = trim(substr($line, $lastColonPos + 1));

            $logins[] = ['url' => $url, 'user' => $user, 'password' => $password];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($logins);
?>