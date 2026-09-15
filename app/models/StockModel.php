<?php
class StockModel {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function products(): array {
        return $this->pdo->query('SELECT p.*, c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.name')->fetchAll();
    }
    public function dashboard(): array {
        return [
            'products'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'units'=>(int)$this->pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn(),
            'low'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= reorder_level')->fetchColumn(),
            'out'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products WHERE quantity=0')->fetchColumn(),
            'in_today'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_transactions WHERE type='IN' AND DATE(created_at)=CURDATE()")->fetchColumn(),
            'out_today'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_transactions WHERE type='OUT' AND DATE(created_at)=CURDATE()")->fetchColumn(),
        ];
    }
    public function history(int $limit=100): array {
        $s=$this->pdo->prepare("SELECT st.*,p.name product,p.sku,u.name user_name FROM stock_transactions st JOIN products p ON p.id=st.product_id LEFT JOIN users u ON u.id=st.user_id ORDER BY st.created_at DESC LIMIT ?");
        $s->bindValue(1,$limit,PDO::PARAM_INT);$s->execute();return $s->fetchAll();
    }
    public function lowStock(): array { return $this->pdo->query('SELECT p.*,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.quantity <= p.reorder_level ORDER BY p.quantity ASC')->fetchAll(); }
    public function change(int $productId,int $delta,string $type,string $note,int $userId): void {
        if(!in_array($type,['IN','OUT'],true)) throw new InvalidArgumentException('Invalid stock transaction type.');
        if($delta<=0) throw new InvalidArgumentException('Quantity must be positive.');
        $this->pdo->beginTransaction();
        try {
            $s=$this->pdo->prepare('SELECT quantity FROM products WHERE id=? FOR UPDATE');$s->execute([$productId]);$current=$s->fetchColumn();
            if($current===false) throw new RuntimeException('Product not found.');
            $current=(int)$current;
            if($type==='OUT' && $current<$delta) throw new RuntimeException('Not enough stock available.');
            $new=$type==='IN'?$current+$delta:$current-$delta;
            $this->pdo->prepare('UPDATE products SET quantity=? WHERE id=?')->execute([$new,$productId]);
            $this->pdo->prepare('INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)')->execute([$productId,$type,$delta,$note?:null,$userId]);
            $this->pdo->commit();
        } catch(Throwable $e) { if($this->pdo->inTransaction()) $this->pdo->rollBack(); throw $e; }
    }
    public function adjust(int $productId,int $newQuantity,string $note,int $userId): void {
        if($newQuantity<0) throw new InvalidArgumentException('Quantity cannot be negative.');
        $this->pdo->beginTransaction();
        try {
            $s=$this->pdo->prepare('SELECT quantity FROM products WHERE id=? FOR UPDATE');$s->execute([$productId]);$old=$s->fetchColumn();
            if($old===false) throw new RuntimeException('Product not found.');
            $delta=$newQuantity-(int)$old;
            if($delta===0) throw new InvalidArgumentException('The new quantity is the same as the current quantity.');
            $this->pdo->prepare('UPDATE products SET quantity=? WHERE id=?')->execute([$newQuantity,$productId]);
            $this->pdo->prepare("INSERT INTO stock_transactions(product_id,type,quantity,note,user_id) VALUES(?,?,?,?,?)")->execute([$productId,'ADJUSTMENT',abs($delta),trim(($note?:'Stock adjustment').' ('.($delta>=0?'+':'').$delta.')'),$userId]);
            $this->pdo->commit();
        } catch(Throwable $e) { if($this->pdo->inTransaction()) $this->pdo->rollBack(); throw $e; }
    }
    public function reportSummary(): array {
        return [
            'total_products'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'total_units'=>(int)$this->pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn(),
            'low_stock'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= reorder_level')->fetchColumn(),
            'out_of_stock'=>(int)$this->pdo->query('SELECT COUNT(*) FROM products WHERE quantity=0')->fetchColumn(),
            'total_in'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_transactions WHERE type='IN'")->fetchColumn(),
            'total_out'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_transactions WHERE type='OUT'")->fetchColumn(),
        ];
    }
    public function reportRows(): array {
        return $this->pdo->query("SELECT st.created_at, st.type, st.quantity, st.note, p.name product, p.sku, u.name user_name FROM stock_transactions st JOIN products p ON p.id=st.product_id LEFT JOIN users u ON u.id=st.user_id ORDER BY st.created_at DESC")->fetchAll();
    }
}
