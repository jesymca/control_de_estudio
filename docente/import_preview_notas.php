<?php
require_once(__DIR__ . '/../funciones/functions.php');

ob_start();
header('Content-Type: application/json; charset=utf-8');

function respondJson($data, $httpCode = 200) {
    $extra = trim(ob_get_clean());
    if ($extra !== '') {
        $data['server_output'] = substr($extra, 0, 4096);
    }
    http_response_code($httpCode);
    echo json_encode($data);
    exit();
}

if (!isLoggedIn() || !isDocente()) {
    respondJson(['error' => 'Acceso denegado'], 403);
}

$seccion_id = $_POST['seccion_id'] ?? null;
$materia_id = $_POST['materia_id'] ?? null;

if (!$seccion_id || !$materia_id) {
    respondJson(['error' => 'Parámetros faltantes'], 400);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    respondJson(['error' => 'No se recibió el archivo o hubo un error en la subida'], 400);
}

$maxBytes = 10 * 1024 * 1024;
if ($_FILES['file']['size'] > $maxBytes) {
    respondJson(['error' => 'Archivo demasiado grande (máx 10MB)'], 413);
}

global $db;
$estudiantes_seccion = [];
$query = "SELECT u.id, u.idusuario, u.nombre 
          FROM estudiante_seccion es
          INNER JOIN users u ON es.id_usuario = u.id
          WHERE es.id_seccion = " . intval($seccion_id) . "
          AND u.estudiante = 1
          ORDER BY u.nombre ASC";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cleanCedula = strtoupper(trim(preg_replace('/[^0-9A-Z]/i', '', $row['idusuario'])));
        $estudiantes_seccion[$cleanCedula] = $row;
        $estudiantes_seccion[strtoupper(trim($row['idusuario']))] = $row;
    }
}

$tmp = $_FILES['file']['tmp_name'];
$fileName = $_FILES['file']['name'] ?? '';
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Función para extraer texto de PDF
function extraerTextoDePDF($filePath) {
    $content = @file_get_contents($filePath);
    if (!$content) return '';
    
    $text = '';
    if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $content, $matches)) {
        foreach ($matches[1] as $stream) {
            $uncompressed = @gzuncompress($stream);
            if ($uncompressed === false) {
                $uncompressed = $stream;
            }
            
            // Extraer comandos Tj
            if (preg_match_all('/\((.*?)\)\s*Tj/s', $uncompressed, $tjMatches)) {
                foreach ($tjMatches[1] as $t) {
                    $text .= $t . ' ';
                }
                $text .= "\n";
            }
            // Extraer comandos TJ
            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $uncompressed, $tjArrayMatches)) {
                foreach ($tjArrayMatches[1] as $arr) {
                    if (preg_match_all('/\((.*?)\)/s', $arr, $subMatches)) {
                        foreach ($subMatches[1] as $sub) {
                            $text .= $sub;
                        }
                    }
                    $text .= ' ';
                }
                $text .= "\n";
            }
        }
    }
    
    // Si no se pudo descomprimir stream, buscar cadenas de texto ASCII legibles
    if (trim($text) === '') {
        if (preg_match_all('/[\x20-\x7E\t\r\n]{4,}/', $content, $textBlocks)) {
            $text = implode("\n", $textBlocks[0]);
        }
    }
    
    return $text;
}

$rows = [];
$validCount = 0;
$invalidCount = 0;

