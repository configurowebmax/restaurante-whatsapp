<?php
// admin/require_once __DIR__ . '/product-json.php';
class ProductJSON
{
    private array $items;

    /**
     * @param bool $adminMode  Si es true, puede incluir propiedades extra o no filtrar nada.
     */
    public function __construct(bool $adminMode = false)
    {
        $path = __DIR__ . '/../items.json';
        if (!is_readable($path)) {
            throw new Exception("No se puede leer items.json");
        }
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            throw new Exception("items.json no contiene un array válido");
        }
        $this->items = $data;
    }

    /**
     * Devuelve todos los productos como array asociativo.
     * @return array|false
     */
    public function getAllProducts()
    {
        return $this->items;
    }
}
