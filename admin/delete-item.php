<?php
// admin/delete-item.php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

if (empty($_POST['id'])) {
    echo json_encode(['error' => 'ID no recibido']);
    exit;
}

// Cargar la clase ProductJSON
require_once __DIR__ . '/product-json.php';

$prod = new ProductJSON(true);  // modo admin
$id   = intval($_POST['id']);

/* ---------- Eliminar en JSON ---------- */
if ($prod->delete($id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'No se pudo eliminar el producto']);
}
