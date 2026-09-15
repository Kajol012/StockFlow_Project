<?php
class SupplierModel {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function all(): array { return $this->pdo->query('SELECT * FROM suppliers ORDER BY id DESC')->fetchAll(); }
    public function find(int $id): ?array { $s=$this->pdo->prepare('SELECT * FROM suppliers WHERE id=?');$s->execute([$id]);return $s->fetch()?:null; }
    public function save(?int $id,array $d): void {
        if($id){$s=$this->pdo->prepare('UPDATE suppliers SET name=?,phone=?,email=?,address=? WHERE id=?');$s->execute([$d['name'],$d['phone']?:null,$d['email']?:null,$d['address']?:null,$id]);}
        else{$s=$this->pdo->prepare('INSERT INTO suppliers(name,phone,email,address) VALUES(?,?,?,?)');$s->execute([$d['name'],$d['phone']?:null,$d['email']?:null,$d['address']?:null]);}
    }
    public function delete(int $id): void {$this->pdo->prepare('DELETE FROM suppliers WHERE id=?')->execute([$id]);}
}