// === PROCESAR ARCHIVO PDF ===
if ($fileExt === 'pdf' || (isset($_FILES['file']['type']) && $_FILES['file']['type'] === 'application/pdf')) {
    $pdfText = extraerTextoDePDF($tmp);
    if (empty(trim($pdfText))) {
        respondJson(['error' => 'No se pudo extraer texto del archivo PDF. Asegúrese de que sea un PDF digital de notas con texto seleccionable.'], 400);
    }
    
    $lineNum = 0;
    $vistos = [];
    foreach ($estudiantes_seccion as $cedulaKey => $estudiante) {
        if (isset($vistos[$estudiante['id']])) continue;
        $vistos[$estudiante['id']] = true;
        
        $lineNum++;
        $cedulaOriginal = $estudiante['idusuario'];
        $cleanCed = strtoupper(trim(preg_replace('/[^0-9A-Z]/i', '', $cedulaOriginal)));
        $nombreEst = $estudiante['nombre'];
        
        $rowObj = [
            'line' => $lineNum,
            'identificador' => $cedulaOriginal,
            'nombre' => $nombreEst,
            'valido' => false,
            'mensaje' => '',
            'estudiante_id' => $estudiante['id'],
            'notas' => [],
            'notas_texto' => ''
        ];
        
        // Buscar posición de la cédula en el texto completo
        $pos = false;
        $cedulasPosibles = array_unique([$cedulaOriginal, $cleanCed, str_replace(['V-', 'E-', 'V', 'E'], '', $cleanCed)]);
        foreach ($cedulasPosibles as $cTest) {
            if (empty($cTest)) continue;
            $pos = stripos($pdfText, $cTest);
            if ($pos !== false) break;
        }
        
        if ($pos !== false) {
            // Extraer un fragmento de texto posterior a la cédula (hasta 200 caracteres)
            $fragmento = substr($pdfText, $pos, 200);
            
            // Buscar todas las secuencias numéricas (1 a 20) en el fragmento
            preg_match_all('/\b(20|1[0-9]|[1-9])(?:\.0+|\,0+)?\b/', $fragmento, $numMatches);
            $numerosEncontrados = [];
            if (!empty($numMatches[1])) {
                foreach ($numMatches[1] as $n) {
                    $numVal = floatval($n);
                    if ($numVal >= 1 && $numVal <= 20) {
                        $numerosEncontrados[] = $numVal;
                    }
                }
            }
            
            $notas_trimestres = [];
            $notas_texto = [];
            
            if (isset($numerosEncontrados[0])) {
                $val = $numerosEncontrados[0];
                $notas_trimestres['trimestre_1'] = $val;
                $notas_texto[] = "T1:$val";
            }
            if (isset($numerosEncontrados[1])) {
                $val = $numerosEncontrados[1];
                $notas_trimestres['trimestre_2'] = $val;
                $notas_texto[] = "T2:$val";
            }
            if (isset($numerosEncontrados[2])) {
                $val = $numerosEncontrados[2];
                $notas_trimestres['trimestre_3'] = $val;
                $notas_texto[] = "T3:$val";
            }
            
            if (!empty($notas_trimestres)) {
                $rowObj['valido'] = true;
                $rowObj['notas'] = $notas_trimestres;
                $rowObj['notas_texto'] = implode(' | ', $notas_texto);
                $rowObj['mensaje'] = 'OK';
                $validCount++;
            } else {
                $rowObj['mensaje'] = 'Estudiante localizado en PDF, pero sin notas registradas';
                $invalidCount++;
            }
        } else {
            $rowObj['mensaje'] = 'No se encontró la cédula en el documento PDF';
            $invalidCount++;
        }
        
        $rows[] = $rowObj;
    }

} else {
    // === PROCESAR ARCHIVO CSV ===
    $handle = fopen($tmp, 'r');
    if (!$handle) {
        respondJson(['error' => 'No se pudo procesar el archivo'], 500);
    }

    $line = 0;
    $isFirstLine = true;

    while (($data = fgetcsv($handle, 10000, ',')) !== false) {
        $line++;
        
        foreach ($data as $k => $v) {
            $data[$k] = trim(preg_replace('/^\xEF\xBB\xBF/', '', $v));
        }
        
        $isEmpty = true;
        foreach ($data as $cell) {
            if ($cell !== '') { $isEmpty = false; break; }
        }
        if ($isEmpty) continue;
        
        if ($isFirstLine) {
            $isFirstLine = false;
            continue;
        }
        
        $cedula = isset($data[0]) ? strtoupper(trim($data[0])) : '';
        $cleanCed = strtoupper(trim(preg_replace('/[^0-9A-Z]/i', '', $cedula)));
        $estudiante = $estudiantes_seccion[$cleanCed] ?? ($estudiantes_seccion[$cedula] ?? null);
        
        $nota1 = isset($data[5]) ? trim($data[5]) : '';
        $nota2 = isset($data[6]) ? trim($data[6]) : '';
        $nota3 = isset($data[7]) ? trim($data[7]) : '';
        
        $rowObj = [
            'line' => $line,
            'identificador' => $cedula,
            'nombre' => $estudiante['nombre'] ?? '',
            'valido' => false,
            'mensaje' => '',
            'estudiante_id' => $estudiante['id'] ?? null,
            'notas' => [],
            'notas_texto' => ''
        ];
        
        if (!$estudiante) {
            $rowObj['mensaje'] = 'Estudiante no encontrado en la sección';
            $invalidCount++;
            $rows[] = $rowObj;
            continue;
        }
        
        $notas_trimestres = [];
        $notas_texto = [];
        
        if ($nota1 !== '') {
            $val = floatval(str_replace(',', '.', $nota1));
            if ($val >= 1 && $val <= 20) {
                $notas_trimestres['trimestre_1'] = $val;
                $notas_texto[] = "T1:$val";
            } else {
                $rowObj['mensaje'] .= 'T1 inválido (' . $nota1 . '); ';
            }
        }
        
        if ($nota2 !== '') {
            $val = floatval(str_replace(',', '.', $nota2));
            if ($val >= 1 && $val <= 20) {
                $notas_trimestres['trimestre_2'] = $val;
                $notas_texto[] = "T2:$val";
            } else {
                $rowObj['mensaje'] .= 'T2 inválido (' . $nota2 . '); ';
            }
        }
        
        if ($nota3 !== '') {
            $val = floatval(str_replace(',', '.', $nota3));
            if ($val >= 1 && $val <= 20) {
                $notas_trimestres['trimestre_3'] = $val;
                $notas_texto[] = "T3:$val";
            } else {
                $rowObj['mensaje'] .= 'T3 inválido (' . $nota3 . '); ';
            }
        }
        
        if (empty($notas_trimestres)) {
            $rowObj['mensaje'] = $rowObj['mensaje'] ?: 'No se encontraron notas válidas';
            $invalidCount++;
        } else {
            $rowObj['valido'] = true;
            $rowObj['notas'] = $notas_trimestres;
            $rowObj['notas_texto'] = implode(' | ', $notas_texto);
            $rowObj['mensaje'] = 'OK';
            $validCount++;
        }
        
        $rows[] = $rowObj;
    }

    fclose($handle);
}

respondJson([
    'previewRows' => $rows,
    'summary' => [
        'total' => count($rows),
        'validas' => $validCount,
        'invalidas' => $invalidCount,
        'formato' => ($fileExt === 'pdf') ? 'PDF' : 'CSV'
    ]
]);
