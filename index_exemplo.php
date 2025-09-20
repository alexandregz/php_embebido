<?php
session_start();

// ----------------- CONFIG (hardcodeado) -----------------
define('USERNAME', 'xxxxxx');       // usuario hardcodeado
define('PASSWORD', 'xxxxxx'); // contrasinal hardcodeado (módao se queres)
// --------------------------------------------------------

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Login attempt
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user'], $_POST['pass'])) {
    $user = trim($_POST['user']);
    $pass = $_POST['pass'];

    if ($user === USERNAME && $pass === PASSWORD) {
        // Autenticado
        $_SESSION['authenticated'] = true;
        // Redirixe para evitar reenviar o formulario se se fai refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = 'Usuario ou contrasinal incorrecto.';
    }
}

// Se non está autenticado, amosa o formulario e remata
if (empty($_SESSION['authenticated'])):
?>
<!DOCTYPE html>
<html lang="gl">
<head>
<meta charset="utf-8">
<title>Acceso restrinxido</title>
<style>
  body { font-family: sans-serif; margin: 30px; }
  .box { max-width: 420px; margin: 0 auto; padding: 16px; border: 1px solid #ddd; border-radius: 6px; background:#fafafa; }
  input[type=text], input[type=password] { width:100%; padding:8px; margin:6px 0 12px; box-sizing:border-box; }
  input[type=submit]{ padding:8px 12px; }
  .error{ color: #c00; margin-bottom: 10px; }
  .hint{ font-size:0.9em; color:#666; margin-top:8px; }
</style>
</head>
<body>
  <div class="box">
    <h2>Identifícate</h2>
    <?php if ($error): ?><div class="error"><?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?></div><?php endif; ?>
    <form method="post" action="">
      <label>Usuario<br>
        <input type="text" name="user" required autofocus>
      </label>
      <label>Contrasinal<br>
        <input type="password" name="pass" required>
      </label>
      <input type="submit" value="Entrar">
    </form>
    <div class="hint">Nota: En produción, usa HTTPS e almacenamento seguro.</div>
  </div>
</body>
</html>
<?php
exit; // non executamos o resto se non está autenticado
endif;

// ----------------- Contido da páxina (só para usuarias autenticadas) -----------------


// php -S localhost:31337

// 2025-09-20
$links = "
[20:44, 1/9/2025] Alexandre Espinosa Menor: X. hhxx 1080 MultiAudio: xxx://outzzaawef

[20:45, 14/9/2025] Alexandre Espinosa Menor: X. hhxx 3: xxx://5r5qwwef
[20:57, 14/9/2025] Alexandre Espinosa Menor: X. hhxx 720: xxx://gg

[20:39, 20/9/2025] Alexandre Espinosa Menor: X. hhxx 1080: xxx://ff
[20:39, 20/9/2025] Alexandre Espinosa Menor: HI+ FFF --> ABC: xxx://yy
";



$porData = [];

$liñas = array_filter(array_map('trim', explode("\n", $links)));

// Regex: captura hora, logo data, logo nome, logo resto
$re = '/^\s*\[\d{1,2}:\d{2},\s*(\d{1,2}\/\d{1,2}\/\d{4})\]\s*([^:]*):\s*(.*)$/';

foreach ($liñas as $liña) {
    if (preg_match($re, $liña, $m)) {
        $data  = $m[1];   // só a data, ex: 20/9/2025
        $nome  = trim($m[2]);
        $resto = trim($m[3]);

        // split polo primeiro ":"
        $partes = explode(':', $resto, 2);
        if (count($partes) === 2) {
            $clave = trim($partes[0]);
            $valor = trim($partes[1]);
        } else {
            $clave = $resto;
            $valor = '';
        }

        $porData[$data][] = [
            'clave' => $clave,
            'valor' => $valor,
        ];
    }
}
// echo "<pre>";print_r($porData);echo "</pre>";
?>


<!DOCTYPE html>
<html lang="gl">
<head>
<meta charset="utf-8">
<title>Links por data</title>
<style>
  table { border-collapse: collapse; width: 100%; }
  th { background: #eee; text-align: left; padding: 6px; font-size: 1.05em; }
  td { padding: 6px; border-top: 1px solid #ccc; }
</style>
</head>
<body>

<?php
// (Opcional) ordenar por data ascendente segundo formato d/m/Y
uksort($porData, function($a, $b) {
    $da = DateTime::createFromFormat('j/n/Y', $a);
    $db = DateTime::createFromFormat('j/n/Y', $b);
    return $da <=> $db; // usa $db <=> $da para descendente
});
?>

<table>
  <?php foreach ($porData as $data => $items): ?>
    <tr>
      <th colspan="2"><?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?></th>
    </tr>
    <?php foreach ($items as $item): 
      $clave = $item['clave'] ?? '';
      $valor = $item['valor'] ?? '';
    ?>
      <tr>
        <td><?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?></td>
        <td>
          <a href="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
</table>

<hr>
<a href="?logout=1">Logout</a>

</body>
</html>



