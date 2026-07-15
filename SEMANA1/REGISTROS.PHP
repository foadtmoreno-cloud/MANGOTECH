<?php
include("CONEXION.PHP");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Ventas | Tienda Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
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
            <i class="fas fa-store"></i>
            <div>
                <h1>Registros de Ventas</h1>
                <p>Panel de control de transacciones</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="INICIO.PHP" class="btn-header btn-header-primary">
                <i class="fas fa-home"></i>
                Inicio
            </a>
        </div>
    </div>

    <?php
    // Calcular estadisticas
    $SQL_STATS = "SELECT 
        COUNT(*) as total_ventas,
        SUM(V.SUBTOTAL) as total_subtotal,
        SUM(V.DESCUENTO) as total_descuento,
        SUM(V.CANTIDAD) as total_productos
        FROM VENTAS V";
    $RESULT_STATS = mysqli_query($CONEXION, $SQL_STATS);
    $STATS = mysqli_fetch_assoc($RESULT_STATS);

    $total_ventas = $STATS['total_ventas'] ?: 0;
    $total_subtotal = $STATS['total_subtotal'] ?: 0;
    $total_descuento = $STATS['total_descuento'] ?: 0;
    $total_productos = $STATS['total_productos'] ?: 0;
    $total_neto = $total_subtotal - $total_descuento;
    ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <h3><?php echo number_format($total_ventas); ?></h3>
            <p>Total de Ventas</p>
        </div>
        <div class="stat-card green">
            <div class="icon"><i class="fas fa-box"></i></div>
            <h3><?php echo number_format($total_productos); ?></h3>
            <p>Productos Vendidos</p>
        </div>
        <div class="stat-card orange">
            <div class="icon"><i class="fas fa-tags"></i></div>
            <h3>L. <?php echo number_format($total_descuento, 2); ?></h3>
            <p>Descuentos Aplicados</p>
        </div>
        <div class="stat-card purple">
            <div class="icon"><i class="fas fa-coins"></i></div>
            <h3>L. <?php echo number_format($total_neto, 2); ?></h3>
            <p>Ingresos Netos</p>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-section">
        <div class="table-header">
            <h2><i class="fas fa-list-alt"></i> Historial de Transacciones</h2>
            <span class="badge"><i class="fas fa-database"></i> <?php echo $total_ventas; ?> registros</span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-id-card"></i> DNI Cliente</th>
                        <th><i class="fas fa-user"></i> Cliente</th>
                        <th><i class="fas fa-laptop"></i> Producto</th>
                        <th><i class="fas fa-tag"></i> Precio Unit.</th>
                        <th><i class="fas fa-sort-numeric-up"></i> Cantidad</th>
                        <th><i class="fas fa-calculator"></i> Subtotal</th>
                        <th><i class="fas fa-percentage"></i> Descuento</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $SQL = "SELECT C.DNI, C.NOMBRE, V.PRODUCTO, V.PRECIO, V.CANTIDAD, V.SUBTOTAL, V.DESCUENTO 
                            FROM CLIENTES C 
                            INNER JOIN VENTAS V ON C.DNI = V.DNI_CLIENTE 
                            ORDER BY V.ID DESC";

                    $RESULTADO = mysqli_query($CONEXION, $SQL);

                    if (mysqli_num_rows($RESULTADO) > 0) {
                        while ($FILA = mysqli_fetch_assoc($RESULTADO)) {
                            $nombres = explode(' ', $FILA['NOMBRE']);
                            $iniciales = '';
                            foreach($nombres as $n) {
                                if(strlen($n) > 0) $iniciales .= strtoupper(substr($n, 0, 1));
                                if(strlen($iniciales) >= 2) break;
                            }

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
                        <td><span class="dni-badge"><?php echo $FILA['DNI']; ?></span></td>
                        <td>
                            <div class="name-cell">
                                <div class="avatar"><?php echo $iniciales; ?></div>
                                <?php echo $FILA['NOMBRE']; ?>
                            </div>
                        </td>
                        <td>
                            <div class="product-cell">
                                <div class="product-icon"><i class="fas <?php echo $icono; ?>"></i></div>
                                <?php echo $FILA['PRODUCTO']; ?>
                            </div>
                        </td>
                        <td><span class="price-tag">L. <?php echo number_format($FILA['PRECIO'], 2); ?></span></td>
                        <td><span class="qty-badge"><?php echo $FILA['CANTIDAD']; ?> unid.</span></td>
                        <td><span class="subtotal-cell">L. <?php echo number_format($FILA['SUBTOTAL'], 2); ?></span></td>
                        <td>
                            <?php if($FILA['DESCUENTO'] > 0) { ?>
                                <span class="discount-cell"><i class="fas fa-arrow-down"></i> L. <?php echo number_format($FILA['DESCUENTO'], 2); ?></span>
                            <?php } else { ?>
                                <span style="color: var(--gray);">-</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="7">
                            <div class="no-data">
                                <i class="fas fa-inbox"></i>
                                <h3>No hay registros de ventas</h3>
                                <p>Aun no se han realizado transacciones en el sistema</p>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    mysqli_close($CONEXION);
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p><i class="fas fa-code"></i> Sistema de Ventas Tech &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
    </div>

</div>

</body>
</html>