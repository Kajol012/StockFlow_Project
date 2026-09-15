<?php
class CategoryModel {
    public function __construct(private PDO $pdo) {}
    public function find(int $id): ?array { $s=$this->pdo->prepare('SELECT * FROM categories WHERE id=?');$s->execute([$id]);return $s->fetch() ?: null; }
    public function all(): array { return $this->pdo->query('SELECT c.*,COUNT(p.id) product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name')->fetchAll(); }
    public function save(?int $id,string $name): void { if($id){$s=$this->pdo->prepare('UPDATE categories SET name=? WHERE id=?');$s->execute([$name,$id]);}else{$s=$this->pdo->prepare('INSERT INTO categories(name) VALUES(?)');$s->execute([$name]);} }
    public function delete(int $id): void { $this->pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]); }
}
