<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Corrección: cargar la clase desde product-json.php
require_once __DIR__ . '/product-json.php';      // ProductJSON
$prod = new ProductJSON(true);

$id = intval($_GET['id'] ?? 0);
$item = $prod->read_single($id);
if (!$item) {
    $_SESSION['error'] = 'Producto no encontrado';
    header('Location: index.php');
    exit;
}

/* ── Detectar tipo y nombre base ───────────────────────────────────── */
$prefixes = [
    'especial'  => 'Especial ',
    'solo_sopa' => 'Solo Sopa ',
    'entrada'   => 'Entrada ',
    'ensalada'  => 'Ensalada '
];
$tipo      = 'del_dia';         // valor por defecto
$baseName  = $item['name'];

foreach ($prefixes as $k => $p) {
    if (str_starts_with($item['name'], $p)) {
        $tipo     = $k;
        $baseName = substr($item['name'], strlen($p));
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar producto – Panel</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="shortcut icon" href="../icono.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-edit"></i> Editar producto</h2>

        <form action="edit_item.php" method="POST" enctype="multipart/form-data" class="card p-4">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <div class="form-group">
                <label>Tipo de producto</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="especial" <?= $tipo === 'especial'  ? 'selected' : '' ?>>Especial</option>
                    <option value="del_dia" <?= $tipo === 'del_dia'   ? 'selected' : '' ?>>Plato del día</option>
                    <option value="solo_sopa" <?= $tipo === 'solo_sopa' ? 'selected' : '' ?>>Solo Sopa</option>
                    <option value="entrada" <?= $tipo === 'entrada'   ? 'selected' : '' ?>>Entrada</option>
                    <option value="ensalada" <?= $tipo === 'ensalada'  ? 'selected' : '' ?>>Ensalada</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nombre (sin prefijo)</label>
                <input type="text" name="name" class="form-control"
                    value="<?= htmlspecialchars($baseName, ENT_QUOTES) ?>" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES) ?></textarea>
            </div>

            <div class="form-group">
                <label>Precio (COP)</label>
                <input type="number" name="price" class="form-control" min="0" step="1"
                    value="<?= floatval($item['price']) ?>" required>
            </div>

            <div class="form-group">
                <label>Imagen actual</label><br>
                <?php if (!empty($item['image'] ?? $item['image_path'])): ?>
                    <img src="../<?= htmlspecialchars($item['image'] ?? $item['image_path'], ENT_QUOTES) ?>"
                        style="max-width:120px" alt="Imagen producto">
                <?php else: ?>
                    <em>Sin imagen</em>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Reemplazar imagen</label>
                <input type="file" name="image" accept="image/*" class="form-control-file">
            </div>

            <button class="btn btn-primary">Guardar cambios</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
</body>

</html>