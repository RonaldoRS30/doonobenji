<?php
// rpa/rpa_cancelaciones.php
// Genera el PDF de cancelaciones y lo envía automáticamente por correo

date_default_timezone_set('America/Lima');

// ✅ 1. Incluir modelo y dependencias
require_once __DIR__ . '/../model/tablero/tm_tablero.model.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer + TCPDF si usas composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // ✅ 2. Obtener datos desde el modelo
    $model = new TableroModel();
    $motivos = $model->ListarMotivosCancelacion();

    if (empty($motivos)) {
        throw new Exception('No hay cancelaciones registradas para generar el reporte.');
    }

    // ✅ 3. Generar el PDF
    $nombreArchivo = 'reporte_cancelaciones_' . date('Y-m-d') . '.pdf';
    $rutaArchivo = __DIR__ . '/../reportes/' . $nombreArchivo;

    // Crear carpeta si no existe
    if (!file_exists(dirname($rutaArchivo))) {
        mkdir(dirname($rutaArchivo), 0777, true);
    }

    // Verificar si TCPDF está disponible
    $tcpdfPath = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
    if (!file_exists($tcpdfPath)) {
        throw new Exception('No se encontró TCPDF en: ' . $tcpdfPath);
    }
    require_once $tcpdfPath;

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Reporte de Motivos de Cancelación - ' . date('d/m/Y'), 0, 1, 'C');
    $pdf->Ln(5);

        // 🔁 Mapeo de los valores del select a su texto visible
    $motivos_texto = [
        'pedido_frio' => 'Pedido frío',
        'demora_entrega' => 'Demora en entrega',
        'error_producto' => 'Producto equivocado',
        'falta_stock' => 'Falta de stock',
        'pago_rechazado' => 'Pago rechazado',
        'insatisfaccion_cliente' => 'Insatisfacción del cliente',
        'cancelacion_cliente' => 'Cancelación por el cliente',
        'otro' => 'Otro'
    ];

    $tbl = '<table border="1" cellpadding="4">
            <tr style="background-color:#f2f2f2; font-weight:bold;">
                <th width="15%"># Pedido</th>
                <th width="45%">Motivo</th>
                <th width="20%">Usuario</th>
                <th width="20%">Fecha</th>
            </tr>';

foreach ($motivos as $m) {
    // Si hay motivo_otro, usamos ese texto
    if (!empty($m['motivo_otro'])) {
        $motivoTexto = $m['motivo_otro'];
    } else {
        // Si no, buscamos el texto en el mapeo
        $motivoTexto = isset($motivos_texto[$m['motivo']]) 
            ? $motivos_texto[$m['motivo']] 
            : $m['motivo']; // si no existe en el mapeo, muestra tal cual
    }

    $tbl .= '<tr>
            <td>' . htmlspecialchars($m['cod_pedido']) . '</td>
            <td>' . htmlspecialchars($motivoTexto) . '</td>
            <td>' . htmlspecialchars($m['usuario']) . '</td>
            <td>' . htmlspecialchars($m['fecha_reg']) . '</td>
            </tr>';
}
    $tbl .= '</table>';

    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output($rutaArchivo, 'F'); // Guarda el PDF en el servidor

    // ✅ 4. Enviar correo con PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jhordanroly22@gmail.com';
    $mail->Password = 'rwjt pjni zkrc xjfb'; // tu contraseña de aplicación
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Remitente y destinatario
    $mail->setFrom('jhordanroly22@gmail.com', 'Sistema de Ventas');
    $mail->addAddress('jhordanroly22@gmail.com', 'Jhordan Roly');

    // Contenido
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '📊 Reporte Semanal de Cancelaciones';
    $mail->Body = "Hola Jhordan,\n\nAdjunto el reporte PDF con las cancelaciones registradas durante la semana.\n\nSaludos,\nTu RPA 🤖";
    $mail->addAttachment($rutaArchivo);
    // Envío
    $mail->send();

    echo "✅ Reporte generado y enviado correctamente a las " . date('H:i:s');

} catch (Exception $e) {
    echo "❌ Error en el proceso: " . $e->getMessage();
}
