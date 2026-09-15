<?php
class PurchaseModel {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function all(): array { return $this->pdo->query('SELECT pr.*,p.name product,p.sku,s.name supplier,u.name user_name FROM purchases pr JOIN products p ON p.id=pr.product_id LEFT JOIN suppliers s ON s.id=pr.supplier_id JOIN users u ON u.id=pr.user_id ORDER BY pr.created_at DESC')->fetchAll(); }
    public function products(): array { return $this->pdo->query('SELECT id,name,sku,quantity,price FROM products ORDER BY name')->fetchAll(); }
    public function suppliers(): array { return $this->pdo->query('SELECT id,name FROM suppliers ORDER BY name')->fetchAll(); }
    public function create(array $d,int $userId): void {
        if($d['quantity']<=0||$d['unit_price']<0) throw new InvalidArgumentException('Invalid purchase values.');
        $this->pdo->beginTransaction();
        try {
            $s=$this->pdo->prepare('SELECT quantity,price FROM products WHERE id=? FOR UPDATE');$s->execute([$d['product_id']]);$product=$s->fetch();if(!$product) throw new RuntimeException('Product not found.');
            $total=$d['quantity']*$d['unit_price'];
            $this->pdo->prepare('INSERT INTO purchases(supplier_id,product_id,quantity,unit_price,total,selling_price,status,user_id) VALUES(?,?,?,?,?,?,?,?)')->execute([$d['supplier_id']?:null,$d['product_id'],$d['quantity'],$d['unit_price'],$total,(float)$product['price'],'Completed',$userId]);
            $this->pdo->prepare('UPDATE products SET quantity=quantity+?, cost_price=? WHERE id=?')->execute([$d['quantity'],$d['unit_price'],$d['product_id']]);
            $this->pdo->prepare("INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)")->execute([$d['product_id'],'IN',$d['quantity'],'Purchase transaction',$userId]);
            $this->pdo->commit();
        }catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
}
