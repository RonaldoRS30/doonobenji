$(function() {
	nropedidosMesa();
	setInterval(nropedidosMesa, 10000);
	nropedidosMostrador();
	setInterval(nropedidosMostrador, 10000);
	nropedidosDelivery();
	setInterval(nropedidosDelivery, 10000);
});	

/* Mostrar todos los pedidos realizados en las mesas */
var contMe = 0;
var nropedidosMesa = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarM',
        success: function (data){
	        $.each(data, function(i, item) {
				var nroPedMe = parseInt(item.Total.nro_p);
				$('#cant_pedidos_mesa').text(nroPedMe);
	    		if(parseInt(nroPedMe) !== contMe){
	    			contMe = 0;
	    			pedidosMesa();
	    			var sound = new buzz.sound("assets/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contMe = nroPedMe + contMe;
	    		}
	    		console.log('contMe = '+contMe+' <> NroPedMe = '+nroPedMe);
			})
		}
	})
}

var pedidosMesa = function(){
	moment.locale('es');
	$('#list_pedidos_mesa').empty();
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarM',
        success: function (data){
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		if (item.id_tipo == 2){
				probar = 'primary';
				nombar = 'En espera';
				accion = 'despMe';
    		} else if(item.id_tipo == 1){
    			if(item.estado == 'a'){
					probar = 'primary';
					nombar = 'En espera';
					accion = 'prepMe';
	    		} else if(item.estado == 'p'){
					probar = 'warning';
					nombar = 'En preparacion';
					accion = 'despMe';
	    		}
    		}
// Convertir tiempo estándar (decimal) a segundos para el temporizador
// Ejemplo: item.tiempostandar = 1.5 significa 1 minuto y 30 segundos
var minutos = Math.floor(item.tiempostandar);                     // parte entera -> minutos
var segundos = Math.round((item.tiempostandar - minutos) * 60);   // parte decimal -> segundos

// Multiplicar por la cantidad
var tiempoTotal = (minutos * 60 + segundos) * item.cantidad;      // total en segundos

// Obtener la fecha de pedido como objeto Date
var fechaPedido = new Date(item.fecha_pedido);

// Obtener diferencia en segundos entre ahora y la fecha de pedido
var ahora = new Date();
var segundosTranscurridos = Math.floor((ahora - fechaPedido) / 1000);

// Calcular tiempo restante
var tiempoRestante = tiempoTotal - segundosTranscurridos;
if(tiempoRestante < 0) tiempoRestante = 0; // si ya pasó, mostrar 0


    		$('#list_pedidos_mesa')
				.append(
					$('<li class="success-element limost"/>')
				 .attr('data-timer', tiempoRestante)  // <- aquí guardamos los segundos
 .attr('data-id-pedido', item.id_pedido)
    .attr('data-id-pres', item.id_pres)
	    .attr('data-id-detalle_pedido', item.id_detalle_pedido)

					.append(
						$('<div class="row"/>')
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
										$('<strong/>')
										.html(item.nro_mesa+'<br>'+item.desc_m)
								)
							)
						.append(
								$('<div class="col-md-2"/>')
									.append(
									$('<span/>')
										.html(
											item.cantidad + ' ' +
											'<span class="nombre-prod">' + item.nombre_prod + '</span> ' +
											'<span class="label label-info">' + item.pres_prod + '</span> ' +
											'<span class="label label-warning">' + item.CProducto.desc_c + '</span><br>' +
											'<i class="fa fa-comment"></i> <small class="text-navy"><em>' + item.comentario + '</em></small>'
										)
								)
							)
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<span/>')
										.html(horaPedido)
								)
							)
						    .append(
                                    $('<div class="col-md-2" style="text-align: center;"/>')
                                      .append(
                $('<span class="temporizador"/>').html(formatTime(tiempoRestante))
            )
                                )
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<div class="progress progress-striped active" style="margin-bottom: -20px;"/>')
										.append(
											$('<div style="width: 100%" aria-valuemax="50" aria-valuemin="0" role="progressbar" class="progress-bar progress-bar-'+probar+'"/>')
												.append(
													$('<span/>')
													.html(nombar)
										)
									)
								)
							)
							.append(
								$('<div class="col-md-2"/>')
									.append(
										$('<span/>')
										.html(item.nombres+' '+item.ape_paterno)
								)
							)
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
											$('<a onclick="'+accion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');"/>')
												.append(
												$('<button class="btn btn-outline btn-primary dim" type="button" style="margin-bottom: 0px !important;margin-top: -5px !important;"/>')
												.append(
													$('<i class="fa fa-check"/>')
										)
									)
								)
							)
						)
					);				
    		});
			  // Iniciar temporizador en tiempo real
            iniciarTemporizadores();
        }
    });
}
var temporizadorInterval = null;

