<?php
session_start();

// ========== CONFIGURACIÓN DE ZONA HORARIA LOCAL (HONDURAS) ==========
date_default_timezone_set('America/Tegucigalpa');

// ========== CONTROL DE NUEVA VENTA (LIMPIEZA AUTOMÁTICA) ==========
if (isset($_GET['nueva_venta'])) {
    unset($_SESSION['PRODUCTOS']);
    unset($_SESSION['DETALLE_RESPALDO']);
    unset($_SESSION['DNI_RESPALDO']);
    unset($_SESSION['NOMBRE_RESPALDO']);
    unset($_SESSION['EMAIL_RESPALDO']);
    unset($_SESSION['CELULAR_RESPALDO']);
    $_SESSION['DNI'] = '';
    $_SESSION['NOMBRE'] = '';
    $_SESSION['EMAIL'] = '';
    $_SESSION['CELULAR'] = '';

    // Redirección limpia para eliminar el parámetro de la URL
    header("Location: INDEX.PHP");
    exit();
}

if (!isset($_SESSION['PRODUCTOS'])) {
    $_SESSION['PRODUCTOS'] = array();
}

// ========== AGREGAR PRODUCTO ==========
if (isset($_POST['AGREGAR'])) {
    $PRODUCTO = $_POST['PRODUCTO'];
    $PRECIO = $_POST['PRECIO'];
    $CANTIDAD = $_POST['CANTIDAD'];
    $SUBTOTAL = $PRECIO * $CANTIDAD;
    $DESCUENTO = 0;

    if ($CANTIDAD >= 5) {
        $DESCUENTO = $SUBTOTAL * 0.10;
    }

    $_SESSION['DNI'] = $_POST['DNI'];
    $_SESSION['NOMBRE'] = $_POST['NOMBRE'];
    $_SESSION['EMAIL'] = $_POST['EMAIL'];
    $_SESSION['CELULAR'] = $_POST['CELULAR'];

    $_SESSION['PRODUCTOS'][] = array(
        'PRODUCTO' => $PRODUCTO,
        'PRECIO' => $PRECIO,
        'CANTIDAD' => $CANTIDAD,
        'SUBTOTAL' => $SUBTOTAL,
        'DESCUENTO' => $DESCUENTO
    );

    // Redirección limpia tras agregar para evitar reenvíos de formulario
    header("Location: INDEX.PHP");
    exit();
}

// ========== LIMPIAR PRODUCTOS DESDE EL INDEX ==========
if (isset($_POST['LIMPIAR_INDEX'])) {
    $_SESSION['PRODUCTOS'] = array();
    $_SESSION['DNI'] = '';
    $_SESSION['NOMBRE'] = '';
    $_SESSION['EMAIL'] = '';
    $_SESSION['CELULAR'] = '';
    
    header("Location: INDEX.PHP");
    exit();
}

$DNI_VAL = isset($_SESSION['DNI']) ? $_SESSION['DNI'] : '';
$NOMBRE_VAL = isset($_SESSION['NOMBRE']) ? $_SESSION['NOMBRE'] : '';
$EMAIL_VAL = isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : '';
$CELULAR_VAL = isset($_SESSION['CELULAR']) ? $_SESSION['CELULAR'] : '';

