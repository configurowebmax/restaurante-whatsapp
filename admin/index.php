<?php
/* ------------------------------------------------------------------
   admin/index.php  –  panel de productos usando items.json
   ------------------------------------------------------------------ */
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* ── 1.  Cargas necesarias ───────────────────────────────────────── */
// Clase ProductJSON carga items.json
require_once __DIR__ . '/product-json.php';

/* ── 2.  Obtener lista de productos ───────────────────────────────── */
try {
    $product  = new ProductJSON(true);  // true = modo admin
    $products = $product->getAllProducts();
    if ($products === false) {
        throw new Exception('Error al obtener productos');
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo - Las delicias de Elida</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="shortcut icon" href="../icono.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .product-image {
            max-width: 100px;
            height: auto;
            object-fit: cover;
        }

        .action-buttons .btn {
            margin: 0 2px;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#"><i class="fas fa-utensils mr-2"></i>Panel Administrativo</a>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarNav" class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="agregar-producto.php"><i class="fas fa-plus"></i> Agregar Producto</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
            <span class="navbar-text"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['username']) ?></span>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-list"></i> Gestión de Productos</h2>
            <a href="agregar-producto.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Producto
            </a>
        </div>

        <?php if (empty($error) && !empty($products)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-right">Precio</th>
                            <th class="text-center">Imagen</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $item): ?>
                            <tr>
                                <td class="text-center"><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['category_name'] ?? '—') ?></td>
                                <td class="text-right">$<?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php $img = $item['image'] ?? $item['image_path'] ?? ''; ?>
                                    <?php if (!empty($img)): ?>
                                        <img src="<?= '../' . htmlspecialchars($img) ?>" class="product-image">
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-image"></i> Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center action-buttons">
                                    <a href="editar-producto.php?id=<?= $item['id'] ?>"
                                        class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(<?= $item['id'] ?>)"
                                        title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay productos registrados.
            </div>
        <?php endif; ?>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            if (!confirm('¿Eliminar este producto?')) return;
            $.post('delete-item.php', {
                id: id
            }, function(r) {
                if (r.success) location.reload();
                else alert('Error: ' + r.error);
            }, 'json').fail(() => alert('Fallo de red'));
        }
    </script>
</body>

</html>