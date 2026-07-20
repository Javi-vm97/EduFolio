<?php
/* EduFolio - Generador minimo de archivos .xlsx (Open XML) sin librerias externas.
   Usa ZipArchive y cadenas en linea (inlineStr). Compatible con PHP 7.2+. */

declare(strict_types=1);

/* Convierte un numero de columna (1 = A) a su letra: 1->A, 27->AA, etc. */
function xlsx_columna(int $n): string
{
    $s = '';
    while ($n > 0) {
        $m = ($n - 1) % 26;
        $s = chr(65 + $m) . $s;
        $n = intdiv($n - $m, 26);
    }
    return $s;
}

/* Escapa texto para XML. */
function xlsx_esc(string $t): string
{
    return htmlspecialchars($t, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/**
 * Genera un .xlsx a partir de una matriz (arreglo de filas) y lo envia al
 * navegador como descarga. Todas las celdas se escriben como texto.
 */
function xlsx_descargar(string $nombreArchivo, array $filas): void
{
    if (!class_exists('ZipArchive')) {
        // Respaldo: CSV si el servidor no tuviera la extension zip
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . preg_replace('/\.xlsx$/', '.csv', $nombreArchivo) . '"');
        echo "\xEF\xBB\xBF"; // BOM para acentos en Excel
        $out = fopen('php://output', 'w');
        foreach ($filas as $fila) {
            fputcsv($out, $fila);
        }
        fclose($out);
        exit;
    }

    // Construye el XML de la hoja
    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
    $r = 1;
    foreach ($filas as $fila) {
        $sheet .= '<row r="' . $r . '">';
        $c = 1;
        foreach ($fila as $valor) {
            $ref = xlsx_columna($c) . $r;
            $sheet .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">'
                . xlsx_esc((string)$valor) . '</t></is></c>';
            $c++;
        }
        $sheet .= '</row>';
        $r++;
    }
    $sheet .= '</sheetData></worksheet>';

    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '</Types>';

    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Asistencia" sheetId="1" r:id="rId1"/></sheets></workbook>';

    $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '</Relationships>';

    $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml', $contentTypes);
    $zip->addFromString('_rels/.rels', $rels);
    $zip->addFromString('xl/workbook.xml', $workbook);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
    $zip->close();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
    header('Content-Length: ' . filesize($tmp));
    header('Cache-Control: private');
    readfile($tmp);
    @unlink($tmp);
    exit;
}
