<?php
// index.php — Carga productos de items.json y los clasifica para los modales
try {
    // Leer el JSON de productos
    $json = file_get_contents(__DIR__ . '/items.json');
    if ($json === false) {
        throw new Exception('No se pudo leer items.json');
    }
    $items = json_decode($json, true);
    if (!is_array($items)) {
        throw new Exception('items.json no es un array válido');
    }

    // Clasificar productos en categorías
    $categoryProducts = [
        'main'     => [],
        'entrada'  => [],
        'ensalada' => [],
        'sopa'     => []
    ];
    foreach ($items as $p) {
        $name = $p['name'] ?? '';
        if (stripos($name, 'Entrada ') === 0) {
            $categoryProducts['entrada'][]  = $p;
        } elseif (stripos($name, 'Ensalada ') === 0) {
            $categoryProducts['ensalada'][] = $p;
        } elseif (stripos($name, 'Solo Sopa ') === 0) {
            $categoryProducts['sopa'][]     = $p;
        } else {
            $categoryProducts['main'][]     = $p;
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Las delicias de Elida – Restaurante</title>
    <link rel="shortcut icon" href="icono.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="site-header">
        <h1>Las delicias de Elida</h1>
        <nav>
            <a href="#" id="btn-dia">Del día</a>
            <a href="#" id="btn-especial">Especiales</a>
            <a href="#" id="btn-sopa">Sopas</a>
            <a href="#" id="btn-todos">Todos</a>
        </nav>
    </header>

    <!-- Modal “¿Solo bandeja?” -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h3>¿Deseas solo Bandeja?</h3>
            <p>Si eliges esta opción, el precio se reducirá en 2000.</p>
            <div class="modal-buttons">
                <button id="yesButton" class="btn btn-primary">Sí</button>
                <button id="noButton" class="btn btn-secondary">No</button>
            </div>
        </div>
    </div>

    <!-- Modal Sopas -->
    <div id="sopaOptionsModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('sopaOptionsModal')">&times;</span>
            <h2>Elige tu Sopa</h2>
            <div id="sopaOptionsContainer" class="options-container"></div>
        </div>
    </div>

    <!-- Modal Entradas -->
    <div id="entradaOptionsModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('entradaOptionsModal')">&times;</span>
            <h2>Elige tu Entrada</h2>
            <div id="entradaOptionsContainer" class="options-container"></div>
        </div>
    </div>

    <!-- Modal Ensaladas -->
    <div id="ensaladaOptionsModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('ensaladaOptionsModal')">&times;</span>
            <h2>Elige tu Ensalada</h2>
            <div id="ensaladaOptionsContainer" class="options-container"></div>
        </div>
    </div>

    <!-- Carrito flotante (móvil) -->
    <div id="floating-cart" class="floating-cart d-md-none">
        <i class="fab fa-whatsapp"></i>
        Completar <span id="cart-count">0</span>
    </div>

    <!-- Contenedor principal -->
    <div class="main-container">
        <div class="row no-gutters">
            <div class="col-md-8">
                <div class="products-container">
                    <div class="products-grid"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cart-container">
                    <div class="cart-header">
                        <h4>Pedido</h4>
                        <span class="close-cart d-md-none">×</span>
                    </div>
                    <ul class="cart-list list-group"></ul>
                    <div class="check-out">
                        Total: $<span class="total">0</span>
                    </div>
                    <div class="buttons">
                        <button onclick="clearCart()" class="btn btn-danger">Vaciar</button>
                        <button onclick="sendWhatsApp()" class="btn btn-success">Ordenar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inyectar datos al front-end -->
    <script>
        window.productsFromPHP = <?= json_encode($categoryProducts['main']);     ?>;
        window.entradaFromPHP = <?= json_encode($categoryProducts['entrada']);  ?>;
        window.ensaladaFromPHP = <?= json_encode($categoryProducts['ensalada']); ?>;
        window.sopaFromPHP = <?= json_encode($categoryProducts['sopa']);     ?>;
    </script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <?php if (isset($error)): ?>
        <script>
            alert('Error: <?= htmlspecialchars($error, ENT_QUOTES) ?>');
        </script>
    <?php endif; ?>
</body>

</html>