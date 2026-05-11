<?php

class Producto {

    private $databaseConnection;

    public function __construct() {
        $this->databaseConnection = Database::getConnection();
    }

    public function listarProductos(): array {
        $sqlConsulta = "SELECT id, nombre, stock, stock_minimo, precio_mercado AS precio 
                        FROM productos 
                        WHERE is_active = true 
                        ORDER BY nombre ASC";
                        
        $statement = $this->databaseConnection->query($sqlConsulta);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearProducto(string $nombreProducto, int $cantidadStock, int $stockMinimoAlerta, float $precioVenta, float $precioCoste = 0.00): bool {
        $sqlInsertar = "INSERT INTO productos (nombre, stock, stock_minimo, precio_mercado, precio_coste, is_active) 
                        VALUES (:nombre, :stock, :stock_minimo, :precio_mercado, :precio_coste, true)";
                        
        $statement = $this->databaseConnection->prepare($sqlInsertar);
        
        return $statement->execute([
            'nombre'         => $nombreProducto,
            'stock'          => $cantidadStock,
            'stock_minimo'   => $stockMinimoAlerta,
            'precio_mercado' => $precioVenta,
            'precio_coste'   => $precioCoste
        ]);
    }

    public function actualizarStock(int $idProducto, int $nuevoStockExacto): bool {
        $sqlActualizar = "UPDATE productos SET stock = :stock WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute([
            'stock' => $nuevoStockExacto, 
            'id'    => $idProducto
        ]);
    }

    public function sumarStock(int $idProducto, int $cantidadSumar): bool {
        $sqlActualizar = "UPDATE productos SET stock = stock + :cantidad WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute([
            'cantidad' => $cantidadSumar, 
            'id'       => $idProducto
        ]);
    }

    public function eliminarProducto(int $idProducto): bool {
        $sqlActualizar = "UPDATE productos SET is_active = false WHERE id = :id";
        
        $statement = $this->databaseConnection->prepare($sqlActualizar);
        
        return $statement->execute(['id' => $idProducto]);
    }
}
?>