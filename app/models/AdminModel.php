<?php
class AdminModel {
    public function __construct(private PDO $pdo) {}

    public function dashboardStats(): array {
        return [
            'users' => (int)$this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'products' => (int)$this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'categories' => (int)$this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
            'stock' => (int)$this->pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn(),
            'low' => (int)$this->pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= reorder_level')->fetchColumn(),
            'sales' => (float)$this->pdo->query('SELECT COALESCE(SUM(total),0) FROM sales')->fetchColumn(),
            'purchases' => (float)$this->pdo->query('SELECT COALESCE(SUM(total),0) FROM purchases')->fetchColumn(),
        ];
    }

    public function lowStock(int $limit = 7): array {
        $stmt = $this->pdo->prepare('SELECT p.name,p.sku,p.quantity,p.reorder_level,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.quantity <= p.reorder_level ORDER BY p.quantity ASC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function recentActivity(int $limit = 8): array {
        $stmt = $this->pdo->prepare("SELECT * FROM (
            SELECT 'Sale' activity, s.created_at, s.total amount, p.name item, u.name user_name FROM sales s JOIN products p ON p.id=s.product_id JOIN users u ON u.id=s.user_id
            UNION ALL
            SELECT 'Purchase', pr.created_at, pr.total, p.name, u.name FROM purchases pr JOIN products p ON p.id=pr.product_id JOIN users u ON u.id=pr.user_id
        ) x ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function reportSummary(): array {
        $sales=$this->pdo->query("SELECT COUNT(*) n, COALESCE(SUM(total),0) total, COALESCE(SUM((unit_price-cost_price)*quantity),0) margin FROM sales WHERE status='Completed'")->fetch();
        $purchase=$this->pdo->query("SELECT COUNT(*) n, COALESCE(SUM(total),0) total, COALESCE(SUM((selling_price-unit_price)*quantity),0) margin FROM purchases WHERE status='Completed'")->fetch();
        $salesReturns=(float)$this->pdo->query("SELECT COALESCE(SUM(r.quantity*(s.unit_price-s.cost_price)),0) FROM sales_returns r JOIN sales s ON s.id=r.sale_id")->fetchColumn();
        $purchaseReturns=(float)$this->pdo->query("SELECT COALESCE(SUM(r.quantity*(p.selling_price-p.unit_price)),0) FROM purchase_returns r JOIN purchases p ON p.id=r.purchase_id")->fetchColumn();
        $netSalesMargin=(float)$sales['margin']-$salesReturns;
        $netPurchaseMargin=(float)$purchase['margin']-$purchaseReturns;
        return [
            'sales'=>$sales,
            'purchases'=>$purchase,
            'sales_returns_margin'=>$salesReturns,
            'purchase_returns_margin'=>$purchaseReturns,
            'net_sales_margin'=>$netSalesMargin,
            'net_purchase_margin'=>$netPurchaseMargin,
            'overall_margin'=>$netSalesMargin,
            'stock'=>$this->pdo->query('SELECT sku,name,quantity,reorder_level,price,cost_price FROM products ORDER BY quantity ASC')->fetchAll(),
        ];
    }
}
