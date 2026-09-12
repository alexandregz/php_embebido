<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <title>Lista de Canles Acestream</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        a {
            color: #1e88e5;
            text-decoration: none;
            word-break: break-all;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Canles Dispoñibles</h2>

    <table>
        <thead>
            <tr>
                <th>Nome da Canle</th>
                <th>Ligazón Acestream</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $ficheiro = 'lista_m3u.txt';

            if (file_exists($ficheiro)) {
                // Lemos o ficheiro converténdoo nun array de liñas
                // FILE_IGNORE_NEW_LINES evita os saltos de liña ao final de cada elemento
                $linhas = file($ficheiro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                // Imos de 2 en 2 (i = liña impar/nome, i+1 = liña par/link)
                for ($i = 0; $i < count($linhas); $i += 2) {
                    // trim() limpa espazos en branco ou caracteres raros ao inicio e fin
                    $nome = trim($linhas[$i]);
                    
                    // Asegurámonos de que existe a liña do link por se o ficheiro está incompleto
                    $link = isset($linhas[$i + 1]) ? trim($linhas[$i + 1]) : '';

                    if (!empty($nome) && !empty($link)) {
                        echo "<tr>\n";
                        echo "  <td>" . htmlspecialchars($nome) . "</td>\n";
                        echo "  <td>\n";
                        echo '    <a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . "</a>\n";
                        echo "  </td>\n";
                        echo "</tr>\n";
                    }
                }
            } else {
                echo "<tr><td colspan='2'>Non se atopou o ficheiro $ficheiro</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
