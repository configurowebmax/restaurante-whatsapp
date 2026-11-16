<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar producto – Panel</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="shortcut icon" href="../icono.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-plus"></i> Agregar producto</h2>

        <form action="add_item.php" method="POST" enctype="multipart/form-data" class="card p-4">
            <!-- Tipo / Categoría -->
            <div class="form-group">
                <label>Tipo de producto</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="" selected disabled>— Selecciona —</option>
                    <option value="especial">Especial</option>
                    <option value="del_dia">Plato del día</option>
                    <option value="solo_sopa">Solo Sopa</option>
                    <option value="entrada">Entrada</option>
                    <option value="ensalada">Ensalada</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nombre (sin prefijo)</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Descripción (opcional)</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Precio (COP)</label>
                <input type="number" name="price" class="form-control" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label>Imagen (JPG / PNG)</label>
                <input type="file" name="image" accept="image/*" class="form-control-file">
            </div>

            <button class="btn btn-primary">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- FontAwesome sólo para icono de cabecera -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
</body>

</html>