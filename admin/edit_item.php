<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cargar la clase que maneja el JSON de productos
require_once __DIR__ . '/product-json.php';  // ProductJSON
$prod = new ProductJSON(true);

/* ---------- Datos del formulario ---------- */
$id    = intval($_POST['id'] ?? 0);
$tipo  =        $_POST['tipo'] ?? '';
$base  = trim($_POST['name'] ?? '');
$desc  = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if (!$id || $tipo === '' || $base === '' || $price < 0) {
    $_SESSION['error'] = 'Campos obligatorios faltantes';
    header('Location: editar-producto.php?id=' . $id);
    exit;
}

/* ---------- Prefijo según tipo ---------- */
$map = [
    'especial'  => 'Especial ',
    'solo_sopa' => 'Solo Sopa ',
    'entrada'   => 'Entrada ',
    'ensalada'  => 'Ensalada ',
    'del_dia'   => ''
];
$fullName = ($map[$tipo] ?? '') . $base;

/* ---------- Imagen (opcional) ---------- */
$itemOld = $prod->read_single($id);
$imgPath = $itemOld['image'] ?? $itemOld['image_path'] ?? '';

if (!empty($_FILES['image']['name'])) {
    $folder = __DIR__ . '/../img/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file = uniqid('prod_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $file)) {
        $imgPath = 'img/' . $file;
    }
}

/* ---------- Actualizar en JSON ---------- */
$ok = $prod->update($id, [
    'name'          => $fullName,
    'description'   => $desc,
    'price'         => $price,
    'image'         => $imgPath,
    'category_name' => ucfirst(str_replace('_', ' ', $tipo))
]);

$_SESSION[$ok ? 'success' : 'error'] =
    $ok ? 'Producto actualizado con éxito.' : 'No se pudo actualizar.';
header('Location: index.php');
