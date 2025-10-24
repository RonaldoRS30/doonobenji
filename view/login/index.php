<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAFETERÍA DOONOBENJI</title>
    <link href='assets/img/restepe.ico' rel='shortcut icon' type='image/x-icon'/>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/animate.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"> -->
    <link href="assets/css/plugins/select/bootstrap-select.css" rel="stylesheet">
    <link href="assets/css/plugins/formvalidation/formValidation.min.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div class="ibox-content">
                <center><img src="assets/img/logo-sistema.png"/></center>
            </div>
            <div class="ibox-content">
                <form id="frm-login" class="m-t" role="form" method="post" action="controller/login.controller.php" autocomplete="off">
  
  <!-- Rol -->
  <div class="form-group">
      <select class="selectpicker form-control cb_tpuser" id="cb_tpuser" name="txt_rol" title="Seleccionar Rol de Usuario">
          <option value="1">ADMINISTRADOR</option>
          <option value="2">CAJA</option>
          <option value="3">ÁREA DE COCINA</option>
          <option value="4">MOZO</option>
      </select>
  </div>

  <!-- Caja y Turno -->
  <div class="opc1 d-none">
      <div class="form-group">
          <select name="txt_caja" class="selectpicker form-control cb_caja" title="Seleccionar Caja" required>
              <?php foreach($this->model->Caja() as $r): ?>
                  <option value="<?php echo $r->id_caja; ?>"><?php echo $r->descripcion; ?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="form-group">
          <select name="txt_turno" class="selectpicker form-control cb_turno" title="Seleccionar Turno">
              <?php foreach($this->model->Turno() as $r): ?>
                  <option value="<?php echo $r->id_turno; ?>"><?php echo $r->descripcion; ?></option>
              <?php endforeach; ?>
          </select>
      </div>
  </div>

  <!-- Área de Producción -->
  <div class="opc4 d-none">
      <div class="form-group">
          <select name="txt_area" class="selectpicker form-control cb_area" title="Seleccionar Área de Producción" required>
              <?php foreach($this->model->AreaProduccion() as $r): ?>
                  <option value="<?php echo $r->id_areap; ?>"><?php echo $r->nombre; ?></option>
              <?php endforeach; ?>
          </select>
      </div>
  </div>

  <!-- Usuario -->
  <div class="form-group">
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-user"></i></span>
          <input type="text" name="txt_usuario" class="form-control" placeholder="Usuario">
      </div>
  </div>

  <!-- Contraseña -->
  <div class="form-group">
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-lock"></i></span>
          <input type="password" name="txt_password" class="form-control" placeholder="Contraseña">
      </div>
  </div>

  <!-- Botón -->
  <button type="submit" class="btn btn-primary block full-width m-b" id="btn-submit">INGRESAR</button>

  <!-- Mensajes -->
  <?php
      if (isset($_GET['m']) == 'e'){
          echo '<div class="alert alert-danger">Datos incorrectos.</div>';
      } elseif (isset($_GET['me']) == 'a') {
          echo '<div class="alert alert-warning">Debe Aperturar Caja</div>';
      }
  ?>

</form>

            </div>
        </div>
    </div>
</body>

<!-- Mainly scripts -->
<script src="assets/js/jquery-2.1.1.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/plugins/select/bootstrap-select.min.js"></script>
<!-- Jquery Validate -->
<script src="assets/js/plugins/formvalidation/formValidation.min.js"></script>
<script src="assets/js/plugins/formvalidation/framework/bootstrap.min.js"></script>
<script src="assets/scripts/login/login.js"></script>

<style>
body.gray-bg {
  font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #f6f1eb 60%, #efe7df 100%);
  min-height: 100vh;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #3b2f2f;
}

/* CONTENEDOR PRINCIPAL */
.middle-box {
  width: 100%;
  max-width: 750px;
  background: linear-gradient(145deg, #fffdfa, #f5f2ee);
  border-radius: 25px;
  padding: 25px; /* espacio entre el borde y el contenido */
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  position: relative;
  box-sizing: border-box;
}

/* Borde naranja más separado */
.middle-box::before {
  content: "";
  position: absolute;
  top: -25px;
  left: -25px;
  right: -25px;
  bottom: -25px;
  background: linear-gradient(135deg, #d49b67, #e8c89b);
  border-radius: 35px;
  z-index: -1;
  box-shadow: 0 0 25px rgba(212, 155, 103, 0.4);
}

/* LOGO */
.ibox-content img {
  display: block;
  margin: 0 auto 40px auto;
  max-width: 160px;
  border-radius: 14px;
  border: 2px solid #e2b88c;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

/* FORMULARIO */
form {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-gap: 22px 30px;
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

/* CAMPOS */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full {
  grid-column: span 2;
}

.selectpicker,
.form-control {
  background: #ffffff !important;
  color: #3b2f2f !important;
  border-radius: 10px;
  border: 1.5px solid #c9b79d;
  font-size: 15px;
  padding: 12px 14px;
  transition: all 0.3s ease;
  width: 100%;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.04);
}

.selectpicker:focus,
.form-control:focus {
  border-color: #c58542 !important;
  box-shadow: 0 0 8px 2px rgba(197, 133, 66, 0.25);
  background: #fff8f3 !important;
  color: #2c1f1f !important;
  font-weight: 600;
}

/* BOTÓN */
.btn-primary {
  grid-column: span 2;
  background: linear-gradient(135deg, #b86a29, #d68943) !important;
  color: #fff;
  font-weight: 700;
  border-radius: 28px;
  padding: 15px;
  font-size: 17px;
  margin-top: 25px;
  transition: all 0.3s ease;
  border: none;
  box-shadow: 0 5px 14px rgba(180, 100, 40, 0.25);
  letter-spacing: 0.5px;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #a55a20, #c67638) !important;
  box-shadow: 0 7px 18px rgba(180, 100, 40, 0.35);
}

/* ALERTAS */
.alert {
  grid-column: span 2;
  background: #fef2f2;
  border: 1.8px solid #f5c2c2;
  color: #b22e2e;
  border-radius: 8px;
  margin-top: 18px;
  padding: 10px 15px;
  text-align: center;
}

/* RESPONSIVE */
@media (max-width: 600px) {
  form {
    grid-template-columns: 1fr;
  }

  .btn-primary,
  .alert {
    grid-column: span 1;
  }

  .middle-box {
    padding: 20px;
  }

  .middle-box::before {
    top: -15px;
    left: -15px;
    right: -15px;
    bottom: -15px;
  }
}
</style>



