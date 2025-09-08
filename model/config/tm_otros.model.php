<?php
include_once("model/rest.model.php");

class ConfigModel
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

    //RESTAURANTE
    public function ListarSM()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM v_mesas");
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

    public function ListarCSM()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_catg_mesa");
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

    //DATOS DE LA EMPRESA
    public function ObtenerDE()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_empresa");
            $stm->execute();
            $r = $stm->fetch(PDO::FETCH_OBJ);

            $alm = new Datos();

            $alm->__SET('id_de', $r->id_de);
            $alm->__SET('trib_acr', $r->trib_acr);
            $alm->__SET('trib_car', $r->trib_car);
            $alm->__SET('di_acr', $r->di_acr);
            $alm->__SET('di_car', $r->di_car);
            $alm->__SET('imp_acr', $r->imp_acr);
            $alm->__SET('imp_val', $r->imp_val);
            $alm->__SET('imp_icbper', $r->imp_icbper);
            $alm->__SET('mon_acr', $r->mon_acr);
            $alm->__SET('mon_val', $r->mon_val);
            $alm->__SET('ruc', $r->ruc);
            $alm->__SET('raz_soc', $r->raz_soc);
            $alm->__SET('direccion', $r->direccion);
            $alm->__SET('logo', $r->logo);

            return $alm;
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function ActualizarDE(Datos $data)
    {
        try 
        {
            $sql = "UPDATE tm_empresa SET

                trib_acr = ?,
                trib_car = ?,
                di_acr = ?,
                di_car = ?,
                imp_acr = ?,
                imp_val = ?,
                imp_icbper = ?,
                mon_acr = ?,
                mon_val = ?,
                ruc = ?,
                raz_soc  = ?,
                direccion = ?,
                logo = ?

            WHERE id_de = ?";

            $this->conexionn->prepare($sql)
                 ->execute(
                array(
                    $data->__GET('trib_acr'), 
                    $data->__GET('trib_car'), 
                    $data->__GET('di_acr'),
                    $data->__GET('di_car'),
                    $data->__GET('imp_acr'),
                    $data->__GET('imp_val'),
                    $data->__GET('imp_icbper'),
                    $data->__GET('mon_acr'),
                    $data->__GET('mon_val'),
                    $data->__GET('ruc'),
                    $data->__GET('raz_soc'),
                    $data->__GET('direccion'),
                    $data->__GET('logo'),                    
                    $data->__GET('id_de')
                    )
                );
            /* ACTUALIZAR DATOS */
            $_SESSION["imp_icbper"] = $data->__GET('imp_icbper');
            $_SESSION["igv"] = $data->__GET('imp_val');
            $_SESSION["moneda"] = $data->__GET('mon_val');
            $_SESSION["tribAcr"] = $data->__GET('trib_acr');
            $_SESSION["tribCar"] = $data->__GET('trib_car');
            $_SESSION["diAcr"] = $data->__GET('di_acr');
            $_SESSION["diCar"] = $data->__GET('di_car');
            $_SESSION["impAcr"] = $data->__GET('imp_acr');
            $_SESSION["monAcr"] = $data->__GET('mon_acr');
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    //TIPO DE DOCUMENTO
    public function ListarTD()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_tipo_doc");
            $stm->execute();            
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json; 
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function GuardarTD($data)
    {
        try 
        {
            $sql = "UPDATE tm_tipo_doc SET serie = ?,numero = ? WHERE id_tipo_doc = ?";
            $this->conexionn->prepare($sql)->execute(array($data['serie'],$data['numero'],$data['cod_td']));
        } catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    //INDICADORES
    public function ListarI01()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_margen_venta");
            $stm->execute();            
            $c = $stm->fetchAll(PDO::FETCH_OBJ);
            $data = array("data" => $c);
            $json = json_encode($data);
            echo $json; 
        }
        catch(Exception $e)
        {
            die($e->getMessage());
        }
    }

    public function GuardarI01($data)
    {
        try 
        {
            $sql = "UPDATE tm_margen_venta SET margen = ? WHERE id = ?";
            $this->conexionn->prepare($sql)->execute(array($data['m_venta'],$data['cod_ind']));
        } 
        catch (Exception $e) 
        {
            die($e->getMessage());
        }
    }

    public function Pais()
    {
        try
        {      
            $stm = $this->conexionn->prepare("SELECT * FROM tm_area_prod");
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
}