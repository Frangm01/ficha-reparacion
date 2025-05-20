<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;

// ========== Configuración ==========
$year = date('Y');
$archivoExcel = __DIR__ . "/excel/RecepcionEquiposWeb/hoja_datos_$year.xlsx";
$logoFile     = __DIR__ . '/assets/DGI_LOGO_TRANSP.png';

// Verificar existencia de logo
if (!file_exists($logoFile)) {
    die("Error: no se encontró el logo en: $logoFile");
}

// Función para formatear fecha a europea
function formatearFecha($fecha) {
    if (empty($fecha)) return '';
    $dt = DateTime::createFromFormat('Y-m-d', $fecha);
    return $dt ? $dt->format('d-m-Y') : $fecha;
}

// Recoger datos del formulario 
$datos = [
    'Nombre'             => $_POST['nombre'] ?? '',
    'Correo electrónico' => $_POST['email'] ?? '',
    'Teléfono'           => $_POST['telefono'] ?? '',
    'Modelo equipo'              => $_POST['marca'] ?? '',
    'Contraseña'         => $_POST['contrasena'] ?? '',
    'Avería'             => $_POST['averia'] ?? '',
    'Observaciones'      => $_POST['observaciones'] ?? '',
    'Fecha entrada'      => formatearFecha($_POST['fecha_entrada'] ?? ''),
];

// Accesorios
$accesorios = $_POST['accesorios'] ?? [];
$otrosTexto = trim($_POST['otrosTexto'] ?? '');
if (in_array('Otros', $accesorios) && $otrosTexto !== '') {
    foreach ($accesorios as &$acc) {
        if ($acc === 'Otros') {
            $acc = "$otrosTexto";
        }
    }
    unset($acc);
}
$datos['Accesorios entregados'] = implode(', ', $accesorios);

// ========== Guardar en Excel ==========
if (file_exists($archivoExcel)) {
    $spreadsheet = IOFactory::load($archivoExcel);
    $sheet = $spreadsheet->getActiveSheet();
} else {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Encabezados según datos
    $encabezados = array_keys($datos);
    $sheet->fromArray($encabezados, NULL, 'A1');
    $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
}

// Insertar nueva fila de datos
$ultimaFila = $sheet->getHighestRow() + 1;
$col = 'A';
foreach ($datos as $valor) {
    $sheet->setCellValue($col . $ultimaFila, $valor);
    $col++;
}

// Guardar archivo Excel
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($archivoExcel);

// ========== Preparar PDF ==========
// Leer logo y convertir a base64
$logoData = base64_encode(file_get_contents($logoFile));
$logoSrc  = 'data:image/png;base64,' . $logoData;

// Opciones Dompdf
$options = new Options();
$options->setIsRemoteEnabled(true);
$dompdf = new Dompdf($options);

// Mensaje footer extraído de Excel
$mensajeFooter = <<<MSG
SE ACEPTA LA POSIBILIDAD DE FORMATEAR PARA PODER DESCARTAR PROBLEMAS DEL SO
Se informa al cliente que, si pasados tres meses del aviso de arreglo de su equipo no viniese a recogerlo,
se podrían cobrar gastos de almacenaje.

Rúa de Barcelona 23 - 25 (36203) Vigo  Tél: 986 493000  -  CIF: B36914380  -  mail: info@dgi.gal
MSG;

// Construir HTML del PDF
$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ficha de Reparación</title>
  <style>
    body { font-family: sans-serif; margin: 20px; color: #333; }
    header { display: flex; align-items: center; border-bottom: 2px solid #0a7373; padding-bottom: 10px; margin-bottom: 20px; }
    header img { max-height: 60px; margin-right: 20px; }
    header h1 { font-size: 24px; color: #0a7373; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background-color: #0a7373; color: white; text-align: left; width: 30%; }
    td { background-color: #f9f9f9; }
    tr:nth-child(even) td { background-color: #eef4f4; }
    .footer-text { font-size: 10px; color: #555; margin-top: 20px; white-space: pre-wrap; }
  </style>
</head>
<body>
  <header>
    <img src="{$logoSrc}" alt="Logo DGI">
    <h1>Ficha de Reparación</h1>
  </header>
  <table>

HTML;

foreach ($datos as $campo => $valor) {
    $valorHtml = nl2br(htmlspecialchars($valor));
    $campoHtml = htmlspecialchars($campo);
    $html .= "<tr><td>{$campoHtml}</td><td>{$valorHtml}</td></tr>";
}

$html .= <<<HTML
  </table>
  <div class="footer-text">{$mensajeFooter}</div>
</body>
</html>
HTML;

// Generar y enviar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('ficha_reparacion_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
exit;
?>