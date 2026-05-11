<?php

class Producto {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarProductos() {
        $sql = "SELECT id, nombre, stock, stock_minimo, precio_mercado AS precio 
                FROM productos 
                WHERE is_active = true 
                ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearProducto($nombre, $stock, $stock_minimo, $precio_mercado, $precio_coste = 0.00) {
        $sql = "INSERT INTO productos (nombre, stock, stock_minimo, precio_mercado, precio_coste, is_active) 
                VALUES (:n, :s, :sm, :p, :pc, true)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'n' => $nombre,
            's' => $stock,
            'sm' => $stock_minimo,
            'p' => $precio_mercado,
            'pc' => $precio_coste
        ]);
    }

    public function actualizarStock($id, $nuevo_stock) {
        $sql = "UPDATE productos SET stock = :s WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['s' => $nuevo_stock, 'id' => $id]);
    }

    public function sumarStock($id, $cantidad) {
        $sql = "UPDATE productos SET stock = stock + :c WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['c' => $cantidad, 'id' => $id]);
    }

    public function eliminarProducto($id) {
        // Hacemos un borrado lógico (desactivar)
        $sql = "UPDATE productos SET is_active = false WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>