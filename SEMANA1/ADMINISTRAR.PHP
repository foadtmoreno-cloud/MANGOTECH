<?php
session_start();
include("CONEXION.PHP");

// ========== ELIMINAR VENTA POR ID ==========
if (isset($_POST['ELIMINAR_VENTA']) && isset($_POST['ID_VENTA'])) {
    $ID = intval($_POST['ID_VENTA']);
    mysqli_query($CONEXION, "DELETE FROM VENTAS WHERE ID = $ID");
    header("Location: ADMINISTRAR.PHP?tab=ventas&msg=venta_eliminada");
    exit;
}

// ========== ELIMINAR CLIENTE Y SUS VENTAS ==========
if (isset($_POST['ELIMINAR_CLIENTE']) && isset($_POST['DNI_CLIENTE'])) {
    $DNI = mysqli_real_escape_string($CONEXION, $_POST['DNI_CLIENTE']);
    mysqli_query($CONEXION, "DELETE FROM VENTAS WHERE DNI_CLIENTE = '$DNI'");
    mysqli_query($CONEXION, "DELETE FROM CLIENTES WHERE DNI = '$DNI'");
    header("Location: ADMINISTRAR.PHP?tab=clientes&msg=cliente_eliminado");
    exit;
}

// ========== PARAMETROS DE BUSQUEDA Y FILTROS ==========
$BUSQUEDA = isset($_GET['busqueda']) ? mysqli_real_escape_string($CONEXION, $_GET['busqueda']) : '';
$FILTRO_PRODUCTO = isset($_GET['filtro_producto']) ? mysqli_real_escape_string($CONEXION, $_GET['filtro_producto']) : '';
$PRECIO_MIN = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? floatval($_GET['precio_min']) : '';
$PRECIO_MAX = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? floatval($_GET['precio_max']) : '';
$FECHA_DESDE = isset($_GET['fecha_desde']) ? mysqli_real_escape_string($CONEXION, $_GET['fecha_desde']) : '';
$FECHA_HASTA = isset($_GET['fecha_hasta']) ? mysqli_real_escape_string($CONEXION, $_GET['fecha_hasta']) : '';
$TAB = isset($_GET['tab']) ? $_GET['tab'] : 'ventas';

// ========== CONSTRUIR WHERE PARA VENTAS ==========
$WHERE_VENTAS = "WHERE 1=1";
if ($BUSQUEDA != '') {
    $WHERE_VENTAS .= " AND (C.DNI LIKE '%$BUSQUEDA%' OR C.NOMBRE LIKE '%$BUSQUEDA%' OR V.PRODUCTO LIKE '%$BUSQUEDA%')";
}
if ($FILTRO_PRODUCTO != '') {
    $WHERE_VENTAS .= " AND V.PRODUCTO = '$FILTRO_PRODUCTO'";
}
if ($PRECIO_MIN !== '') {
    $WHERE_VENTAS .= " AND V.PRECIO >= $PRECIO_MIN";
}
if ($PRECIO_MAX !== '') {
    $WHERE_VENTAS .= " AND V.PRECIO <= $PRECIO_MAX";
}
if ($FECHA_DESDE != '') {
    $WHERE_VENTAS .= " AND V.FECHA >= '$FECHA_DESDE'";
}
if ($FECHA_HASTA != '') {
    $WHERE_VENTAS .= " AND V.FECHA <= '$FECHA_HASTA'";
}

// ========== CONSULTAS ==========
$SQL_VENTAS = "SELECT V.ID, C.DNI, C.NOMBRE, V.PRODUCTO, V.PRECIO, V.CANTIDAD, V.SUBTOTAL, V.DESCUENTO, V.FECHA, V.HORA 
               FROM VENTAS V 
               INNER JOIN CLIENTES C ON V.DNI_CLIENTE = C.DNI 
               $WHERE_VENTAS 
               ORDER BY V.ID DESC";
$RESULTADO_VENTAS = mysqli_query($CONEXION, $SQL_VENTAS);

