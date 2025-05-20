<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$year = date('Y');
$archivoExcel = __DIR__ . "/excel/RecepcionEquiposWeb/hoja_datos_$year.xlsx";

if (!file_exists($archivoExcel)) {
    die("El excel no está disponible o no es accesible.");
}

$spreadsheet = IOFactory::load($archivoExcel);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de fichas</title>
    <link rel="icon" type="image/jpeg" href="assets/Runa.jpg" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body { padding: 20px; font-family: Arial; }
        h1 { color: #0a7373; }
        table { border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; }
        th { background-color: #0a7373; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .dataTables_filter input { padding: 5px; border-radius: 4px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Registro de fichas ('.$year.')</h1>
    <table id="tablaRegistros" class="display">
        <thead>
            <tr>';
foreach ($data[0] as $header) {
    echo '<th>'.htmlspecialchars($header).'</th>';
}
echo '          </tr>
        </thead>
        <tbody>';

array_shift($data); // Eliminar la fila de encabezados
foreach ($data as $row) {
    echo '<tr>';
    foreach ($row as $cell) {
        echo '<td>'.htmlspecialchars($cell).'</td>';
    }
    echo '</tr>';
}

echo '      </tbody>
    </table>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#tablaRegistros").DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                pageLength: 25,
                order: [[7, "desc"]] // Ordenar por fecha de entrada descendente
            });
        });
    </script>
</body>
</html>';
?>