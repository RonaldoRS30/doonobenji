<?php
require_once(__DIR__ . '/../../../vendor/tecnickcom/tcpdf/tcpdf.php');
include_once(__DIR__ . '/../../../model/rest.model.php');

// Crear conexión PDO
$conn = Database::Conectar();

// Recibir parámetros (fechas y tipo de pedido)
$start = $_GET['start'] ?? date('Y-m-d');
$end   = $_GET['end'] ?? date('Y-m-d');
$estado = $_GET['estado'] ?? '%';

// Obtener pedidos
require_once(__DIR__ . '/../../../controller/informes/ventas/inf_pedidoretraso.model.php'); // tu modelo correcto
$pedidos = (new AreaProdModel())->ConsultarPedidosRetraso($start, $end, $estado);

$detalle_ids = array_map(function($p){ return $p->id_detalle_pedido; }, $pedidos);
$clientes = (new AreaProdModel())->ObtenerClientesPorDetalles($detalle_ids);

// Convertir clientes a un array asociativo para fácil lookup
$clientes_map = [];
foreach($clientes as $c){
    $clientes_map[$c->id_detalle_pedido] = $c->nombre_cliente;
}

// Crear PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Sistema');
$pdf->SetTitle('Reporte de Pedidos con Retraso');
$pdf->SetHeaderData('', 0, 'Reporte de Pedidos con Retraso', '');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(10, 20, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Tabla
$html = '<table border="1" cellpadding="4">
<tr style="background-color:#16a085; color:white;">
<th>Cod Pedido Retraso</th>
<th>ID Pedido</th>
<th>Nombre Cliente</th>
<th>Producto</th>
<th>Tiempo Standar(min:seg)</th>
<th>Tiempo Demorado(min:seg)</th>
<th>Fecha Pedido</th>
<th>Fecha Envío</th>
<th>Tipo de Pedido</th>
</tr>';

$totalSegundosDemora = 0;
$totalPedidos = 0;

foreach($pedidos as $p){
    $fechaPedido = strtotime($p->fecha_pedido);
    $fechaEnvio  = strtotime($p->fecha_envio);
    $diffSegundos= $fechaEnvio - $fechaPedido;
    $diffMinutos = floor($diffSegundos/60);
    $diffSeg     = $diffSegundos % 60;
    $tiempoDemora = str_pad($diffMinutos,2,'0',STR_PAD_LEFT).':'.str_pad($diffSeg,2,'0',STR_PAD_LEFT);

    $totalSegundosDemora += $diffSegundos;
    $totalPedidos++;

    // Estado
    switch($p->estado){
        case '1': $estadoTexto='PARA MESA'; break;
        case '2': $estadoTexto='PARA LLEVAR'; break;
        case '3': $estadoTexto='DELIVERY'; break;
        default: $estadoTexto = $p->estado;
    }

    $nombreCliente = $clientes_map[$p->id_detalle_pedido] ?? 'N/A';

    $html .= '<tr>
        <td>'.$p->cod_pedido_retraso.'</td>
        <td>'.$p->id_pedido.'</td>
        <td>'.$nombreCliente.'</td>
        <td>'.$p->nombre_producto.'</td>
        <td>'.$p->tiempostandar.'</td>
        <td>'.$tiempoDemora.'</td>
        <td>'.$p->fecha_pedido.'</td>
        <td>'.$p->fecha_envio.'</td>
        <td>'.$estadoTexto.'</td>
    </tr>';
}

$html .= '</table>';

// Totales
$promedio = ($totalPedidos>0)? floor($totalSegundosDemora/$totalPedidos) : 0;
$promMin = floor($promedio/60);
$promSeg = $promedio % 60;
$promFormateado = str_pad($promMin,2,'0',STR_PAD_LEFT).':'.str_pad($promSeg,2,'0',STR_PAD_LEFT);

$htmlHeader = '<h3>Total de pedidos: '.$totalPedidos.'</h3>';
$htmlHeader .= '<h3>Promedio de demora: '.$promFormateado.'</h3>';

$pdf->writeHTML($htmlHeader, true, false, true, false, '');
$pdf->writeHTML($html, true, false, true, false, '');

// Salida
$pdf->Output('Reporte_Pedidos_Retrasos.pdf', 'I');