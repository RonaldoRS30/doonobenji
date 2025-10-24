<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CAFETERÍA DOONOBENJI</title>
  <link href="assets/img/restepe.ico" rel="shortcut icon" type="image/x-icon"/>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/animate.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background: #f6f4f1 url(assets/img/login-bg.png) repeat;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .middle-box {
      background: #fff;
      border: 3px solid #d77a2b;
      border-radius: 20px;
      padding: 50px 60px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      max-width: 620px;
      width: 90%;
      text-align: center;
    }

    /* Logo */
    .ibox-content img {
      width: 190px;
      margin-bottom: 25px;
      border-radius: 14px;
    }

    /* Panel de advertencia */
    .panel-transparent {
      background: transparent;
      color: #3b2f2f;
      margin-top: 10px;
      margin-bottom: 30px;
    }

    .panel-transparent i {
      font-size: 70px;
      color: #d77a2b;
      margin-bottom: 12px;
    }

    .panel-transparent h2 {
      font-weight: 700;
      font-size: 28px;
      color: #c56d1e;
      margin-bottom: 12px;
    }

    .panel-transparent p {
      font-size: 17px;
      color: #4b3b3b;
      margin: 0 0 10px;
      line-height: 1.6;
    }

    /* Footer con botones */
    .ibox-footer {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 30px;
      margin-top: 25px;
    }

    .btn {
      border-radius: 28px;
      font-weight: 600;
      padding: 12px 45px;
      font-size: 16px;
      transition: all 0.3s ease-in-out;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      min-width: 180px;
    }

    .btn-warning {
      background: #e1b34f;
      border: none;
      color: #fff;
    }

    .btn-warning:hover {
      background: #d09c38;
      transform: translateY(-2px);
    }

    .btn-primary {
      background: #c46d25;
      border: none;
      color: #fff;
    }

    .btn-primary:hover {
      background: #a3561b;
      transform: translateY(-2px);
    }

    /* Responsivo */
    @media (max-width: 700px) {
      .middle-box {
        padding: 35px 25px;
      }

      .panel-transparent i {
        font-size: 55px;
      }

      .panel-transparent h2 {
        font-size: 23px;
      }

      .panel-transparent p {
        font-size: 15px;
      }

      .ibox-footer {
        flex-direction: column;
        gap: 15px;
      }

      .btn {
        width: 80%;
      }
    }
  </style>
</head>

<body>
  <div class="middle-box animated fadeInDown">
    <div class="ibox-content">
      <img src="assets/img/logo-sistema.png" alt="Logo">
    </div>

    <div class="panel panel-transparent">
      <i class="fa fa-warning"></i>
      <h2>Advertencia</h2>
      <p>Los datos seleccionados no coinciden con una Apertura de Caja.</p>
      <p>¿Desea continuar de todas formas?</p>
    </div>

    <div class="ibox-footer">
      <a href="close_session.php" class="btn btn-warning">Regresar</a>
      <a href="lista_tm_tablero.php" class="btn btn-primary">Continuar</a>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/js/jquery-2.1.1.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script>
    document.oncontextmenu = () => false;
    document.onselectstart = (e) => ['text', 'textarea', 'password'].includes(e.target.type);
    document.ondragstart = () => false;
  </script>
</body>
</html>