function iniciarTemporizadores(){
    if(temporizadorInterval) return; // Si ya existe, no crear otro

    temporizadorInterval = setInterval(function(){
        try {
            $('#list_pedidos_mesa li').each(function(){
                var timer = parseInt($(this).attr('data-timer'));

                if(isNaN(timer)){
                    throw new Error('El temporizador no es un número válido para el pedido.');
                }

                if(timer > 0){
                    timer--;
                    $(this).attr('data-timer', timer);
                    $(this).find('.temporizador').text(formatTime(timer));
                } else {
                    $(this).find('.temporizador').text('00:00');

                    // Verificar si ya se mostró la alerta
                    if($(this).attr('data-alerta') !== "1"){
                        // Cambiar color de la fila a rojo
                        $(this).css('background-color', '#f2dede'); // rojo claro estilo bootstrap danger

                        // Lanzar alerta
						var nombre_prod = $(this).find('.nombre-prod').text();

                        var mesa = $(this).find('strong').text().split('\n')[0];
						//var nombre_prod = $(this).find('span').first().text();
					toastr.warning('¡El pedido ' + nombre_prod + ' de la mesa ' + mesa + ' está retrasado!');
                        // Marcar como alerta mostrada
                        $(this).attr('data-alerta', "1");

                        // --- INSERTAR EN tm_pedido_retraso ---
                        var id_pedido = $(this).data('id-pedido');   // agregar data-id-pedido al generar li
                        var id_pres = $(this).data('id-pres');       // agregar data-id-pres
						var id_detalle_pedido = $(this).data('id-detalle_pedido'); // agregar data-id-detalle_pedido
                        $.ajax({
                            type: "POST",
                            url: "?c=AreaProd&a=InsertarPedidoRetraso",
                            data: {
                                id_pedido: id_pedido,
                                id_pres: id_pres,
								id_detalle_pedido: id_detalle_pedido,
								estado: 1  // <- indicador de la función

                            },
                            success: function(res){
                                console.log("Pedido retrasado insertado correctamente:", res);
                            },
                            error: function(err){
                                console.error("Error al insertar pedido retrasado:", err);
                            }
                        });
                    }
                }
            });
        } catch(err) {
            console.error('Error en el temporizador:', err);
            toastr.error('Error en el temporizador: ' + err.message);
        }
    }, 1000);
}



function formatTime(segundos){
    var m = Math.floor(segundos / 60);
    var s = segundos % 60;
    // Agregar ceros a la izquierda si es menor que 10
    return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
}

function recargarPedidos() {
    pedidosMesa(); // Llama a tu función de carga de pedidos
pedidosMostrador();
}

// Recargar cada 5 segundos (5000 ms)
//setInterval(recargarPedidos, 5000);



/* Mostrar todos los pedidos realizados en el mostrador o para llevar */
var contMo = 0;
var nropedidosMostrador = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarMO',
        success: function (data){
        	$.each(data, function(i, item) {
				var nroPedMo = parseInt(item.Total.nro_p);
				$('#cant_pedidos_most').text(nroPedMo);
	    		if(parseInt(nroPedMo) !== contMo){
	    			contMo = 0;
	    			pedidosMostrador();
	    			var sound = new buzz.sound("assets/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contMo = nroPedMo + contMo;
	    		}
	    		console.log('contMo = '+contMo+' <> NroPedMo = '+nroPedMo);
			})
		}
	})
}

