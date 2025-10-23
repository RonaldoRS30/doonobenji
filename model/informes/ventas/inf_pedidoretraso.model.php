<?php
include_once(__DIR__ . "/../../rest.model.php");

class InformeModel
{
    private $conexionn;

    public function __CONSTRUCT()
    {
        try
        {
            $this->conexionn = Database::Conectar();
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Datos()
    {
        try
        {
            $ifecha = date('Y-m-d H:i:s',strtotime($_POST['ifecha']));
            $ffecha = date('Y-m-d H:i:s',strtotime($_POST['ffecha']));
            $stm = $this->conexionn->prepare("SELECT v.id_ven,v.id_ped,v.id_tpag,v.pago_efe,v.pago_tar,v.descu,v.total AS stotal,v.fec_ven,v.desc_td,CONCAT(v.ser_doc,'-',v.nro_doc) AS numero,IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total,v.id_cli,v.igv,v.id_usu,c.desc_caja FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (v.fec_ven >= ? AND v.fec_ven <= ?) AND v.id_tped like ? AND v.id_tdoc like ? AND c.id_caja like ? AND v.id_cli like ? GROUP BY v.id_ven");
            $stm->execute(array($ifecha,$ffecha,$_POST['tped'],$_POST['tdoc'],$_POST['icaja'],$_POST['cliente']));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);           
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->conexionn->query("SELECT nombre FROM v_clientes WHERE id_cliente = ".$d->id_cli)
                    ->fetch(PDO::FETCH_OBJ);
            }
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json;       
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }


public function ConsultarPedidosRetraso($start, $end, $estado)
{
    try {
        $stm = $this->conexionn->prepare("
            SELECT 
                pr.cod_pedido_retraso,
                pr.id_pedido,
                pr.id_detalle_pedido,
                pr.id_pres,
                dp.fecha_pedido,
                dp.fecha_envio,
                pr.estado,
                tp.nombre AS nombre_producto,
                tpp.tiempostandar AS tiempostandar
            FROM tm_pedido_retraso pr
            INNER JOIN tm_detalle_pedido dp 
                ON pr.id_detalle_pedido = dp.id_detalle_pedido
            INNER JOIN tm_producto_pres tpp
                ON pr.id_pres = tpp.id_pres
            INNER JOIN tm_producto tp
                ON tpp.id_prod = tp.id_prod
            WHERE dp.fecha_pedido BETWEEN ? AND ?
            AND pr.estado LIKE ?
        ");
        $stm->execute([$start, $end, $estado]);
        return $stm->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        return [];
    }
}



public function ObtenerClientesPorDetalles($detalle_ids) {
    try {
        if(count($detalle_ids) === 0) return [];

        $placeholders = implode(',', array_fill(0, count($detalle_ids), '?'));
        $sql = "
            SELECT 
                dp.id_detalle_pedido,
                dp.id_pres,
                dp.id_pedido,
                COALESCE(dv.nomb_cliente, ml.nomb_cliente, ms.nomb_cliente) AS nombre_cliente
            FROM tm_detalle_pedido dp
            LEFT JOIN tm_pedido_delivery dv ON dp.id_pedido = dv.id_pedido
            LEFT JOIN tm_pedido_llevar ml ON dp.id_pedido = ml.id_pedido
            LEFT JOIN tm_pedido_mesa ms ON dp.id_pedido = ms.id_pedido
            WHERE dp.id_detalle_pedido IN ($placeholders)
        ";
        $stm = $this->conexionn->prepare($sql);
        $stm->execute($detalle_ids);
        return $stm->fetchAll(PDO::FETCH_OBJ);
    } catch(Exception $e){
        return [];
    }
}





    public function Detalle()
    {
        try
        {
            $cod = $_POST['cod'];
            $stm = $this->conexionn->prepare("SELECT id_prod,SUM(cantidad) AS cantidad,precio FROM tm_detalle_venta WHERE id_venta = ? GROUP BY id_prod");
            $stm->execute(array($cod));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Producto'} = $this->conexionn->query("SELECT nombre_prod,pres_prod FROM v_productos WHERE id_pres = ".$d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function TipoPedido()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_tipo_pedido");
            $stm->execute();            
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $stm->closeCursor();
            return $c;
            $this->conexionn=null;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Cajas()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_caja");
            $stm->execute();            
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $stm->closeCursor();
            return $c;
            $this->conexionn=null;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function Clientes()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT id_cliente,nombre FROM v_clientes");
            $stm->execute();            
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $stm->closeCursor();
            return $c;
            $this->conexionn=null;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ExportExcel($data)
    {
        try
        {
            $stm = $this->conexionn->prepare("SELECT v.id_ped,v.id_tpag,v.pago_efe,v.pago_tar,v.descu,v.total AS stotal,v.fec_ven,v.desc_td,v.ser_doc,v.nro_doc, IFNULL(SUM(v.pago_efe+v.pago_tar),0) AS total,v.id_cli,v.igv,v.id_usu,c.desc_caja FROM v_ventas_con AS v INNER JOIN v_caja_aper AS c ON v.id_apc = c.id_apc WHERE (DATE(v.fec_ven) >= ? AND DATE(v.fec_ven) <= ?) AND v.id_tped like ? AND v.id_tdoc like ? AND c.id_caja like ? AND v.id_cli like ? GROUP BY v.id_ven");
            $stm->execute(array(
                $data->__GET('start'),
                $data->__GET('end'),
                $data->__GET('tped'),
                $data->__GET('tipo_doc'),
                $data->__GET('cod_cajas'),
                $data->__GET('cliente')
            ));
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            foreach($c as $k => $d)
            {
                $c[$k]->{'Cliente'} = $this->conexionn->query("SELECT nombre, CONCAT(dni,'',ruc) AS numero FROM v_clientes WHERE id_cliente = ".$d->id_cli)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;    
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ObtenerDatosImpAll($data)
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM v_ventas_con WHERE id_ped = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->conexionn->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->conexionn->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven." GROUP BY id_prod")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach($c->Detalle as $k => $d)
            {
                $c->Detalle[$k]->{'Producto'} = $this->conexionn->query("SELECT nombre_prod, pres_prod FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ObtenerDatosImp($data)
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM v_ventas_con WHERE id_ven = ?");
            $stm->execute(array($data));
            $c = $stm->fetch(PDO::FETCH_OBJ);
            $c->{'Cliente'} = $this->conexionn->query("SELECT * FROM v_clientes WHERE id_cliente = " . $c->id_cli)
                ->fetch(PDO::FETCH_OBJ);
            /* Traemos el detalle */
            $c->{'Detalle'} = $this->conexionn->query("SELECT id_prod,SUM(cantidad) AS cantidad, precio FROM tm_detalle_venta WHERE id_venta = " . $c->id_ven." GROUP BY id_prod")
                ->fetchAll(PDO::FETCH_OBJ);
            foreach($c->Detalle as $k => $d)
            {
                $c->Detalle[$k]->{'Producto'} = $this->conexionn->query("SELECT nombre_prod, pres_prod FROM v_productos WHERE id_pres = " . $d->id_prod)
                    ->fetch(PDO::FETCH_OBJ);
            }
            return $c;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

public function ProductosFrecuentesRetraso($minVeces)
{
    try {
        // Solo cuenta los productos con retrasos del día actual
        $sql = "
            SELECT 
                pr.id_pres,
                COUNT(*) AS total_retrasos,
                tp.nombre AS nombre_producto
            FROM tm_pedido_retraso pr
            INNER JOIN tm_producto_pres tpp 
                ON pr.id_pres = tpp.id_pres
            INNER JOIN tm_producto tp 
                ON tpp.id_prod = tp.id_prod
            WHERE DATE(pr.fecha_pedido) = CURDATE()  -- 🔍 Solo pedidos del día actual
            GROUP BY pr.id_pres
            HAVING COUNT(*) >= ?
            ORDER BY total_retrasos DESC
        ";

        $stm = $this->conexionn->prepare($sql);
        $stm->execute([$minVeces]);
        return $stm->fetchAll(PDO::FETCH_OBJ);
    } catch (Exception $e) {
        return [];
    }
}

public function ActualizarTiempoEstandar($idsPres, $incremento = 2)
{
    try {
        if (empty($idsPres)) {
            return false;
        }

        // Crear placeholders (?, ?, ?, ...)
        $placeholders = implode(',', array_fill(0, count($idsPres), '?'));

        $sql = "
            UPDATE tm_producto_pres
            SET tiempostandar = tiempostandar + ?
            WHERE id_pres IN ($placeholders)
        ";

        // Mezclar el incremento como primer parámetro
        $params = array_merge([$incremento], $idsPres);

        // 🔍 Log de depuración
        $logPath = __DIR__ . '/../../../reportes/log_rpapedido.txt';
        $log = "[" . date('Y-m-d H:i:s') . "] SQL: $sql | Params: " . json_encode($params) . "\n";
        file_put_contents($logPath, $log, FILE_APPEND);

        $stm = $this->conexionn->prepare($sql);
        $resultado = $stm->execute($params);

        // 🔍 Log después de ejecutar
        $log = "[" . date('Y-m-d H:i:s') . "] Filas afectadas: " . $stm->rowCount() . "\n";
        file_put_contents($logPath, $log, FILE_APPEND);

        return $resultado;
    } catch (Exception $e) {
        $errorLog = "[" . date('Y-m-d H:i:s') . "] ❌ Error en ActualizarTiempoEstandar: " . $e->getMessage() . "\n";
        file_put_contents(__DIR__ . '/../../../reportes/log_rpapedido.txt', $errorLog, FILE_APPEND);
        return false;
    }
}





}