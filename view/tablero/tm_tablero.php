<?php
    date_default_timezone_set($_SESSION["zona_horaria"]);
    setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
    $fecha = date("d-m-Y h:i A");
    $fechaa = date("d-m-Y 07:00");
?>
<input type="hidden" id="moneda" value="<?php echo $_SESSION["moneda"]; ?>"/>
<input type="hidden" id="dia_a" value="<?php echo $fecha; ?>"/>

<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" style="overflow: inherit;">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-1">
                            <i class="fa fa-info-circle"></i> Datos obtenidos:
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-r" name="start" id="start" value="<?php echo $fechaa,' AM'; ?>"/>
                                    <span class="input-group-addon">al</span>
                                    <input type="text" class="form-control bg-r" name="end" id="end" value="<?php echo $fecha; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="p-w-md m-t-sm">
                        <div class="row">
                            <div class="col-sm-8"> 
                                <div class="row">
                                    <div class="col-sm-4">
                                        <span class="stats-label text-navy">Ventas en efectivo</span>
                                        <h4 class="efe"></h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="stats-label text-success">Ventas con tarjeta</span>
                                        <h4 class="tar"></h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="stats-label text-info">Total de Ventas</span>
                                        <h4 class="total_v"></h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="stats-label text-navy">Total de ingresos de caja</span>
                                        <h4><span id="ing"></span></h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="stats-label text-danger">Total de egresos de caja</span>
                                        <h4><span id="gas"></span></h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="stats-label text-warning">Total de descuentos</span>
                                        <h4><span class="des"></span></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="row m-t-xs">
                                    <div class="col-sm-6 col-sm-offset-6">
                                        <h3 class="m-b-xs text-navy">Efectivo real</h3>
                                        <h1 class="no-margins" id="efe_real"></h1>
                                        <div class="font-bold text-navy">100% <i class="fa fa-money"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row"
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span><i class="fa fa-money fa-2x pull-right"></i></span>
                    <h5>Ventas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><span class="total_v"></span></h1>
                    <div class="stat-percent font-bold text-warning">Descuento <span class="des"></span> <i class="fa fa-level-down"></i></div>
                    <small>Total de ventas</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span><i class="fa fa-paypal fa-2x pull-right"></i></span>
                    <h5>Por tipo de pago</h5>
                </div>
                <div class="ibox-content">
                <div class="row rco">
                    <div class="col-lg-6">
                        <div class="font-bold text-default">Efectivo <span class="text-navy efe_p"></span></div>
                        <small><span class="efe"></span> - <span class="text-navy">100%</span></small>
                    </div>
                    <div class="col-lg-6">
                        <div class="stat-percent font-bold text-default">Tarjeta <span class="text-navy tar_p"></span></div>
                        <small class="stat-percent"><span class="tar"></span> - <span class="text-navy">100%</span></small>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span><i class="fa fa-line-chart fa-1x pull-right"></i></span>
                    <h5>Promedio de consumo</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-navy" id="pro_m"></h1>
                    <div class="stat-percent font-bold text-navy">por mesa</div>
                    <small>en <span class="t_mesas"></span> venta(s)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span><i class="fa fa-line-chart fa-1x pull-right"></i></span>
                    <h5>Promedio de consumo</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-success" id="pro_mo"></h1>
                    <div class="stat-percent font-bold text-success">para llevar</div>
                    <small>en <span class="t_most"></span> venta(s)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">RANKING DIARIO</span>
                    <h5>Mozo del d&iacute;a</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins" id="mozo"></h1>
                    <div class="stat-percent font-bold text-navy"><span id="t_ped"></span>% de las ventas <i class="fa fa-level-up"></i></div>
                    <small id="pedidos"></small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">ATENCI&Oacute;N</span>
                    <h5>Pedidos Anulados</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                            <div class="col-md-6">
                                <h1 class="no-margins" id="pa_me"></h1>
                                <div class="font-bold text-danger">Mesas <i class="fa fa-level-down"></i></div>
                            </div>
                            <div class="col-md-6" style="text-align: right;">
                                <h1 class="no-margins" id="pa_mo"></h1>
                                <div class="font-bold text-danger">Para llevar <i class="fa fa-level-down"></i></div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">DIARIO</span>
                    <h5>Mesas atendidas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins t_mesas"></h1>
                    <div class="stat-percent font-bold text-navy">100% <i class="fa fa-bolt"></i></div>
                    <small>Mesa(s) atendida(s)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right">DIARIO</span>
                    <h5>Pedidos atendidos</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins t_most"></h1>
                    <div class="stat-percent font-bold text-success">100% <i class="fa fa-bolt"></i></div>
                    <small>Pedidos(s) atendido(s)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">TODOS LOS PRODUCTOS</span>
                    <h5>10 M&aacute;s vendidos</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover no-margins">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th style="text-align: center;">Ventas</th>
                            <th style="text-align: center;">Importe</th>
                            <th style="text-align: center;">% Ventas</th>
                        </tr>
                        </thead>
                        <tbody id="lista_productos">           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right">PLATOS PRINCIPALES</span>
                    <h5>10 M&aacute;s vendidos</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover no-margins">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th style="text-align: center;">Ventas</th>
                            <th style="text-align: center;">Importe</th>
                            <th style="text-align: center;">% Ventas</th>
                        </tr>
                        </thead>
                        <tbody id="lista_platos">           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-lg-2">
            <button type="button" class="btn btn-danger btn-block" id="btn-motivos-cancelacion">
                <i class="fa fa-ban"></i> Pedidos Cancelados - Motivos
            </button>
        </div>
