<?php
// admin/product-json.php

class ProductJSON
{
    private string $file;
    private array  $items;

    public function __construct(bool $adminMode = false)
    {
        $this->file = __DIR__ . '/../items.json';
        if (!is_readable($this->file)) {
            throw new Exception("No se puede leer items.json");
        }
        $data = json_decode(file_get_contents($this->file), true);
        if (!is_array($data)) {
            throw new Exception("items.json no es un array válido");
        }
        $this->items = $data;
    }

    /**
     * Devuelve todo el array de productos
     *
     * @return array
     */
    public function getAllProducts(): array
    {
        return $this->items;
    }

    /**
     * Devuelve un producto por su ID, o null si no existe
     *
     * @param int $id
     * @return array|null
     */
    public function read_single(int $id): ?array
    {
        foreach ($this->items as $item) {
            if ((int)$item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Actualiza los campos dados de un producto y reescribe el JSON.
     *
     * @param int   $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $fields): bool
    {
        $found = false;
        foreach ($this->items as &$item) {
            if ((int)$item['id'] === $id) {
                $item = array_merge($item, $fields);
                $found = true;
                break;
            }
        }
        if (! $found) {
            return false;
        }
        return $this->save();
    }

    /**
     * Elimina un producto por su ID y reescribe el JSON.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $found = false;
        foreach ($this->items as $idx => $item) {
            if ((int)$item['id'] === $id) {
                array_splice($this->items, $idx, 1);
                $found = true;
                break;
            }
        }
        if (! $found) {
            return false;
        }
        return $this->save();
    }

    /**
     * Genera el siguiente ID incremental basado en los IDs existentes.
     *
     * @return int
     */
    public function getNextId(): int
    {
        $ids = array_column($this->items, 'id');
        $ids = array_map(fn($v) => (int)$v, $ids);
        return empty($ids) ? 1 : (max($ids) + 1);
    }

    /**
     * Crea un nuevo producto y lo añade al JSON.
     *
     * @param array $fields
     * @return bool
     */
    public function create(array $fields): bool
    {
        // Asignar ID si no está presente
        if (!isset($fields['id'])) {
            $fields['id'] = $this->getNextId();
        }
        $this->items[] = $fields;
        return $this->save();
    }

    /**
     * Guarda el array items de vuelta en el archivo JSON.
     *
     * @return bool
     */
    private function save(): bool
    {
        $json = json_encode($this->items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            return false;
        }
        return file_put_contents($this->file, $json) !== false;
    }
}
