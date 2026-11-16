<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cargar la clase ProductJSON
require_once __DIR__ . '/product-json.php';  // contiene ProductJSON

/* ---- recoger y validar ---- */
$tipo  = $_POST['tipo']        ?? '';
$base  = trim($_POST['name']   ?? '');
$desc  = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if ($tipo === '' || $base === '' || $price < 0) {
    $_SESSION['error'] = 'Tipo, nombre y precio son obligatorios.';
    header('Location: agregar-producto.php');
    exit;
}

/* ---- prefijo según el tipo ---- */
$prefixMap = [
    'especial'  => 'Especial ',
    'solo_sopa' => 'Solo Sopa ',
    'entrada'   => 'Entrada ',
    'ensalada'  => 'Ensalada ',
    'del_dia'   => ''                 // platos del día sin prefijo
];
$fullName = ($prefixMap[$tipo] ?? '') . $base;

/* ---- subir imagen (opcional) ---- */
$imgPath = '';
if (!empty($_FILES['image']['name'])) {
    $folder = __DIR__ . '/../img/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file = uniqid('prod_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $file)) {
        $imgPath = 'img/' . $file;
    }
}

// Crear el producto en el JSON
$prod = new ProductJSON(true);
$new  = $prod->create([
    'id'            => $prod->getNextId(),  // o similar si implementas auto-increment
    'name'          => $fullName,
    'description'   => $desc,
    'price'         => $price,
    'image'         => $imgPath,
    'category_name' => ucfirst(str_replace('_', ' ', $tipo))
]);

$_SESSION[$new ? 'success' : 'error'] =
    $new ? 'Producto creado con éxito.' : 'No se pudo guardar el producto.';
header('Location: index.php');
