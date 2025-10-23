<?php
// view/informes/ventas/rpapedido.php
// 🤖 RPA: Ajusta automáticamente los tiempos estándar de preparación 
// de productos con retrasos del día actual

require_once __DIR__ . '/../model/informes/ventas/inf_pedidoretraso.model.php';

date_default_timezone_set('America/Lima');

try {
    // ✅ 1. Incluir modelo
    $model = new InformeModel();

    // ✅ 2. Parámetros del proceso
    $minVeces = 6;        // Cantidad mínima de retrasos para considerar un producto frecuente
    $incremento = 2;      // Minutos que se aumentarán al tiempo estándar

    // ✅ 3. Obtener productos del día actual con más de $minVeces retrasos
    $productosFrecuentes = $model->ProductosFrecuentesRetraso($minVeces);

    if (empty($productosFrecuentes)) {
        echo "ℹ No se encontraron productos con más de $minVeces retrasos hoy (" . date('Y-m-d') . ").\n";
        exit;
    }

    // ✅ 4. Extraer los IDs (id_pres) de los productos encontrados
    $idsPres = array_map(function($p) {
        return $p->id_pres;
    }, $productosFrecuentes);

    // ✅ 5. Actualizar tiempos estándar (+$incremento)
    $resultado = $model->ActualizarTiempoEstandar($idsPres, $incremento);

    // ✅ 6. Registrar resultado en un archivo log
    $logPath = __DIR__ . '/../../../reportes/log_rpapedido.txt';
    if (!file_exists(dirname($logPath))) {
        mkdir(dirname($logPath), 0777, true);
    }

    $fecha = date('Y-m-d H:i:s');
    $mensaje = "[$fecha] ";

    if ($resultado) {
        $listaProductos = implode(', ', $idsPres);
        $mensaje .= "✔ Tiempos actualizados para productos frecuentes de hoy (id_pres): $listaProductos | +{$incremento} min\n";
        echo "✅ RPA ejecutado correctamente. Tiempos actualizados.\n";
    } else {
        $mensaje .= "⚠ No se pudieron actualizar los tiempos.\n";
        echo "⚠ Error al actualizar los tiempos estándar.\n";
    }

    file_put_contents($logPath, $mensaje, FILE_APPEND);
    echo "🗒 Log registrado en: log_rpapedido.txt\n";

} catch (Exception $e) {
    echo "❌ Error en el proceso RPA: " . $e->getMessage();
}
