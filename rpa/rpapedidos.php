<?php
// view/informes/ventas/inf_pedidoretraso_pdf.php
// Genera el PDF de pedidos con retraso y lo envía automáticamente por correo

require_once __DIR__ . '/../model/informes/ventas/inf_pedidoretraso.model.php';

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Lima');

try {
    // ✅ 1. Incluir modelo
    $model = new InformeModel();

    // ✅ 2. Parámetros GET (rango de fechas y estado)
    $start = $_GET['start'] ?? date('Y-m-d');
    $end = $_GET['end'] ?? date('Y-m-d');
    $estado = $_GET['estado'] ?? '%';
    $start = date('Y-m-01 00:00:00');  // primer día del mes
    $end   = date('Y-m-t 23:59:59');   // último día del mes

    // ✅ 3. Consultar pedidos
    $pedidos = $model->ConsultarPedidosRetraso($start, $end, $estado);
    if (empty($pedidos)) {
        throw new Exception('No hay pedidos con retraso en el rango seleccionado.');
    }

    // ✅ 4. Obtener nombres de clientes
    $detalle_ids = array_map(function($p) {
    return $p->id_detalle_pedido;
    }, $pedidos);
    $clientes = $model->ObtenerClientesPorDetalles($detalle_ids);

    $clientes_map = [];
    foreach ($clientes as $c) {
        $clientes_map[$c->id_detalle_pedido] = $c->nombre_cliente;
    }

    // ✅ 5. Crear carpeta de reportes
    $nombreArchivo = 'reporte_pedidos_retraso_' . date('Y-m-d') . '.pdf';
    $rutaArchivo = __DIR__ . '/../../../reportes/' . $nombreArchivo;
    if (!file_exists(dirname($rutaArchivo))) {
        mkdir(dirname($rutaArchivo), 0777, true);
    }

    // ✅ 6. Generar PDF con TCPDF
    $tcpdfPath = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
    if (!file_exists($tcpdfPath)) {
        throw new Exception('No se encontró TCPDF en: ' . $tcpdfPath);
    }
    require_once $tcpdfPath;

    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema DoonoBenji');
    $pdf->SetTitle('Reporte de Pedidos con Retraso');
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);

    // ✅ 7. Construir tabla HTML
    $tbl = '<table border="1" cellpadding="4">
        <tr style="background-color:#16a085; color:white; font-weight:bold;">
            <th>Cod Pedido Retraso</th>
            <th>ID Pedido</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Tiempo Estándar (min/seg)</th>
            <th>Tiempo Demorado (min/seg)</th>
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
        $tiempoDemora = sprintf('%02d:%02d', $min, $seg);

        $ts = (int)$p->tiempostandar;
        $tiempoEstandar = sprintf('%02d:00', $ts);

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
            <td>' . $tiempoEstandar . '</td>
            <td>' . $tiempoDemora . '</td>
            <td>' . $p->fecha_pedido . '</td>
            <td>' . $p->fecha_envio . '</td>
            <td>' . $estadoTexto . '</td>
        </tr>';
    }

    $tbl .= '</table>';

    // ✅ 8. Calcular promedio
    $promedio = ($totalPedidos > 0) ? floor($totalSegundos / $totalPedidos) : 0;
    $promMin = floor($promedio / 60);
    $promSeg = $promedio % 60;
    $promedioFormateado = sprintf('%02d:%02d', $promMin, $promSeg);

    $resumen = '
        <h3><strong>Total de pedidos:</strong> ' . $totalPedidos . '</h3>
        <h3><strong>Promedio de demora:</strong> ' . $promedioFormateado . '</h3>
        <br><br>
    ';

    $pdf->writeHTML($resumen, true, false, false, false, '');
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output($rutaArchivo, 'F'); // Guarda el PDF en servidor

    // ✅ 9. Enviar correo con PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jhordanroly22@gmail.com'; // tu correo Gmail
    $mail->Password = 'rwjt pjni zkrc xjfb'; // contraseña de aplicación
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Datos del correo
    $mail->setFrom('jhordanroly22@gmail.com', 'Sistema DoonoBenji');
    $mail->addAddress('jhordanroly22@gmail.com', 'Jhordan Roly');
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '📦 Reporte de Pedidos con Retraso - ' . date('d/m/Y');
    $mail->Body = "Hola Jhordan,\n\nAdjunto el reporte PDF con los pedidos que tuvieron retraso.\n\nSaludos,\nTu RPA 🤖";
    $mail->addAttachment($rutaArchivo);

    $mail->send();
    echo "✅ Reporte generado y enviado correctamente a las " . date('H:i:s');

} catch (Exception $e) {
    echo "❌ Error al generar/enviar el reporte: " . $e->getMessage();
}
