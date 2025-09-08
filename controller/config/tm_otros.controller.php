<?php
require_once 'model/config/tm_otros.entidad.php';
require_once 'model/config/tm_otros.model.php';

class ConfigController{
    
    private $model;

    public function __CONSTRUCT(){
        $this->model = new ConfigModel();
    }
    
    public function Index(){
        require_once 'view/header.php';
        require_once 'view/config/tm_config.php';
        require_once 'view/footer.php';
    }

    //DATOS DE LA EMPRESA
    public function IndexDE(){
        $alm = new Datos();
        $alm = $this->model->ObtenerDE();
        require_once 'view/header.php';
        require_once 'view/config/sist/de.php';
        require_once 'view/footer.php';
    }

    public function GuardarDE(){
        $alm = new Datos();

        $alm->__SET('id_de', $_REQUEST['id']);
        $alm->__SET('trib_acr', $_REQUEST['tribAcr']);
        $alm->__SET('trib_car', $_REQUEST['tribCar']);
        $alm->__SET('di_acr', $_REQUEST['diAcr']);
        $alm->__SET('di_car', $_REQUEST['diCar']);
        $alm->__SET('imp_acr', $_REQUEST['impAcr']);
        $alm->__SET('imp_val', $_REQUEST['impVal']);
        $alm->__SET('imp_icbper', $_REQUEST['imp_icbper']);
        $alm->__SET('mon_acr', $_REQUEST['monAcr']);
        $alm->__SET('mon_val', $_REQUEST['monVal']);
        $alm->__SET('ruc', $_REQUEST['ruc']);
        $alm->__SET('raz_soc', $_REQUEST['razSoc']);
        $alm->__SET('direccion', $_REQUEST['direc']);
        $alm->__SET('logo', $_REQUEST['logo']);

        if( !empty( $_FILES['logo']['name'] ) ){
            $logo = date('ymdhis') . '-' . strtolower($_FILES['logo']['name']);
            move_uploaded_file ($_FILES['logo']['tmp_name'], 'assets/img/' . $logo);          
            $alm->__SET('logo', $logo);
        }

        if($_REQUEST['id'] != ''){
           $this->model->ActualizarDE($alm);
           //header('Location: lista_tm_otros.php?c=Config&a=IndexDE&m=u');
           echo("<script>location.href = 'lista_tm_otros.php?c=Config&a=IndexDE&m=u';</script>");
        }
    }

    //TIPO DE DOCUMENTO
    public function IndexTD(){
        require_once 'view/header.php';
        require_once 'view/config/sist/tipo_doc.php';
        require_once 'view/footer.php';
    }

    public function ListarTD(){
        $this->model->ListarTD();
    }

    public function GuardarTD(){
        if($_POST['cod_td'] != '' and $_POST['serie'] != '' and $_POST['numero'] != ''){
            print_r(json_encode( $this->model->GuardarTD($_POST)));
        }
    }

    //INDICADOR 01
    public function IndexI01(){
        require_once 'view/header.php';
        require_once 'view/config/indi/indicador_01.php';
        require_once 'view/footer.php';
    }

    public function ListarI01(){
        $this->model->ListarI01();
    }

    public function GuardarI01(){
        if($_POST['cod_ind'] != ''){
            print_r(json_encode( $this->model->GuardarI01($_POST)));
        }
    }

}
?> 