var pedidosMostrador = function(){
	moment.locale('es');
	$('#list_pedidos_most').empty();
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarMO',
        success: function (data){
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		if (item.id_tipo == 2){
	    			mprobar = 'primary';
	    			mnombar = 'En espera';
	    			maccion = 'despMo';
    		} else if(item.id_tipo == 1){
    				if(item.estado == 'a'){
	    			mprobar = 'primary';
	    			mnombar = 'En espera';
	    			maccion = 'prepMo';
	    		} else if(item.estado == 'p'){
	    			mprobar = 'warning';
	    			mnombar = 'En preparacion';
	    			maccion = 'despMo';
	    		}
    		}

// Convertir tiempo estándar (decimal) a segundos para el temporizador
// Ejemplo: item.tiempostandar = 1.5 significa 1 minuto y 30 segundos
var minutos = Math.floor(item.tiempostandar);                     // parte entera -> minutos
var segundos = Math.round((item.tiempostandar - minutos) * 60);   // parte decimal -> segundos

// Multiplicar por la cantidad
var tiempoTotal = (minutos * 60 + segundos) * item.cantidad;      // total en segundos

// Obtener la fecha de pedido como objeto Date
var fechaPedido = new Date(item.fecha_pedido);

// Obtener diferencia en segundos entre ahora y la fecha de pedido
var ahora = new Date();
var segundosTranscurridos = Math.floor((ahora - fechaPedido) / 1000);

// Calcular tiempo restante
var tiempoRestante = tiempoTotal - segundosTranscurridos;
if(tiempoRestante < 0) tiempoRestante = 0; // si ya pasó, mostrar 0


    		$('#list_pedidos_most')
				.append(
					$('<li class="success-element limost"/>')
					 .attr('data-timer', tiempoRestante)  // <- aquí guardamos los segundos
 .attr('data-id-pedido', item.id_pedido)
    .attr('data-id-pres', item.id_pres)
	    .attr('data-id-detalle_pedido', item.id_detalle_pedido)				
					
					.append(
						$('<div class="row"/>')
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
										$('<strong/>')
										.html('<i class="fa fa-slack"></i> '+item.nro_pedido)
								)
							)
							.append(
								$('<div class="col-md-2"/>')
									.append(
									$('<span/>')
										.html(
											item.cantidad + ' ' +
											'<span class="nombre-prod">' + item.nombre_prod + '</span> ' +
											'<span class="label label-info">' + item.pres_prod + '</span> ' +
											'<span class="label label-warning">' + item.CProducto.desc_c + '</span><br>' +
											'<i class="fa fa-comment"></i> <small class="text-navy"><em>' + item.comentario + '</em></small>'
										)
								)
							)
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<span/>')
										.html(horaPedido)
								)
							)
							.append(
                                    $('<div class="col-md-2" style="text-align: center;"/>')
                                      .append(
                $('<span class="temporizador"/>').html(formatTimeMO(tiempoRestante))
            )
                                )
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<div class="progress progress-striped active" style="margin-bottom: -20px;"/>')
										.append(
											$('<div style="width: 100%" aria-valuemax="50" aria-valuemin="0" role="progressbar" class="progress-bar progress-bar-'+mprobar+'"/>')
												.append(
													$('<span/>')
													.html(mnombar)
										)
									)
								)
							)
							.append(
								$('<div class="col-md-2"/>')
									.append(
										$('<span/>')
										.html(item.nombres+' '+item.ape_paterno)
								)
							)
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
											$('<a onclick="'+maccion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');"/>')
												.append(
												$('<button class="btn btn-outline btn-primary dim" type="button" style="margin-bottom: 0px !important;margin-top: -5px !important;"/>')
												.append(
													$('<i class="fa fa-check"/>')
										)
									)
								)
							)
						)
					);			
    		});
			  // Iniciar temporizador en tiempo real
            iniciarTemporizadoresMO();
        }
    });
}

