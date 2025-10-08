<?php
require_once 'model/tablero/tm_tablero.model.php';

class TableroController{
    
    private $model;

    public function __CONSTRUCT(){
        $this->model = new TableroModel();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/tablero/tm_tablero.php';
        require_once 'view/footer.php';
    }

    public function DatosGrls()
    {
        $this->model->DatosGrls($_POST);
    }

    public function DatosGraf()
    {
        $this->model->DatosGraf($_POST);
    }

public function ListarMotivosCancelacion()
{
    $fecha_desde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
    $fecha_hasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;

    $motivos = $this->model->ListarMotivosCancelacion($fecha_desde, $fecha_hasta);

    $motivoTextos = [
        'pedido_frio' => 'Pedido frío',
        'demora_entrega' => 'Demora en entrega',
        'error_producto' => 'Producto equivocado',
        'falta_stock' => 'Falta de stock',
        'pago_rechazado' => 'Pago rechazado',
        'insatisfaccion_cliente' => 'Insatisfacción del cliente',
        'cancelacion_cliente' => 'Cancelación por el cliente',
        'otro' => 'Otro'
    ];

    $counts = [];
    foreach ($motivos as &$m) {
        $m['motivo'] = $motivoTextos[$m['motivo']] ?? ucfirst(str_replace('_', ' ', $m['motivo']));
        $counts[$m['motivo']] = ($counts[$m['motivo']] ?? 0) + 1;
    }

    // ✅ Verificamos si $counts tiene datos antes de usar max()
    $mostFrequent = null;
    if (!empty($counts)) {
        $maxCount = max($counts);
        $mostFrequent = array_keys($counts, $maxCount)[0];
    }

    echo json_encode([
        'data' => $motivos,
        'summary' => [
            'mostFrequent' => $mostFrequent,
            'counts' => $counts
        ]
    ]);
}





}
?> 