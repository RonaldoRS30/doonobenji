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
    $motivos = $this->model->ListarMotivosCancelacion();

    // Contar frecuencia de cada motivo
    $counts = [];
    foreach ($motivos as $m) {
        if (!isset($counts[$m['motivo']])) {
            $counts[$m['motivo']] = 0;
        }
        $counts[$m['motivo']]++;
    }

    // Encontrar motivo más frecuente
    $mostFrequent = null;
    $maxCount = 0;
    foreach ($counts as $motivo => $count) {
        if ($count > $maxCount) {
            $maxCount = $count;
            $mostFrequent = $motivo;
        }
    }

    // Devolver JSON para DataTable con datos y resumen
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