</div>



    </div>
</div>

<div class="modal fade" id="mdl-motivos-cancelacion" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-ban"></i> Motivos de Pedidos Cancelados</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table id="tabla-motivos" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th># Pedido</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se llenará con DataTable o PHP -->
            </tbody>
        </table>
        
        <!-- Contenedor para texto motivo más frecuente -->
        <div id="motivo-mas-frecuente" style="margin-top: 10px; font-weight: bold;"></div>

        <!-- Lienzo para gráfico de pastel -->
        <!-- Contenedor para gráficos centrado -->
<div style="text-align:center; margin-top:20px;">
  <canvas id="grafico-motivos" width="450" height="300" style="background-color:white; margin: 0 auto; display: block;"></canvas>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Asegúrate de incluir Chart.js -->

<script>
$(document).ready(function() {

  $('#btn-motivos-cancelacion').click(function() {
    $('#mdl-motivos-cancelacion').modal('show');

    if (!$.fn.DataTable.isDataTable('#tabla-motivos')) {
      $('#tabla-motivos').DataTable({
        ajax: {
          url: '?c=Tablero&a=ListarMotivosCancelacion',
          type: 'POST',
          dataSrc: function(json) {
            // Actualizar texto motivo más frecuente
            $('#motivo-mas-frecuente').text('Motivo más recurrente: ' + json.summary.mostFrequent);

            // Preparar canvas con fondo blanco antes de crear gráfico
            var canvas = document.getElementById('grafico-motivos');
            var ctx = canvas.getContext('2d');
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.restore();

            // Destruir gráfico anterior si existe
            if (window.graficoMotivos) {
              window.graficoMotivos.destroy();
            }

            // Crear gráfico de pastel
            window.graficoMotivos = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: Object.keys(json.summary.counts),
                datasets: [{
                  data: Object.values(json.summary.counts),
                  backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8AFF33', '#FF5733']
                }]
              },
              options: {
                responsive: false,
                plugins: {
                  legend: { position: 'top' }
                }
              }
            });

            return json.data;
          }
        },
        columns: [
          { data: 'cod_pedido', title: '# Pedido' },
          { data: 'motivo', title: 'Motivo' },
          { data: 'usuario', title: 'Usuario' },
          { data: 'fecha_reg', title: 'Fecha' }
        ],
        responsive: true,
        autoWidth: false,
        scrollX: false,
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [{
          extend: 'pdfHtml5',
          text: 'Generar PDF',
          title: 'Motivos de Pedidos Cancelados',
          exportOptions: { columns: ':visible' },
          customize: function(doc) {
            doc.pageMargins = [40, 60, 40, 60];
            doc.defaultStyle.fontSize = 12;
            doc.content[1].table.widths = ['15%', '45%', '20%', '20%'];
            doc.content[0].alignment = 'center';
            doc.content[0].fontSize = 16;
            doc.content[0].bold = true;

            doc.content.push({
              text: 'Motivo más recurrente: ' + $('#motivo-mas-frecuente').text().replace('Motivo más recurrente: ', ''),
              margin: [0, 20, 0, 10],
              fontSize: 12,
              bold: true
            });

            // OMITIR la inserción del gráfico para evitar el error de pdfMake
          }
        }]
      });
    } else {
      $('#tabla-motivos').DataTable().ajax.reload();
    }
  });

});

</script>


</script>

<script src="assets/scripts/tablero/func-tablero.js"></script>


