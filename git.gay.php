<?php
// Establecer a ruta do repositorio local
$repoDir = __DIR__ . '/AceStream_IDs';
$jsonPath = $repoDir . '/hashes.json';

// Desactivar a memoria de búfer de saída de PHP para transmitir a saída en tempo real
if (function_exists('apache_setenv')) {
  @apache_setenv('no-gzip', 1);
}
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) {
  ob_end_flush();
}
ob_implicit_flush(1);
?>
<!DOCTYPE html>
<html lang="gl">

<head>
  <meta charset="utf-8">
  <title>Links por data</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
    }

    .plain-output {
      background: #f4f4f4;
      padding: 15px;
      border-radius: 5px;
      font-family: monospace;
      margin-bottom: 30px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th {
      background: #eee;
      text-align: left;
      padding: 6px;
      font-size: 1.05em;
    }

    td {
      padding: 6px;
      border-top: 1px solid #ccc;
    }

    .status {
      background: #fff3cd;
      color: #856404;
      padding: 10px;
      border: 1px solid #ffeeba;
      margin-bottom: 20px;
      border-radius: 4px;
    }

    /* Estilos para o acordeón do Git Output */
    details.git-details {
      background: #1e1e1e;
      color: #00ff66;
      border-radius: 6px;
      margin-bottom: 25px;
      overflow: hidden;
      font-family: monospace;
    }

    details.git-details summary {
      background: #333;
      color: #fff;
      padding: 10px 15px;
      cursor: pointer;
      font-weight: bold;
      user-select: none;
    }

    details.git-details pre {
      margin: 0;
      padding: 15px;
      white-space: pre-wrap;
      word-break: break-all;
    }
  </style>
</head>

<body>

  <!-- ACORDEÓN DE GIT OUTPUT (aberto por defecto con 'open') -->
  <details class="git-details" open>
    <summary>🔄 Saída de `git pull origin main`</summary>
    <pre><?php
          // Executar git pull dentro do directorio do repositorio
          $cmd = "cd " . escapeshellarg($repoDir) . " && git pull origin main 2>&1";

          $handle = popen($cmd, 'r');
          if ($handle) {
            while (!feof($handle)) {
              $buffer = fgets($handle);
              echo htmlspecialchars($buffer);
              echo str_repeat(' ', 1024); // Forzar envío de buffer ao navegador
              flush();
            }
            pclose($handle);
          } else {
            echo "Erro ao tentar executar o comando git.";
          }
          ?></pre>
  </details>

  <?php
  // Comprobar se o arquivo existe despois do pull
  if (!file_exists($jsonPath)) {
    echo "<div style='color:red;'>Erro: O arquivo JSON non se atopou en: " . htmlspecialchars($jsonPath) . "</div></body></html>";
    exit;
  }

  // Ler e descodificar o JSON
  $jsonData = file_get_contents($jsonPath);
  $data = json_decode($jsonData, true);

  // Comprobar se se puido procesar o JSON
  if (!is_array($data) || !isset($data['hashes']) || !is_array($data['hashes'])) {
    echo "<div style='color:red;'>Erro: Formato JSON inválido ou a clave 'hashes' non existe.</div></body></html>";
    exit;
  }
  ?>

  <!-- SECCIÓN 1: Formato de texto simple / log -->
  <!-- 
  <h2>Liñas de texto</h2>
  <div class="plain-output">
    <?php
    foreach ($data['hashes'] as $item) {
      $tvgId = $item['tvg_id'] ?? '';
      $hash  = $item['hash'] ?? '';

      if (!empty($tvgId) && !empty($hash)) {
        echo "[" . date("H:i") . ", " . date("d/m/Y") . "] Alexandre Espinosa Menor: " . htmlspecialchars($tvgId) . ": acestream://" . htmlspecialchars($hash) . "<br>";
      }
    }
    ?>
  </div>
-->

  <!-- SECCIÓN 2: Formato en Táboa HTML -->
  <h2>Lista en Táboa</h2>
  <table>
    <tr>
      <th colspan="2"><?php echo date("d/m/Y"); ?></th>
    </tr>
    <?php foreach ($data['hashes'] as $item): ?>
      <?php
      $tvgId = $item['tvg_id'] ?? '';
      $hash  = $item['hash'] ?? '';
      ?>
      <?php if (!empty($tvgId) && !empty($hash)): ?>
        <tr>
          <td><?php echo htmlspecialchars($tvgId); ?></td>
          <td>
            <a href="acestream://<?php echo htmlspecialchars($hash); ?>">
              acestream://<?php echo htmlspecialchars($hash); ?>
            </a>
          </td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>

</body>

</html>
