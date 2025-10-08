<?php
// rpa/rpa_cancelaciones.php
// Este script genera el PDF de cancelaciones y lo envía al gerente automáticamente

date_default_timezone_set('America/Lima');

require_once __DIR__ . '/../model/tablero/tm_tablero.model.php';
require 'vendor/autoload.php'; // Para PHPMailer (si usas composer)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // 1️⃣ Obtener datos desde el modelo
    $model = new Tm_tableroModel(); // Asegúrate de que la clase se llame así
    $motivos = $model->ListarMotivosCancelacion();

    // 2️⃣ Generar el PDF con los datos obtenidos
    $nombreArchivo = 'reporte_cancelaciones_' . date('Y-m-d') . '.pdf';
    $rutaArchivo = __DIR__ . '/../reportes/' . $nombreArchivo;

    // Crear carpeta si no existe
    if (!file_exists(dirname($rutaArchivo))) {
        mkdir(dirname($rutaArchivo), 0777, true);
    }

    // Usaremos TCPDF (puedes cambiarlo si ya usas otro)
    require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Reporte de Motivos de Cancelación - ' . date('d/m/Y'), 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezados de tabla
    $tbl = '<table border="1" cellpadding="4">
              <tr>
                <th width="15%"># Pedido</th>
                <th width="45%">Motivo</th>
                <th width="20%">Usuario</th>
                <th width="20%">Fecha</th>
              </tr>';
    foreach ($motivos as $m) {
        $tbl .= '<tr>
                   <td>'.$m['cod_pedido'].'</td>
                   <td>'.$m['motivo'].'</td>
                   <td>'.$m['usuario'].'</td>
                   <td>'.$m['fecha_reg'].'</td>
                 </tr>';
    }
    $tbl .= '</table>';

    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output($rutaArchivo, 'F');

    // 3️⃣ Enviar correo con PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tu_correo@gmail.com';
    $mail->Password = 'rwjtpjnikrzcxjfb'; // tu contraseña de aplicación
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('tu_correo@gmail.com', 'Sistema de Ventas');
    $mail->addAddress('correo_del_gerente@empresa.com', 'Gerente General');
    $mail->Subject = 'Reporte Semanal de Motivos de Cancelación';
    $mail->Body = 'Adjunto el reporte PDF con las cancelaciones registradas durante la semana.';
    $mail->addAttachment($rutaArchivo);

    $mail->send();
    echo "✅ Reporte enviado correctamente a las " . date('H:i:s');

} catch (Exception $e) {
    echo "❌ Error en el envío: " . $e->getMessage();
}
?>