var temporizadorIntervalMO = null;

function iniciarTemporizadoresMO(){
    if(temporizadorIntervalMO) return; // Si ya existe, no crear otro

    temporizadorIntervalMO = setInterval(function(){
        try {
            $('#list_pedidos_most li').each(function(){
                var timer = parseInt($(this).attr('data-timer'));

                if(isNaN(timer)){
                    throw new Error('El temporizador no es un número válido para el pedido.');
                }

                if(timer > 0){
                    timer--;
                    $(this).attr('data-timer', timer);
                    $(this).find('.temporizador').text(formatTimeMO(timer));
                } else {
                    $(this).find('.temporizador').text('00:00');

                    // Verificar si ya se mostró la alerta
                    if($(this).attr('data-alerta') !== "1"){
                        // Cambiar color de la fila a rojo
                        $(this).css('background-color', '#f2dede'); // rojo claro estilo bootstrap danger

                        // Lanzar alerta
						var nombre_prod = $(this).find('.nombre-prod').text();

                        var mesa = $(this).find('strong').text().split('\n')[0];
						//var nombre_prod = $(this).find('span').first().text();
					toastr.warning('¡' + nombre_prod + ' del pedido ' + mesa + ' está retrasado!');

                        // Marcar como alerta mostrada
                        $(this).attr('data-alerta', "1");

                        // --- INSERTAR EN tm_pedido_retraso ---
                        var id_pedido = $(this).data('id-pedido');   // agregar data-id-pedido al generar li
                        var id_pres = $(this).data('id-pres');       // agregar data-id-pres
						var id_detalle_pedido = $(this).data('id-detalle_pedido'); // agregar data-id-detalle_pedido
                        $.ajax({
                            type: "POST",
                            url: "?c=AreaProd&a=InsertarPedidoRetraso",
                            data: {
                                id_pedido: id_pedido,
                                id_pres: id_pres,
								id_detalle_pedido: id_detalle_pedido,
								estado: 2  // <- indicador de la función

                            },
                            success: function(res){
                                console.log("Pedido retrasado insertado correctamente:", res);
                            },
                            error: function(err){
                                console.error("Error al insertar pedido retrasado:", err);
                            }
                        });
                    }
                }
            });
        } catch(err) {
            console.error('Error en el temporizador:', err);
            toastr.error('Error en el temporizador: ' + err.message);
        }
    }, 1000);
}


function formatTimeMO(segundos){
    var min = Math.floor(segundos / 60);
    var sec = segundos % 60;
    return (min < 10 ? '0'+min : min) + ':' + (sec < 10 ? '0'+sec : sec);
}
/* Mostrar todos los pedidos realizados en el mostrador o para llevar */
var contDe = 0;
var nropedidosDelivery = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarDE',
        success: function (data){
	        $.each(data, function(i, item) {
				var nroPedDe = parseInt(item.Total.nro_p);
				$('#cant_pedidos_del').text(nroPedDe);
	    		if(parseInt(nroPedDe) !== contDe){
	    			contDe = 0;
	    			pedidosDelivery();
	    			var sound = new buzz.sound("assets/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contDe = nroPedDe + contDe;
	    		}
	    		console.log('contDe = '+contDe+' <> NroPedDe = '+nroPedDe);
			})
		}
	})
}