$SQL_CLIENTES = "SELECT C.DNI, C.NOMBRE, C.EMAIL, C.CELULAR, COUNT(V.ID) as TOTAL_COMPRAS, SUM(V.SUBTOTAL) as TOTAL_GASTADO 
                 FROM CLIENTES C 
                 LEFT JOIN VENTAS V ON C.DNI = V.DNI_CLIENTE 
                 GROUP BY C.DNI, C.NOMBRE, C.EMAIL, C.CELULAR 
                 ORDER BY TOTAL_GASTADO DESC";
$RESULTADO_CLIENTES = mysqli_query($CONEXION, $SQL_CLIENTES);

$SQL_PRODUCTOS = "SELECT DISTINCT PRODUCTO FROM VENTAS ORDER BY PRODUCTO";
$RESULTADO_PRODUCTOS = mysqli_query($CONEXION, $SQL_PRODUCTOS);

$SQL_STATS = "SELECT COUNT(*) as total, SUM(SUBTOTAL) as subtotal, SUM(DESCUENTO) as descuento, SUM(CANTIDAD) as cantidad 
              FROM VENTAS V 
              INNER JOIN CLIENTES C ON V.DNI_CLIENTE = C.DNI 
              $WHERE_VENTAS";
$RESULT_STATS = mysqli_query($CONEXION, $SQL_STATS);
$STATS = mysqli_fetch_assoc($RESULT_STATS);

mysqli_close($CONEXION);

// Helper para mantener parametros en URLs
$params = '';
if ($BUSQUEDA != '') $params .= '&busqueda=' . urlencode($BUSQUEDA);
if ($FILTRO_PRODUCTO != '') $params .= '&filtro_producto=' . urlencode($FILTRO_PRODUCTO);
if ($PRECIO_MIN !== '') $params .= '&precio_min=' . $PRECIO_MIN;
if ($PRECIO_MAX !== '') $params .= '&precio_max=' . $PRECIO_MAX;
if ($FECHA_DESDE != '') $params .= '&fecha_desde=' . $FECHA_DESDE;
if ($FECHA_HASTA != '') $params .= '&fecha_hasta=' . $FECHA_HASTA;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDE de Eliminacion, Busquedas y Filtros | Tienda Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos especificos de ADMINISTRAR - solo ajustes de color para tablas */
        .table-wrapper thead {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.2));
        }

        .table-wrapper tbody tr:hover {
            background: rgba(239, 68, 68, 0.05);
        }
    </style>
</head>
<body>

<div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>
<div class="noise"></div>

