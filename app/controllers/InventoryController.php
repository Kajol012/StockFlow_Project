<?php
require_once __DIR__.'/../models/StockModel.php';

class InventoryController {
    private PDO $pdo;
    public function __construct(PDO $pdo){$this->pdo=$pdo;}

    public function dashboard(): void {
        $m=new StockModel($this->pdo); $stats=$m->dashboard(); $low=$m->lowStock(); $history=$m->history(8);
        $title='Inventory Manager Dashboard'; require __DIR__.'/../views/inventory/dashboard.php';
    }

    public function stock(): void {
        $m=new StockModel($this->pdo); $products=$m->products(); $title='Current Stock';
        require __DIR__.'/../views/inventory/stock.php';
    }

    public function stockIn(): void { $this->processStockChange('IN','Stock In'); }
    public function stockOut(): void { $this->processStockChange('OUT','Stock Out'); }

    private function processStockChange(string $type,string $pageTitle): void {
        $m=new StockModel($this->pdo); $products=$m->products(); $err='';
        if($_SERVER['REQUEST_METHOD']==='POST'){
            verify_csrf();
            $productId=(int)($_POST['product_id']??0); $quantity=(int)($_POST['quantity']??0); $note=trim($_POST['note']??'');
            try{
                $m->change($productId,$quantity,$type,$note,(int)current_user()['id']);
                flash('success',$pageTitle.' completed successfully.');
                redirect_to(project_base_url().'/inventory_manager/'.strtolower(str_replace(' ','_',$pageTitle)).'.php');
            }catch(Throwable $e){$err=$e->getMessage();}
        }
        $title=$pageTitle; require __DIR__.'/../views/inventory/stock_action.php';
    }

    public function adjustment(): void {
        $m=new StockModel($this->pdo); $products=$m->products(); $err='';
        if($_SERVER['REQUEST_METHOD']==='POST'){
            verify_csrf();
            $productId=(int)($_POST['product_id']??0); $newQuantity=(int)($_POST['new_quantity']??-1); $note=trim($_POST['note']??'');
            try{
                $m->adjust($productId,$newQuantity,$note,(int)current_user()['id']);
                flash('success','Stock adjustment completed successfully.');
                redirect_to(project_base_url().'/inventory_manager/adjustment.php');
            }catch(Throwable $e){$err=$e->getMessage();}
        }
        $title='Stock Adjustment'; require __DIR__.'/../views/inventory/adjustment.php';
    }

    public function lowStock(): void {$m=new StockModel($this->pdo);$products=$m->lowStock();$title='Low Stock Monitoring';require __DIR__.'/../views/inventory/low_stock.php';}
    public function history(): void {$m=new StockModel($this->pdo);$history=$m->history();$title='Stock History';require __DIR__.'/../views/inventory/history.php';}
    public function reports(): void {$m=new StockModel($this->pdo);$summary=$m->reportSummary();$rows=$m->reportRows();$title='Inventory Reports';require __DIR__.'/../views/inventory/reports.php';}
}