var pedidosDelivery = function(){
	moment.locale('es');
	$('#list_pedidos_del').empty();
	$.ajax({     
        type: "post",
        dataType: "json",
        url: '?c=AreaProd&a=ListarDE',
        success: function (data){
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		$('#cant_pedidos_del').text(item.Total.nro_p);
    		if (item.id_tipo == 2){
	    			mprobar = 'primary';
	    			mnombar = 'En espera';
	    			maccion = 'despDe';
    		} else if(item.id_tipo == 1){
    				if(item.estado == 'a'){
	    			mprobar = 'primary';
	    			mnombar = 'En espera';
	    			maccion = 'prepDe';
	    		} else if(item.estado == 'p'){
	    			mprobar = 'warning';
	    			mnombar = 'En preparacion';
	    			maccion = 'despDe';
	    		}
    		}


// Convertir tiempo estándar (decimal) a segundos para el temporizador
// Ejemplo: item.tiempostandar = 1.5 significa 1 minuto y 30 segundos
var minutos = Math.floor(item.tiempostandar);                     // parte entera -> minutos
var segundos = Math.round((item.tiempostandar - minutos) * 60);   // parte decimal -> segundos

// Multiplicar por la cantidad
var tiempoTotal = (minutos * 60 + segundos) * item.cantidad;      // total en segundos

// Obtener la fecha de pedido como objeto Date
var fechaPedido = new Date(item.fecha_pedido);

// Obtener diferencia en segundos entre ahora y la fecha de pedido
var ahora = new Date();
var segundosTranscurridos = Math.floor((ahora - fechaPedido) / 1000);

// Calcular tiempo restante
var tiempoRestante = tiempoTotal - segundosTranscurridos;
if(tiempoRestante < 0) tiempoRestante = 0; // si ya pasó, mostrar 0


    		$('#list_pedidos_del')
				.append(
					$('<li class="success-element limost"/>')
			 .attr('data-timer', tiempoRestante)  // <- aquí guardamos los segundos
 .attr('data-id-pedido', item.id_pedido)
    .attr('data-id-pres', item.id_pres)
	    .attr('data-id-detalle_pedido', item.id_detalle_pedido)			
					
					.append(
						$('<div class="row"/>')
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
										$('<strong/>')
										.html('<i class="fa fa-slack"></i> '+item.nro_pedido)
								)
							)
						.append(
								$('<div class="col-md-2"/>')
									.append(
									$('<span/>')
										.html(
											item.cantidad + ' ' +
											'<span class="nombre-prod">' + item.nombre_prod + '</span> ' +
											'<span class="label label-info">' + item.pres_prod + '</span> ' +
											'<span class="label label-warning">' + item.CProducto.desc_c + '</span><br>' +
											'<i class="fa fa-comment"></i> <small class="text-navy"><em>' + item.comentario + '</em></small>'
										)
								)
							)
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<span/>')
										.html(horaPedido)
								)
							)

							.append(
                                    $('<div class="col-md-2" style="text-align: center;"/>')
                                      .append(
                $('<span class="temporizador"/>').html(formatTimeDE(tiempoRestante))
            )
                                )	
							.append(
								$('<div class="col-md-2" style="text-align: center;"/>')
									.append(
										$('<div class="progress progress-striped active" style="margin-bottom: -20px;"/>')
										.append(
											$('<div style="width: 100%" aria-valuemax="50" aria-valuemin="0" role="progressbar" class="progress-bar progress-bar-'+mprobar+'"/>')
												.append(
													$('<span/>')
													.html(mnombar)
										)
									)
								)
							)
							.append(
								$('<div class="col-md-2"/>')
									.append(
										$('<span/>')
										.html(item.nombres+' '+item.ape_paterno)
								)
							)
							.append(
								$('<div class="col-md-1" style="text-align: center;"/>')
									.append(
											$('<a onclick="'+maccion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');"/>')
												.append(
												$('<button class="btn btn-outline btn-primary dim" type="button" style="margin-bottom: 0px !important;margin-top: -5px !important;"/>')
												.append(
													$('<i class="fa fa-check"/>')
										)
									)
								)
							)
						)
					);			
    		});
			  // Iniciar temporizador en tiempo real
            iniciarTemporizadoresDE();
        }
    });
}


var temporizadorIntervalDE = null;

