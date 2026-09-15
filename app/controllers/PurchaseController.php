<?php
require_once __DIR__.'/../models/SupplierModel.php';require_once __DIR__.'/../models/PurchaseModel.php';require_once __DIR__.'/../models/ReturnModel.php';
class PurchaseController {
    private PDO $pdo; public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function dashboard(): void {$pm=new PurchaseModel($this->pdo);$rows=$pm->all();$sup=(new SupplierModel($this->pdo))->all();$total=(float)$this->pdo->query("SELECT COALESCE(SUM(total),0) FROM purchases")->fetchColumn();$count=(int)$this->pdo->query("SELECT COUNT(*) FROM purchases")->fetchColumn();$title='Purchase Officer Dashboard';require __DIR__.'/../views/purchase/dashboard.php';}
    public function suppliers(): void {$m=new SupplierModel($this->pdo);$edit=null;if(isset($_GET['edit']))$edit=$m->find((int)$_GET['edit']);
        if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)($_POST['id']??0);$d=['name'=>trim($_POST['name']??''),'phone'=>trim($_POST['phone']??''),'email'=>trim($_POST['email']??''),'address'=>trim($_POST['address']??'')];
            if($d['name']===''||($d['email']!==''&&!filter_var($d['email'],FILTER_VALIDATE_EMAIL))){flash('error','Please enter valid supplier information.');redirect_to(project_base_url().'/purchase_officer/suppliers.php');}
            try{$m->save($id?:null,$d);flash('success',$id?'Supplier updated successfully.':'Supplier added successfully.');}catch(Throwable $e){flash('error','Supplier could not be saved.');}redirect_to(project_base_url().'/purchase_officer/suppliers.php');}
        if(isset($_GET['delete'])){try{$m->delete((int)$_GET['delete']);flash('success','Supplier deleted.');}catch(Throwable $e){flash('error','Supplier cannot be deleted because it is used in purchase history.');}redirect_to(project_base_url().'/purchase_officer/suppliers.php');}
        $suppliers=$m->all();$title='Supplier Management';require __DIR__.'/../views/purchase/suppliers.php';
    }
    public function purchases(): void {$m=new PurchaseModel($this->pdo);if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$d=['supplier_id'=>(int)($_POST['supplier_id']??0),'product_id'=>(int)($_POST['product_id']??0),'quantity'=>(int)($_POST['quantity']??0),'unit_price'=>(float)($_POST['unit_price']??-1)];try{$m->create($d,(int)$_SESSION['user']['id']);flash('success','Purchase recorded and stock increased automatically.');}catch(Throwable $e){flash('error',$e->getMessage()==='Product not found.'?$e->getMessage():'Purchase could not be completed.');}redirect_to(project_base_url().'/purchase_officer/purchases.php');}$products=$m->products();$suppliers=$m->suppliers();$purchases=$m->all();$title='Purchase Processing & History';require __DIR__.'/../views/purchase/purchases.php';}


    public function returns(): void {
        $m=new ReturnModel($this->pdo);
        if($_SERVER['REQUEST_METHOD']==='POST'){
            verify_csrf();
            $purchaseId=(int)($_POST['purchase_id']??0); $qty=(int)($_POST['quantity']??0); $reason=trim($_POST['reason']??'');
            try{$m->processPurchaseReturn($purchaseId,$qty,$reason,(int)$_SESSION['user']['id']);flash('success','Purchase return recorded and stock reduced automatically.');}
            catch(Throwable $e){$msg=$e->getMessage();flash('error',in_array($msg,['Return quantity cannot exceed the purchased quantity.','Completed purchase not found.','Not enough current stock to return this purchase.'],true)?$msg:'Purchase return could not be completed.');}
            redirect_to(project_base_url().'/purchase_officer/returns.php');
        }
        $purchases=$m->purchaseOptions(); $returns=$m->purchaseReturns(); $title='Purchase Returns';
        require __DIR__.'/../views/purchase/returns.php';
    }

    public function reports(): void {
        $m=new PurchaseModel($this->pdo);
        $summary=[
            'transactions'=>(int)$this->pdo->query("SELECT COUNT(*) FROM purchases WHERE status='Completed'")->fetchColumn(),
            'total_value'=>(float)$this->pdo->query("SELECT COALESCE(SUM(total),0) FROM purchases WHERE status='Completed'")->fetchColumn(),
            'total_quantity'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM purchases WHERE status='Completed'")->fetchColumn(),
            'suppliers'=>(int)$this->pdo->query('SELECT COUNT(*) FROM suppliers')->fetchColumn(),
            'profit'=>(float)$this->pdo->query("SELECT COALESCE(SUM(CASE WHEN selling_price>unit_price THEN (selling_price-unit_price)*quantity ELSE 0 END),0) FROM purchases WHERE status='Completed'")->fetchColumn(),
            'loss'=>(float)$this->pdo->query("SELECT COALESCE(SUM(CASE WHEN unit_price>selling_price THEN (unit_price-selling_price)*quantity ELSE 0 END),0) FROM purchases WHERE status='Completed'")->fetchColumn(),
        ];
        $rows=$m->all();
        $title='Purchase Reports';
        require __DIR__.'/../views/purchase/reports.php';
    }
}
