<?php
    class Mesa
    {
        private $id_mesa;
        private $id_catg;
        private $nro_mesa;
        private $estado;
        public function __GET($k){ return $this->$k; }
        public function __SET($k, $v){ return $this->$k = $v; }
    }
    class Mostrador
    {
        public function __GET($k){ return $this->$k; }
        public function __SET($k, $v){ return $this->$k = $v; }
    }
    class Delivery
    {
        public function __GET($k){ return $this->$k; }
        public function __SET($k, $v){ return $this->$k = $v; }
    }
    class Pedido
    {
        public function __GET($k){ return $this->$k; }
        public function __SET($k, $v){ return $this->$k = $v; }
    }
    class Venta
    {
        public function __GET($k){ return $this->$k; }
        public function __SET($k, $v){ return $this->$k = $v; }
    }

class MotivoCancelacion
{
    private $cod_pedido;
    private $motivo_select; // motivo del select
    private $motivo_otro;   // motivo libre si eligieron "Otro"
    private $motivo;        // motivo unificado para reportes/RPA
    private $usuario;

    public function __GET($k){ return $this->$k; }
    public function __SET($k, $v){ return $this->$k = $v; }
}
?>