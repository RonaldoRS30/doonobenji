<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- jsPDF AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


<div class="wrapper wrapper-content animated fadeIn">
    <div class="ibox">
        <div class="ibox-title">
            <div class="ibox-title-buttons pull-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Excel</button>
                <a class="btn btn-warning" href="lista_tm_informes.php"><i class="fa fa-arrow-left"></i> Atr&aacute;s
                </a>
            </div>
            <h5><strong><i class="fa fa-calendar"></i> Cantidad de pedidos con retraso</strong></h5>
        </div>
        <div class="ibox-content" style="position: relative; min-height: 30px;">
            <div class="row">
                <div class="col-sm-2">
                    <label class="control-label">Fecha Inicio</label>
                    <input type="text" class="form-control text-center" name="start" id="start"
                        value="<?php echo '01-' . $fechaa; ?>" placeholder="dd-mm-yyyy" />
                </div>
                <div class="col-sm-2">
                    <label class="control-label">Fecha Fin</label>
                    <input type="text" class="form-control text-center" name="end" id="end"
                        value="<?php echo $fecha; ?>" placeholder="dd-mm-yyyy" />
                </div>

                <div class="col-sm-2">
                    <label for="estado">Tipo de pedido</label>
                    <select name="estado" id="estado" class="form-control selectpicker" data-live-search="true"
                        autocomplete="off" data-size="5">
                        <option value="%" selected>Todos</option>
                        <option value="1">MESA</option>
                        <option value="2">PARA LLEVAR</option>
                        <option value="3">DELIVERY</option>
                    </select>
                </div>


            </div>





            <!-- JS de Datepicker (y jQuery) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.es.min.js"></script>
            <br>
            <div class="row mt-4 mb-7 justify-content-center">
                <div class="col-sm-3 text-center">
                    <button id="filtrarPedidosBtn" class="btn btn-success w-100">Filtrar</button>
                </div>
                <div class="col-sm-3 text-center">
                    <button id="generarPDFBtn" class="btn btn-danger w-100"><i class="fa fa-file-pdf-o"></i> Generar
                        PDF</button>
                </div>

            </div>

            <br>
            <div class="punteo d-flex justify-content-center align-items-center my-4 gap-5">
                <div class="text-center">
                    <h5><strong>Cantidad Total</strong></h5>
                    <h1><strong id="total_retrasos"></strong></h1>
                </div>

                <div class="text-center">
                    <h5><strong>Promedio de demora</strong></h5>
                    <h1><strong id="promedio_demora"></strong></h1>
                </div>
            </div>
            <div class="table-responsive">
                <div class="col-sm-12 table-responsive">
                    <table id="tablaPedidos" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Cod Pedido Retraso</th>
                                <th>ID Pedido</th>
                                <th>Nombre Cliente</th>
                                <th>Producto</th>
                                <th>Tiempo Standar(min:seg)</th>
                                <th>Tiempo demorado(min:seg)</th>
                                <th>Fecha Pedido</th>
                                <th>Fecha Envío</th>
                                <th>Tipo de pedido</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>

    $(document).ready(function () {
        $('#tipo_doc').selectpicker({
            style: 'btn-outline-primary',
            size: 5,
            liveSearch: true
        });
    });

    $(document).ready(function () {
        $('#start, #end').datepicker({
            format: "dd-mm-yyyy",
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
    });

    $(document).ready(function () {
        // Obtener fecha actual en formato dd-mm-yyyy
        let hoy = new Date();
        let dd = String(hoy.getDate()).padStart(2, '0');
        let mm = String(hoy.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
        let yyyy = hoy.getFullYear();
        let fechaHoy = dd + '-' + mm + '-' + yyyy;

        // Asignar fecha de hoy a los inputs
        $('#start').val(fechaHoy);
        $('#end').val(fechaHoy);

        // Inicializar Datepicker
        $('#start, #end').datepicker({
            format: "dd-mm-yyyy",
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });

        // Inicializar selectpicker y dejar "Todos" por defecto
        $('#estado').selectpicker('val', '%');

        // Ejecutar el filtrado automáticamente al cargar la página
        $('#filtrarPedidosBtn').trigger('click');
    });



    $(document).ready(function () {
        // Inicializar DataTable
        var tabla = $('#tablaPedidos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });

        // Filtrar pedidos y actualizar tabla
        $('#filtrarPedidosBtn').on('click', function () {
            let start = $('#start').val();
            let end = $('#end').val();

            // Convertir a formato YYYY-MM-DD
            let startDate = start.split('-').reverse().join('-'); // 'yyyy-mm-dd'
            let endDate = end.split('-').reverse().join('-') + ' 23:59:59'; // incluir todo el día
            let estado = $('#estado').val();
            $.ajax({
                type: "POST",
                url: "?c=Informe&a=PedidosRetraso",
                data: { start: startDate, end: endDate, estado: estado },
                dataType: "json",
                success: function (res) {
                    tabla.clear(); // Limpia la tabla
                    let totalSegundosDemora = 0; // acumulador para calcular promedio
                    let totalPedidos = res.data.length;
                    res.data.forEach(function (pedido) {
                        let tiempoDecimal = parseFloat(pedido.tiempostandar); // ejemplo 1.5
                        let minutos = Math.floor(tiempoDecimal);            // parte entera -> minutos
                        let segundos = Math.round((tiempoDecimal - minutos) * 60); // parte decimal -> segundos

                        let tiempoFormateado = String(minutos).padStart(2, '0') + ':' + String(segundos).padStart(2, '0');

                        // Calcular tiempo de demora entre fecha_pedido y fecha_envio
                        let fechaPedido = new Date(pedido.fecha_pedido);
                        let fechaEnvio = new Date(pedido.fecha_envio);
                        let diffSegundos = Math.floor((fechaEnvio - fechaPedido) / 1000);
                        let diffMinutos = Math.floor(diffSegundos / 60);
                        let diffSegundosRest = diffSegundos % 60;
                        let tiempoDemora = String(diffMinutos).padStart(2, '0') + ':' + String(diffSegundosRest).padStart(2, '0');

                        // Acumulamos segundos totales de demora
                        totalSegundosDemora += diffSegundos;
                        let estadoTexto = '';
                        switch (pedido.estado) {
                            case '1':
                                estadoTexto = 'PARA MESA';
                                break;
                            case '2':
                                estadoTexto = 'PARA LLEVAR';
                                break;
                            case '3':
                                estadoTexto = 'DELIVERY';
                                break;
                            default:
                                estadoTexto = pedido.estado; // si no coincide, se muestra tal cual
                        }

                        tabla.row.add([
                            pedido.cod_pedido_retraso,
                            pedido.id_pedido,
                            pedido.nombre_cliente,// <-- nueva columna
                            pedido.nombre_producto,
                            tiempoFormateado, // <-- tiempo formateado
                            tiempoDemora,
                            pedido.fecha_pedido,
                            pedido.fecha_envio,
                            estadoTexto
                        ]).draw();
                    });
                    // Actualizar total de registros
                    $('#total_retrasos').text(totalPedidos);
                    if (totalPedidos > 0) {
                        let promedioSegundos = Math.floor(totalSegundosDemora / totalPedidos);
                        let promedioMinutos = Math.floor(promedioSegundos / 60);
                        let promedioSegundosRest = promedioSegundos % 60;
                        let promedioFormateado = String(promedioMinutos).padStart(2, '0') + ':' + String(promedioSegundosRest).padStart(2, '0');
                        $('#promedio_demora').text(promedioFormateado);
                    } else {
                        $('#promedio_demora').text('00:00');
                    }

                },
                error: function (err) {
                    console.error(err);
                }
            });
        });
        // **Disparar automáticamente al cargar la página**
        $('#filtrarPedidosBtn').trigger('click');
    });

$('#generarPDFBtn').on('click', function() {
    let start = $('#start').val().split('-').reverse().join('-'); // yyyy-mm-dd
    let end = $('#end').val().split('-').reverse().join('-');     // yyyy-mm-dd
    let estado = $('#estado').val();

    // Abrir PDF en otra pestaña usando el nombre correcto del archivo
    window.open(`view/informes/ventas/inf_pedidoretraso_pdf.php?start=${start}&end=${end}&estado=${estado}`, '_blank');
});


</script>