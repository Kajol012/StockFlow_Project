<?php
class ProductModel {
    public function __construct(private PDO $pdo) {}
    public function find(int $id): ?array { $s=$this->pdo->prepare('SELECT * FROM products WHERE id=?');$s->execute([$id]);return $s->fetch() ?: null; }
    public function all(): array { return $this->pdo->query('SELECT p.*,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll(); }
    public function categories(): array { return $this->pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(); }
    public function create(array $d): void { $s=$this->pdo->prepare('INSERT INTO products(name,sku,category_id,price,quantity,reorder_level) VALUES(?,?,?,?,?,?)');$s->execute([$d['name'],$d['sku'],$d['category_id'],$d['price'],$d['quantity'],$d['reorder_level']]); }
    public function update(int $id,array $d): void { $s=$this->pdo->prepare('UPDATE products SET name=?,sku=?,category_id=?,price=?,quantity=?,reorder_level=? WHERE id=?');$s->execute([$d['name'],$d['sku'],$d['category_id'],$d['price'],$d['quantity'],$d['reorder_level'],$id]); }
    public function delete(int $id): void { $this->pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]); }
    public function search(string $q): array { $s=$this->pdo->prepare('SELECT id,name,sku,quantity,price FROM products WHERE name LIKE ? OR sku LIKE ? ORDER BY name LIMIT 20');$like='%'.$q.'%';$s->execute([$like,$like]);return $s->fetchAll(); }
}