// Calcular totales del carrito
$TOTAL_PRODUCTOS = 0;
$SUBTOTAL_GENERAL = 0;
$TOTAL_DESCUENTO = 0;
foreach ($_SESSION['PRODUCTOS'] as $PROD) {
    $TOTAL_PRODUCTOS += $PROD['CANTIDAD'];
    $SUBTOTAL_GENERAL += $PROD['SUBTOTAL'];
    $TOTAL_DESCUENTO += $PROD['DESCUENTO'];
}
$IMPUESTO = ($SUBTOTAL_GENERAL - $TOTAL_DESCUENTO) * 0.15;
$TOTAL_PAGO = ($SUBTOTAL_GENERAL - $TOTAL_DESCUENTO) + $IMPUESTO;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Tech | Sistema de Ventas</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos especificos de INDEX */
        .carousel-section {
            margin-bottom: 32px;
        }

        .carousel-container {
            width: 100%;
            max-width: 900px;
            height: 380px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 16px;
            position: relative;
            border: 1px solid var(--glass-border);
        }

        .carousel-track {
            display: flex;
            width: 400%;
            height: 380px;
            animation: slideCarousel 16s infinite ease-in-out;
        }

        .carousel-item {
            width: 25%;
            height: 380px;
            position: relative;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: saturate(0.9) contrast(1.05);
        }

        .carousel-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 180px;
            background: linear-gradient(to top, rgba(10, 14, 18, 0.96), transparent);
        }

        .carousel-caption {
            position: absolute;
            bottom: 30px;
            left: 35px;
            color: white;
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
            z-index: 2;
            letter-spacing: -0.3px;
        }

        .carousel-caption span {
            display: block;
            font-family: var(--font-mono);
            font-size: 12px;
            font-weight: 500;
            color: var(--primary-light);
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        @keyframes slideCarousel {
            0%, 20% { transform: translateX(0); }
            25%, 45% { transform: translateX(-25%); }
            50%, 70% { transform: translateX(-50%); }
            75%, 95% { transform: translateX(-75%); }
            100% { transform: translateX(0); }
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-bottom: 32px;
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

    <div class="header animate-in">
        <div class="header-left">
            <div class="logo-icon">
                <i class="fas fa-terminal"></i>
            </div>
            <div class="header-text">
                <h1>Sistema de Ventas</h1>
                <p>&gt; Tienda Tech — Punto de Venta</p>
            </div>
        </div>
    </div>

    <div class="carousel-section animate-in delay-1">
        <div class="carousel-container">
            <div class="carousel-track">
                <div class="carousel-item">
                    <img src="IMAGENES/RATON.jpg" alt="Mouse Gaming">
                    <div class="carousel-caption">Mouse Gamer Pro<span>Precision Extrema</span></div>
                </div>
                <div class="carousel-item">
                    <img src="IMAGENES/MONITOR LED.jpg" alt="Monitor LED">
                    <div class="carousel-caption">Monitor LED 4K<span>Calidad Ultra HD</span></div>
                </div>
                <div class="carousel-item">
                    <img src="IMAGENES/FUENTE DE PODER.jpg" alt="Fuente de Poder">
                    <div class="carousel-caption">Fuente de Poder 750W<span>Potencia Total</span></div>
                </div>
                <div class="carousel-item">
                    <img src="IMAGENES/TECLADO.jpg" alt="Teclado Mecanico">
                    <div class="carousel-caption">Teclado Mecanico RGB<span>Estilo Unico</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-grid">

        <div class="card animate-in delay-2">
            <div class="card-title">
                <div class="card-title-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                Nueva Venta
            </div>

            <form action="INDEX.PHP" method="POST">

                <div class="section-label">
                    <i class="fas fa-user"></i> Datos del Cliente
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> DNI</label>
                        <input type="text" name="DNI" value="<?php echo $DNI_VAL; ?>" required placeholder="Ingrese DNI">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombre Completo</label>
                        <input type="text" name="NOMBRE" value="<?php echo $NOMBRE_VAL; ?>" required placeholder="Nombre del cliente">
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr;">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> E-Mail</label>
                        <input type="email" name="EMAIL" value="<?php echo $EMAIL_VAL; ?>" required placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr;">
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Celular</label>
                        <input type="text" name="CELULAR" maxlength="8" value="<?php echo $CELULAR_VAL; ?>" required placeholder="0000-0000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha</label>
                        <input type="text" name="FECHA" value="<?php echo date('d/m/Y'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Hora</label>
                        <input type="text" name="HORA" value="<?php echo date('H:i'); ?>" readonly>
                    </div>
                </div>

                <div class="section-divider">
                    <div class="section-label">
                        <i class="fas fa-shopping-cart"></i> Datos de la Compra
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-laptop"></i> Producto</label>
                        <select name="PRODUCTO" required>
                            <option value="">Seleccione un producto...</option>
                            <option value="Mouse Optico">Mouse Optico</option>
                            <option value="Teclado USB">Teclado USB</option>
                            <option value="Monitor LED">Monitor LED</option>
                            <option value="Disco Duro 1TB">Disco Duro 1TB</option>
                            <option value="Memoria RAM 8GB">Memoria RAM 8GB</option>
                            <option value="Tarjeta Madre">Tarjeta Madre</option>
                            <option value="Procesador Intel">Procesador Intel</option>
                            <option value="Fuente de Poder">Fuente de Poder</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Precio (L.)</label>
                            <input type="number" name="PRECIO" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-sort-numeric-up"></i> Cantidad</label>
                            <input type="number" name="CANTIDAD" min="1" required placeholder="1">
                        </div>
                    </div>
                </div>

                <button type="submit" name="AGREGAR" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    Agregar al Carrito
                </button>

            </form>
        </div>

        <div class="card receipt-card animate-in delay-3">
            <div class="card-title">
                <div class="card-title-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                Carrito de Compras
                <?php if(count($_SESSION['PRODUCTOS']) > 0) { ?>
                    <span class="cart-badge">
                        <i class="fas fa-box"></i>
                        <?php echo count($_SESSION['PRODUCTOS']); ?> items
                    </span>
                <?php } ?>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cant.</th>
                            <th>Subtotal</th>
                            <th>Desc.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($_SESSION['PRODUCTOS']) > 0) {
                            foreach ($_SESSION['PRODUCTOS'] as $PROD) {
                                $icono = 'fa-microchip';
                                $producto_lower = strtolower($PROD['PRODUCTO']);
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
                            <td>
                                <div class="product-name">
                                    <div class="product-icon"><i class="fas <?php echo $icono; ?>"></i></div>
                                    <?php echo $PROD['PRODUCTO']; ?>
                                </div>
                            </td>
                            <td class="price-cell">L. <?php echo number_format($PROD['PRECIO'], 2); ?></td>
                            <td><span class="qty-badge"><?php echo $PROD['CANTIDAD']; ?></span></td>
                            <td class="subtotal-cell">L. <?php echo number_format($PROD['SUBTOTAL'], 2); ?></td>
                            <td class="discount-cell">
                                <?php echo $PROD['DESCUENTO'] > 0 ? 'L. '.number_format($PROD['DESCUENTO'], 2) : '-'; ?>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-cart">
                                    <i class="fas fa-shopping-basket"></i>
                                    <p>No hay productos en el carrito</p>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="receipt-barcode"></div>

            <div class="totals-section">
                <div class="totals-row">
                    <span><i class="fas fa-boxes"></i> Cantidad de Productos</span>
                    <span><?php echo $TOTAL_PRODUCTOS; ?></span>
                </div>
                <div class="totals-row">
                    <span><i class="fas fa-percentage"></i> Impuesto (15%)</span>
                    <span>L. <?php echo number_format($IMPUESTO, 2); ?></span>
                </div>
                <div class="totals-row">
                    <span><i class="fas fa-tags"></i> Descuento Total</span>
                    <span>L. <?php echo number_format($TOTAL_DESCUENTO, 2); ?></span>
                </div>
                <div class="totals-row total-final">
                    <span><i class="fas fa-coins"></i> TOTAL A PAGAR</span>
                    <span>L. <?php echo number_format($TOTAL_PAGO, 2); ?></span>
                </div>
            </div>

            <div class="action-buttons">
                <form action="GUARDAR.PHP" method="POST" style="display: inline-block; width: 48%;">
                    <input type="hidden" name="DNI" value="<?php echo $DNI_VAL; ?>">
                    <input type="hidden" name="NOMBRE" value="<?php echo $NOMBRE_VAL; ?>">
                    <input type="hidden" name="EMAIL" value="<?php echo $EMAIL_VAL; ?>">
                    <input type="hidden" name="CELULAR" value="<?php echo $CELULAR_VAL; ?>">
                    <button type="submit" name="ENVIAR" class="btn-send" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Enviar Venta
                    </button>
                </form>

                <form action="INDEX.PHP" method="POST" style="display: inline-block; width: 48%;">
                    <button type="submit" name="LIMPIAR_INDEX" class="btn-clear" style="width: 100%;">
                        <i class="fas fa-trash-alt"></i> Limpiar
                    </button>
                </form>
            </div>

        </div>

    </div>

    <div class="footer">
        <p><i class="fas fa-microchip"></i> Sistema de Ventas Tech &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
    </div>

</div>

</body>
</html>