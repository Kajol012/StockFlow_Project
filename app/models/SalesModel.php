<?php
class SalesModel {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function all(): array { return $this->pdo->query('SELECT s.*,p.name product,p.sku,c.name customer,u.name user_name FROM sales s JOIN products p ON p.id=s.product_id LEFT JOIN customers c ON c.id=s.customer_id JOIN users u ON u.id=s.user_id ORDER BY s.created_at DESC')->fetchAll(); }
    public function products(): array { return $this->pdo->query('SELECT id,name,sku,quantity,price,cost_price FROM products ORDER BY name')->fetchAll(); }
    public function customers(): array { return $this->pdo->query('SELECT id,name FROM customers ORDER BY name')->fetchAll(); }
    public function create(array $d,int $userId): void {
        if($d['quantity']<=0||$d['unit_price']<0) throw new InvalidArgumentException('Invalid sale values.');
        $this->pdo->beginTransaction();
        try {
            $s=$this->pdo->prepare('SELECT quantity,cost_price FROM products WHERE id=? FOR UPDATE');$s->execute([$d['product_id']]);$product=$s->fetch();if(!$product)throw new RuntimeException('Product not found.');$stock=(int)$product['quantity'];
            if($stock<$d['quantity'])throw new RuntimeException('Not enough stock available.');
            $total=$d['quantity']*$d['unit_price'];
            $this->pdo->prepare('INSERT INTO sales(customer_id,product_id,quantity,unit_price,total,cost_price,status,user_id) VALUES(?,?,?,?,?,?,?,?)')->execute([$d['customer_id']?:null,$d['product_id'],$d['quantity'],$d['unit_price'],$total,(float)$product['cost_price'],'Completed',$userId]);
            $this->pdo->prepare('UPDATE products SET quantity=quantity-? WHERE id=?')->execute([$d['quantity'],$d['product_id']]);
            $this->pdo->prepare("INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)")->execute([$d['product_id'],'OUT',$d['quantity'],'Sales transaction',$userId]);
            $this->pdo->commit();
        }catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
}