<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <i class="fas fa-tools"></i>
            <div>
                <h1>IDE de Eliminacion, Busquedas y Filtros</h1>
                <p>Panel de administracion de datos - Tienda Tech</p>
            </div>
        </div>
        <a href="INICIO.PHP" class="btn-back">
            <i class="fas fa-home"></i>
            Inicio
        </a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="ADMINISTRAR.PHP?tab=ventas<?php echo $params; ?>" class="tab <?php echo $TAB == 'ventas' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Ventas
        </a>
        <a href="ADMINISTRAR.PHP?tab=clientes<?php echo $BUSQUEDA ? '&busqueda='.urlencode($BUSQUEDA) : ''; ?>" class="tab <?php echo $TAB == 'clientes' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Clientes
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'venta_eliminada') { ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        La venta ha sido eliminada correctamente.
    </div>
    <?php } ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cliente_eliminado') { ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        El cliente y sus ventas asociadas han sido eliminados correctamente.
    </div>
    <?php } ?>

    <?php if ($TAB == 'ventas') { ?>

    <!-- Search & Filters -->
    <div class="card">
        <div class="search-section">
            <h3><i class="fas fa-filter"></i> Busquedas y Filtros de Ventas</h3>
            <form method="GET" action="ADMINISTRAR.PHP">
                <input type="hidden" name="tab" value="ventas">
                <div class="search-grid">
                    <input type="text" name="busqueda" placeholder="Buscar por DNI, Cliente o Producto..." value="<?php echo htmlspecialchars($BUSQUEDA); ?>">
                    <select name="filtro_producto">
                        <option value="">Todos los productos</option>
                        <?php while($PROD = mysqli_fetch_assoc($RESULTADO_PRODUCTOS)) { ?>
                        <option value="<?php echo $PROD['PRODUCTO']; ?>" <?php echo $FILTRO_PRODUCTO == $PROD['PRODUCTO'] ? 'selected' : ''; ?>><?php echo $PROD['PRODUCTO']; ?></option>
                        <?php } ?>
                    </select>
                    <input type="number" name="precio_min" step="0.01" placeholder="Precio minimo (L.)" value="<?php echo $PRECIO_MIN !== '' ? $PRECIO_MIN : ''; ?>">
                    <input type="number" name="precio_max" step="0.01" placeholder="Precio maximo (L.)" value="<?php echo $PRECIO_MAX !== '' ? $PRECIO_MAX : ''; ?>">
                    <input type="date" name="fecha_desde" value="<?php echo $FECHA_DESDE; ?>">
                    <input type="date" name="fecha_hasta" value="<?php echo $FECHA_HASTA; ?>">
                </div>
                <div class="search-actions">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i> Aplicar Filtros</button>
                    <a href="ADMINISTRAR.PHP?tab=ventas" class="btn-clear-search"><i class="fas fa-eraser"></i> Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stat-item">
                <h4><?php echo number_format($STATS['total'] ?: 0); ?></h4>
                <p>Registros Encontrados</p>
            </div>
            <div class="stat-item">
                <h4>L. <?php echo number_format($STATS['subtotal'] ?: 0, 2); ?></h4>
                <p>Subtotal</p>
            </div>
            <div class="stat-item">
                <h4>L. <?php echo number_format($STATS['descuento'] ?: 0, 2); ?></h4>
                <p>Descuentos</p>
            </div>
            <div class="stat-item">
                <h4><?php echo number_format($STATS['cantidad'] ?: 0); ?></h4>
                <p>Unidades</p>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI Cliente</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cant.</th>
                        <th>Subtotal</th>
                        <th>Desc.</th>
                        <th>Fecha / Hora</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($RESULTADO_VENTAS) > 0) {
                        while ($FILA = mysqli_fetch_assoc($RESULTADO_VENTAS)) {
                            $icono = 'fa-microchip';
                            $producto_lower = strtolower($FILA['PRODUCTO']);
                            if(strpos($producto_lower, 'mouse') !== false) $icono = 'fa-mouse';
                            elseif(strpos($producto_lower, 'teclado') !== false) $icono = 'fa-keyboard';
                            elseif(strpos($producto_lower, 'monitor') !== false) $icono = 'fa-desktop';
                            elseif(strpos($producto_lower, 'disco') !== false) $icono = 'fa-hdd';
                            elseif(strpos($producto_lower, 'ram') !== false) $icono = 'fa-memory';
                            elseif(strpos($producto_lower, 'fuente') !== false) $icono = 'fa-bolt';
                            elseif(strpos($producto_lower, 'procesador') !== false) $icono = 'fa-microchip';
                            elseif(strpos($producto_lower, 'tarjeta') !== false) $icono = 'fa-server';
                    ?>
                    <tr>
                        <td><span class="id-badge">#<?php echo $FILA['ID']; ?></span></td>
                        <td><span class="dni-badge"><?php echo $FILA['DNI']; ?></span></td>
                        <td><strong><?php echo $FILA['NOMBRE']; ?></strong></td>
                        <td><i class="fas <?php echo $icono; ?>" style="color:var(--primary-light);margin-right:8px;"></i><?php echo $FILA['PRODUCTO']; ?></td>
                        <td class="price-tag">L. <?php echo number_format($FILA['PRECIO'], 2); ?></td>
                        <td><?php echo $FILA['CANTIDAD']; ?></td>
                        <td class="price-tag">L. <?php echo number_format($FILA['SUBTOTAL'], 2); ?></td>
                        <td class="discount-cell"><?php echo $FILA['DESCUENTO'] > 0 ? 'L. '.number_format($FILA['DESCUENTO'], 2) : '-'; ?></td>
                        <td class="date-cell"><?php echo $FILA['FECHA']; ?><br><?php echo $FILA['HORA']; ?></td>
                        <td>
                            <form method="POST" action="ADMINISTRAR.PHP?tab=ventas<?php echo $params; ?>" onsubmit="return confirm('Esta seguro de eliminar esta venta? Esta accion no se puede deshacer.');">
                                <input type="hidden" name="ID_VENTA" value="<?php echo $FILA['ID']; ?>">
                                <button type="submit" name="ELIMINAR_VENTA" class="btn-delete">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="10">
                            <div class="no-data">
                                <i class="fas fa-inbox"></i>
                                <h3>No se encontraron ventas</h3>
                                <p>Intenta con otros criterios de busqueda o filtros</p>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php } else { ?>

    <!-- CLIENTES TAB -->
    <div class="card">
        <div class="search-section">
            <h3><i class="fas fa-search"></i> Buscar Clientes</h3>
            <form method="GET" action="ADMINISTRAR.PHP">
                <input type="hidden" name="tab" value="clientes">
                <div class="search-grid" style="grid-template-columns: 1fr auto;">
                    <input type="text" name="busqueda" placeholder="Buscar por DNI o Nombre del cliente..." value="<?php echo htmlspecialchars($BUSQUEDA); ?>">
                    <div class="search-actions">
                        <button type="submit" class="btn-search"><i class="fas fa-search"></i> Buscar</button>
                        <a href="ADMINISTRAR.PHP?tab=clientes" class="btn-clear-search"><i class="fas fa-eraser"></i> Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Total Compras</th>
                        <th>Total Gastado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hay_clientes = false;
                    while ($FILA = mysqli_fetch_assoc($RESULTADO_CLIENTES)) {
                        if ($BUSQUEDA != '' && strpos(strtolower($FILA['DNI']), strtolower($BUSQUEDA)) === false && strpos(strtolower($FILA['NOMBRE']), strtolower($BUSQUEDA)) === false) {
                            continue;
                        }
                        $hay_clientes = true;
                    ?>
                    <tr>
                        <td><span class="dni-badge"><?php echo $FILA['DNI']; ?></span></td>
                        <td><strong><?php echo $FILA['NOMBRE']; ?></strong></td>
                        <td><?php echo $FILA['EMAIL']; ?></td>
                        <td><?php echo $FILA['CELULAR']; ?></td>
                        <td><?php echo $FILA['TOTAL_COMPRAS']; ?></td>
                        <td class="price-tag">L. <?php echo number_format($FILA['TOTAL_GASTADO'] ?: 0, 2); ?></td>
                        <td>
                            <form method="POST" action="ADMINISTRAR.PHP?tab=clientes<?php echo $BUSQUEDA ? '&busqueda='.urlencode($BUSQUEDA) : ''; ?>" onsubmit="return confirm('Esta seguro de eliminar este cliente? Se eliminaran TODAS sus ventas asociadas. Esta accion no se puede deshacer.');">
                                <input type="hidden" name="DNI_CLIENTE" value="<?php echo $FILA['DNI']; ?>">
                                <button type="submit" name="ELIMINAR_CLIENTE" class="btn-delete">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (!$hay_clientes) { ?>
                    <tr>
                        <td colspan="7">
                            <div class="no-data">
                                <i class="fas fa-inbox"></i>
                                <h3>No se encontraron clientes</h3>
                                <p>Intenta con otros criterios de busqueda</p>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php } ?>

    <div class="footer">
        <p><i class="fas fa-code"></i> Sistema de Ventas Tech &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
    </div>

</div>

</body>
</html>