<?php
class ReturnModel {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}

    public function saleOptions(): array {
        return $this->pdo->query("SELECT s.id,s.product_id,s.quantity,s.unit_price,s.created_at,p.name product,p.sku,c.name customer FROM sales s JOIN products p ON p.id=s.product_id LEFT JOIN customers c ON c.id=s.customer_id WHERE s.status='Completed' ORDER BY s.created_at DESC")->fetchAll();
    }
    public function purchaseOptions(): array {
        return $this->pdo->query("SELECT pr.id,pr.product_id,pr.quantity,pr.unit_price,pr.created_at,p.name product,p.sku,s.name supplier FROM purchases pr JOIN products p ON p.id=pr.product_id LEFT JOIN suppliers s ON s.id=pr.supplier_id WHERE pr.status='Completed' ORDER BY pr.created_at DESC")->fetchAll();
    }
    public function salesReturns(): array {
        return $this->pdo->query("SELECT r.*,p.name product,p.sku,u.name user_name FROM sales_returns r JOIN products p ON p.id=r.product_id LEFT JOIN users u ON u.id=r.user_id ORDER BY r.created_at DESC")->fetchAll();
    }
    public function purchaseReturns(): array {
        return $this->pdo->query("SELECT r.*,p.name product,p.sku,u.name user_name FROM purchase_returns r JOIN products p ON p.id=r.product_id LEFT JOIN users u ON u.id=r.user_id ORDER BY r.created_at DESC")->fetchAll();
    }
    public function processSaleReturn(int $saleId,int $qty,string $reason,int $userId): void {
        if($qty<=0) throw new InvalidArgumentException('Return quantity must be greater than zero.');
        $this->pdo->beginTransaction();
        try {
            $q=$this->pdo->prepare("SELECT s.id,s.product_id,s.quantity,s.unit_price,s.customer_id,s.status,p.name product FROM sales s JOIN products p ON p.id=s.product_id WHERE s.id=? FOR UPDATE");
            $q->execute([$saleId]); $sale=$q->fetch();
            if(!$sale || $sale['status']!=='Completed') throw new RuntimeException('Completed sale not found.');
            $r=$this->pdo->prepare('SELECT COALESCE(SUM(quantity),0) FROM sales_returns WHERE sale_id=?');$r->execute([$saleId]);$already=(int)$r->fetchColumn();
            if($already+$qty>(int)$sale['quantity']) throw new RuntimeException('Return quantity cannot exceed the sold quantity.');
            $amount=$qty*(float)$sale['unit_price'];
            $this->pdo->prepare('INSERT INTO sales_returns(sale_id,product_id,customer_id,quantity,refund_amount,reason,user_id) VALUES(?,?,?,?,?,?,?)')->execute([$saleId,$sale['product_id'],$sale['customer_id'],$qty,$amount,$reason?:null,$userId]);
            $this->pdo->prepare('UPDATE products SET quantity=quantity+? WHERE id=?')->execute([$qty,$sale['product_id']]);
            $this->pdo->prepare("INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)")->execute([$sale['product_id'],'IN',$qty,'Sales return',$userId]);
            $this->pdo->commit();
        } catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
    public function processPurchaseReturn(int $purchaseId,int $qty,string $reason,int $userId): void {
        if($qty<=0) throw new InvalidArgumentException('Return quantity must be greater than zero.');
        $this->pdo->beginTransaction();
        try {
            $q=$this->pdo->prepare("SELECT pr.id,pr.product_id,pr.quantity,pr.unit_price,pr.supplier_id,pr.status,p.name product FROM purchases pr JOIN products p ON p.id=pr.product_id WHERE pr.id=? FOR UPDATE");
            $q->execute([$purchaseId]); $purchase=$q->fetch();
            if(!$purchase || $purchase['status']!=='Completed') throw new RuntimeException('Completed purchase not found.');
            $r=$this->pdo->prepare('SELECT COALESCE(SUM(quantity),0) FROM purchase_returns WHERE purchase_id=?');$r->execute([$purchaseId]);$already=(int)$r->fetchColumn();
            if($already+$qty>(int)$purchase['quantity']) throw new RuntimeException('Return quantity cannot exceed the purchased quantity.');
            $stock=$this->pdo->prepare('SELECT quantity FROM products WHERE id=? FOR UPDATE');$stock->execute([$purchase['product_id']]);$current=(int)$stock->fetchColumn();
            if($current<$qty) throw new RuntimeException('Not enough current stock to return this purchase.');
            $amount=$qty*(float)$purchase['unit_price'];
            $this->pdo->prepare('INSERT INTO purchase_returns(purchase_id,product_id,supplier_id,quantity,refund_amount,reason,user_id) VALUES(?,?,?,?,?,?,?)')->execute([$purchaseId,$purchase['product_id'],$purchase['supplier_id'],$qty,$amount,$reason?:null,$userId]);
            $this->pdo->prepare('UPDATE products SET quantity=quantity-? WHERE id=?')->execute([$qty,$purchase['product_id']]);
            $this->pdo->prepare("INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)")->execute([$purchase['product_id'],'OUT',$qty,'Purchase return',$userId]);
            $this->pdo->commit();
        } catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
}