function iniciarTemporizadoresDE(){
    if(temporizadorIntervalDE) return; // Si ya existe, no crear otro

    temporizadorIntervalDE = setInterval(function(){
        try {
            $('#list_pedidos_del li').each(function(){
                var timer = parseInt($(this).attr('data-timer'));

                if(isNaN(timer)){
                    throw new Error('El temporizador no es un número válido para el pedido.');
                }

                if(timer > 0){
                    timer--;
                    $(this).attr('data-timer', timer);
                    $(this).find('.temporizador').text(formatTimeMO(timer));
                } else {
                    $(this).find('.temporizador').text('00:00');

                    // Verificar si ya se mostró la alerta
                    if($(this).attr('data-alerta') !== "1"){
                        // Cambiar color de la fila a rojo
                        $(this).css('background-color', '#f2dede'); // rojo claro estilo bootstrap danger

                        // Lanzar alerta
						var nombre_prod = $(this).find('.nombre-prod').text();

                        var mesa = $(this).find('strong').text().split('\n')[0];
						//var nombre_prod = $(this).find('span').first().text();
					toastr.warning('¡' + nombre_prod + ' del pedido ' + mesa + ' está retrasado!');

                        // Marcar como alerta mostrada
                        $(this).attr('data-alerta', "1");

                        // --- INSERTAR EN tm_pedido_retraso ---
                        var id_pedido = $(this).data('id-pedido');   // agregar data-id-pedido al generar li
                        var id_pres = $(this).data('id-pres');       // agregar data-id-pres
						var id_detalle_pedido = $(this).data('id-detalle_pedido'); // agregar data-id-detalle_pedido
                        $.ajax({
                            type: "POST",
                            url: "?c=AreaProd&a=InsertarPedidoRetraso",
                            data: {
                                id_pedido: id_pedido,
                                id_pres: id_pres,
								id_detalle_pedido: id_detalle_pedido,
								estado: 3  // <- indicador de la función

                            },
                            success: function(res){
                                console.log("Pedido retrasado insertado correctamente:", res);
                            },
                            error: function(err){
                                console.error("Error al insertar pedido retrasado:", err);
                            }
                        });
                    }
                }
            });
        } catch(err) {
            console.error('Error en el temporizador:', err);
            toastr.error('Error en el temporizador: ' + err.message);
        }
    }, 1000);
}

function formatTimeDE(segundos){
    var min = Math.floor(segundos / 60);
    var sec = segundos % 60;
    return (min < 10 ? '0'+min : min) + ':' + (sec < 10 ? '0'+sec : sec);
}

$('#tab1').on('click', function() { 
	nropedidosMesa();
});

$('#tab2').on('click', function() {
	nropedidosMostrador();
});

$('#tab3').on('click', function() { 
	nropedidosDelivery();
});

var prepMe = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Preparacion',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	$('#cant_pedidos_mesa').text('');
      	nropedidosMesa();
      	pedidosMesa();
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}

var prepMo = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Preparacion',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	nropedidosMostrador();
      	pedidosMostrador();
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}

var prepDe = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Preparacion',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	nropedidosDelivery();
      	pedidosDelivery();
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}

var despMe = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Atendido',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	nropedidosMesa();
      	pedidosMesa();
      	$('#cant_pedidos_mesa').text('');
		contMe = contMe - 1;
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}

var despMo = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Atendido',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	nropedidosMostrador();
      	pedidosMostrador();
      	$('#cant_pedidos_most').text('');
		contMo = contMo - 1;
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}

var despDe = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: '?c=AreaProd&a=Atendido',
      data: {
      	cod_ped: cod_ped,
      	cod_prod: cod_prod,
      	fecha_p: fecha_p
      },
      success: function (datos) {
      	nropedidosDelivery();
      	pedidosDelivery();
      	$('#cant_pedidos_del').text('');
		contDe = contDe - 1;
      },
      error: function(jqXHR, textStatus, errorThrown){
          console.log(errorThrown + ' ' + textStatus);
      }   
  });
}


