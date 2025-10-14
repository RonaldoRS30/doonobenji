<?php
// view/informes/ventas/inf_pedidoretraso_pdf.php
// Genera un PDF con los pedidos con retraso filtrados
require_once __DIR__ . '/../../../model/informes/ventas/inf_pedidoretraso.model.php';

date_default_timezone_set('America/Lima');

try {
    // ✅ 1. Incluir modelo y dependencias

    // Ruta TCPDF (ajusta si está en otra ubicación)
    $tcpdfPath = __DIR__ . '/../../../vendor/tecnickcom/tcpdf/tcpdf.php';
    if (!file_exists($tcpdfPath)) {
        throw new Exception('No se encontró TCPDF en: ' . $tcpdfPath);
    }
    require_once $tcpdfPath;

    // ✅ 2. Crear instancia del modelo
    $model = new InformeModel(); // nombre de tu clase del modelo

    // ✅ 3. Obtener parámetros GET (o usar fecha actual por defecto)
$start = $_GET['start'] ?? date('Y-m-d');
$end = $_GET['end'] ?? date('Y-m-d');
$estado = $_GET['estado'] ?? '%';

// Ajustar formato de tiempo para incluir todo el rango del día
$start = date('Y-m-d 00:00:00', strtotime($start));
$end = date('Y-m-d 23:59:59', strtotime($end));

    // ✅ 4. Consultar los pedidos
    $pedidos = $model->ConsultarPedidosRetraso($start, $end, $estado);

    if (empty($pedidos)) {
        throw new Exception('No hay pedidos con retraso en el rango seleccionado.');
    }

    // ✅ 5. Obtener los nombres de clientes
$detalle_ids = array_map(function($p) {
    return $p->id_detalle_pedido;
}, $pedidos);    $clientes = $model->ObtenerClientesPorDetalles($detalle_ids);

    $clientes_map = [];
    foreach ($clientes as $c) {
        $clientes_map[$c->id_detalle_pedido] = $c->nombre_cliente;
    }

    // ✅ 6. Crear carpeta para reportes
    $nombreArchivo = 'reporte_pedidos_retraso_' . date('Y-m-d') . '.pdf';
    $rutaArchivo = __DIR__ . '/../../../reportes/' . $nombreArchivo;

    if (!file_exists(dirname($rutaArchivo))) {
        mkdir(dirname($rutaArchivo), 0777, true);
    }

    // ✅ 7. Generar el PDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema DoonoBenji');
    $pdf->SetTitle('Reporte de Pedidos con Retraso');
    $pdf->SetHeaderData('', 0, 'Reporte de Pedidos con Retraso - ' . date('d/m/Y'), '');
    $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
    $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);

    // ✅ 8. Construir la tabla HTML
    $tbl = '<table border="1" cellpadding="4">
        <tr style="background-color:#16a085; color:white; font-weight:bold;">
            <th>Cod Pedido Retraso</th>
            <th>ID Pedido</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Tiempo Estándar(min/seg)</th>
            <th>Tiempo Demorado(min/seg)</th>
            <th>Fecha Pedido</th>
            <th>Fecha Envío</th>
            <th>Tipo Pedido</th>
        </tr>';

    $totalSegundos = 0;
    $totalPedidos = 0;

    foreach ($pedidos as $p) {
        $fechaPedido = strtotime($p->fecha_pedido);
        $fechaEnvio = strtotime($p->fecha_envio);
        $diffSeg = max(0, $fechaEnvio - $fechaPedido);

        $min = floor($diffSeg / 60);
        $seg = $diffSeg % 60;
        $tiempoDemora = str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seg, 2, '0', STR_PAD_LEFT);

 // convertir tiempostandar también a mm:ss (ej. 7 -> 07:00)
$ts = (int)$p->tiempostandar;
$minStd = str_pad($ts, 2, '0', STR_PAD_LEFT);
$tiempoEstandar = $minStd . ':00';

        
        $totalSegundos += $diffSeg;
        $totalPedidos++;

        switch ($p->estado) {
            case '1': $estadoTexto = 'PARA MESA'; break;
            case '2': $estadoTexto = 'PARA LLEVAR'; break;
            case '3': $estadoTexto = 'DELIVERY'; break;
            default: $estadoTexto = $p->estado;
        }

        $nombreCliente = $clientes_map[$p->id_detalle_pedido] ?? 'N/A';

        $tbl .= '<tr>
            <td>' . $p->cod_pedido_retraso . '</td>
            <td>' . $p->id_pedido . '</td>
            <td>' . htmlspecialchars($nombreCliente) . '</td>
            <td>' . htmlspecialchars($p->nombre_producto) . '</td>
            <td>' .  $tiempoEstandar. '</td>
            <td>' . $tiempoDemora . '</td>
            <td>' . $p->fecha_pedido . '</td>
            <td>' . $p->fecha_envio . '</td>
            <td>' . $estadoTexto . '</td>
        </tr>';
    }

    $tbl .= '</table>';

    // ✅ 9. Calcular promedio
    $promedio = ($totalPedidos > 0) ? floor($totalSegundos / $totalPedidos) : 0;
    $promMin = floor($promedio / 60);
    $promSeg = $promedio % 60;
    $promedioFormateado = str_pad($promMin, 2, '0', STR_PAD_LEFT) . ':' . str_pad($promSeg, 2, '0', STR_PAD_LEFT);

    // ✅ 10. Encabezado del resumen
    $resumen = '
        <h3><strong>Total de pedidos:</strong> ' . $totalPedidos . '</h3>
        <h3><strong>Promedio de demora:</strong> ' . $promedioFormateado . '</h3>
        <br><br>
    ';

    $pdf->writeHTML($resumen, true, false, false, false, '');
    $pdf->writeHTML($tbl, true, false, false, false, '');

    // // ✅ 11. Guardar el archivo (puedes usar 'I' para ver directamente)
    // $pdf->Output($rutaArchivo, 'F');

    // echo "✅ Reporte generado correctamente: " . basename($rutaArchivo);

    // Descargar automáticamente el PDF con nombre personalizado
$nombreArchivo = 'reporte_pedidos_retraso_' . date('Y-m-d_His') . '.pdf';
// Generar PDF temporal en memoria
$pdf->Output($nombreArchivo, 'I'); // 'I' = lo envía al navegador directamente
exit; // 🔒 Importante: evita que se imprima texto adicional


} catch (Exception $e) {
    echo "❌ Error al generar el reporte: " . $e->getMessage();
}
