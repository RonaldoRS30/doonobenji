<?php
require_once 'model/informes/ventas/inf_pedidoretraso.model.php';
require_once 'model/informes/ventas/informes.entidad.php';
class InformeController{
    
    private $model;
    
    public function __CONSTRUCT(){
        $this->model  = new InformeModel();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/informes/ventas/inf_pedidoretraso.php';
        require_once 'view/footer.php';
    }

    public function Datos()
    {
        $this->model->Datos($_POST);
    }

    public function Detalle()
    {
        print_r(json_encode($this->model->Detalle($_POST)));
    }

    public function ExportExcel(){
        $alm = new Informes();
        $alm->__SET('start', date('Y-m-d',strtotime($_REQUEST['start'])));
        $alm->__SET('end', date('Y-m-d',strtotime($_REQUEST['end'])));
        $alm->__SET('cod_cajas', $_REQUEST['cod_cajas']);
        $alm->__SET('tipo_doc', $_REQUEST['tipo_doc']);
        $alm->__SET('tped', $_REQUEST['tipo_ped']);
        $alm->__SET('cliente', $_REQUEST['cliente']);
        $_SESSION["min-1"] = $_REQUEST['start'];
        $_SESSION["max-1"] = $_REQUEST['end'];
        $data = $this->model->ExportExcel($alm);
        require_once 'view/informes/ventas/exportar/inf_pedidoretraso_xls.php';
    }

    public function Imprimir(){
        $data = $this->model->ObtenerDatosImp($_REQUEST['Cod']);
        require_once 'view/inicio/imprimir/comp.php';
    }

    public function ImprimirAll(){
        $data = $this->model->ObtenerDatosImpAll($_REQUEST['Cod']);
        require_once 'view/inicio/imprimir/comp.php';
    }

public function PedidosRetraso()
{
    try {
        $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
        $end = date('Y-m-d H:i:s', strtotime($_POST['end']));
        $estado = $_POST['estado'];

        $pedidos = $this->model->ConsultarPedidosRetraso($start, $end, $estado);

        // Obtener todos los id_detalle_pedido de forma segura
        $detalle_ids = [];
        foreach($pedidos as $p){
            $detalle_ids[] = $p->id_detalle_pedido;
        }

        // Obtener clientes correspondientes
        $clientes = $this->model->ObtenerClientesPorDetalles($detalle_ids);

        $clientesMap = [];
        foreach($clientes as $c){
            $clientesMap[$c->id_detalle_pedido] = $c->nombre_cliente;
        }

        // Asignar nombre_cliente a cada pedido
        foreach($pedidos as $p){
            $p->nombre_cliente = $clientesMap[$p->id_detalle_pedido] ?? 'N/A';
        }

        echo json_encode(['data' => $pedidos]);

    } catch(Exception $e){
        echo json_encode(['data' => []]);
    }
}


public function PedidosConClientes()
{
    try {
        // Recibir parámetros del AJAX
        $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
        $end = date('Y-m-d H:i:s', strtotime($_POST['end']));
        $estado = $_POST['estado']; // "%", 1, 2, 3

        // Llamar al modelo para traer los pedidos filtrados por fecha y estado
        $pedidos = $this->model->ConsultarPedidosRetraso($start, $end, $estado);

        // Crear un array con todos los id_detalle_pedido
$detalle_ids = array_map(function($p) {
    return $p->id_detalle_pedido;
}, $pedidos);
        // Obtener los clientes correspondientes
        $clientes = $this->model->ObtenerClientesPorDetalles($detalle_ids);

        // Mapear id_detalle_pedido => nombre_cliente
        $clientesMap = [];
        foreach($clientes as $c){
            $clientesMap[$c->id_detalle_pedido] = $c->nombre_cliente;
        }

        // Agregar nombre_cliente a cada pedido
        foreach($pedidos as $p){
            $p->nombre_cliente = $clientesMap[$p->id_detalle_pedido] ?? 'N/A';
        }

        echo json_encode(['data' => $pedidos]);

    } catch (Exception $e) {
        echo json_encode(['data' => []]);
    }
}




}