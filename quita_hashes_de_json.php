<?php

// Comprobar se se especificou a URL ou o ficheiro como argumento
if ($argc < 2) {
    echo "Uso: php " . $argv[0] . " <url_ou_ficheiro.json>\n";
    exit(1);
}

$source = $argv[1];
$jsonContent = false;

// Comprobar se é unha URL ou un ficheiro local
if (filter_var($source, FILTER_VALIDATE_URL)) {
    // É unha URL, usamos cURL para garantir que o servidor responda
    $ch = curl_init($source);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redireccións (moi común en gits)
    
    $jsonContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($jsonContent === false || $httpCode !== 200) {
        echo "Erro: Non se puido descargar a URL (Código HTTP: $httpCode).\n";
        exit(1);
    }
} else {
    // É un ficheiro local
    if (!file_exists($source)) {
        echo "Erro: O ficheiro local '$source' non existe.\n";
        exit(1);
    }
    $jsonContent = file_get_contents($source);
}

// Comprobar se se obtivo contido
if ($jsonContent === false) {
    echo "Erro: Non se puido obter o contido desde '$source'.\n";
    exit(1);
}

// Decodificar o contido do JSON
$data = json_decode($jsonContent, true);

// Comprobar se o JSON é válido
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erro: O contido recollido non é un JSON válido (" . json_last_error_msg() . ").\n";
    exit(1);
}

// A partir de aquí podes traballar co array $data
echo "JSON cargado correctamente desde: $source\n";


// Verificar se a estrutura do JSON é correcta
if (!$data || !isset($data['hashes']) || !is_array($data['hashes'])) {
    echo "Erro: O JSON non ten un formato válido ou falta a clave 'hashes'.\n";
    exit(1);
}

// Imprimir as liñas co formato desexado
foreach ($data['hashes'] as $item) {
    $tvgId = $item['tvg_id'] ?? '';
    $hash  = $item['hash'] ?? '';

    if (!empty($tvgId) && !empty($hash)) {
        echo "[".date("H:i").", ".date("d/m/Y")."] Alexandre Espinosa Menor: {$tvgId}: acestream://{$hash}\n";
    }
}
