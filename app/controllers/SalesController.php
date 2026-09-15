<?php
require_once __DIR__.'/../models/CustomerModel.php';require_once __DIR__.'/../models/SalesModel.php';require_once __DIR__.'/../models/ReturnModel.php';
class SalesController {
    private PDO $pdo; public function __construct(PDO $pdo){$this->pdo=$pdo;}
    public function dashboard(): void {$sm=new SalesModel($this->pdo);$rows=$sm->all();$cus=(new CustomerModel($this->pdo))->all();$total=(float)$this->pdo->query("SELECT COALESCE(SUM(total),0) FROM sales")->fetchColumn();$count=(int)$this->pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn();$title='Sales Staff Dashboard';require __DIR__.'/../views/sales/dashboard.php';}
    public function customers(): void {$m=new CustomerModel($this->pdo);$edit=null;if(isset($_GET['edit']))$edit=$m->find((int)$_GET['edit']);
        if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)($_POST['id']??0);$d=['name'=>trim($_POST['name']??''),'phone'=>trim($_POST['phone']??''),'email'=>trim($_POST['email']??''),'address'=>trim($_POST['address']??'')];if($d['name']===''||($d['email']!==''&&!filter_var($d['email'],FILTER_VALIDATE_EMAIL))){flash('error','Please enter valid customer information.');redirect_to(project_base_url().'/staff/customers.php');}try{$m->save($id?:null,$d);flash('success',$id?'Customer updated successfully.':'Customer added successfully.');}catch(Throwable $e){flash('error','Customer could not be saved.');}redirect_to(project_base_url().'/staff/customers.php');}
        if(isset($_GET['delete'])){try{$m->delete((int)$_GET['delete']);flash('success','Customer deleted.');}catch(Throwable $e){flash('error','Customer cannot be deleted because it is used in sales history.');}redirect_to(project_base_url().'/staff/customers.php');}
        $customers=$m->all();$title='Customer Management';require __DIR__.'/../views/sales/customers.php';
    }
    public function sales(): void {$m=new SalesModel($this->pdo);if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$d=['customer_id'=>(int)($_POST['customer_id']??0),'product_id'=>(int)($_POST['product_id']??0),'quantity'=>(int)($_POST['quantity']??0),'unit_price'=>(float)($_POST['unit_price']??-1)];try{$m->create($d,(int)$_SESSION['user']['id']);flash('success','Sale recorded and stock reduced automatically.');}catch(Throwable $e){flash('error',$e->getMessage()==='Not enough stock available.'?$e->getMessage():'Sale could not be completed.');}redirect_to(project_base_url().'/staff/sales.php');}$products=$m->products();$customers=$m->customers();$sales=$m->all();$title='Sales Processing & History';require __DIR__.'/../views/sales/sales.php';}


    public function returns(): void {
        $m=new ReturnModel($this->pdo);
        if($_SERVER['REQUEST_METHOD']==='POST'){
            verify_csrf();
            $saleId=(int)($_POST['sale_id']??0); $qty=(int)($_POST['quantity']??0); $reason=trim($_POST['reason']??'');
            try{$m->processSaleReturn($saleId,$qty,$reason,(int)$_SESSION['user']['id']);flash('success','Sales return recorded and stock increased automatically.');}
            catch(Throwable $e){flash('error',$e->getMessage()==='Return quantity cannot exceed the sold quantity.'||$e->getMessage()==='Completed sale not found.'?$e->getMessage():'Sales return could not be completed.');}
            redirect_to(project_base_url().'/staff/returns.php');
        }
        $sales=$m->saleOptions(); $returns=$m->salesReturns(); $title='Sales Returns';
        require __DIR__.'/../views/sales/returns.php';
    }

    public function reports(): void {
        $m=new SalesModel($this->pdo);
        $summary=[
            'transactions'=>(int)$this->pdo->query("SELECT COUNT(*) FROM sales WHERE status='Completed'")->fetchColumn(),
            'total_value'=>(float)$this->pdo->query("SELECT COALESCE(SUM(total),0) FROM sales WHERE status='Completed'")->fetchColumn(),
            'total_quantity'=>(int)$this->pdo->query("SELECT COALESCE(SUM(quantity),0) FROM sales WHERE status='Completed'")->fetchColumn(),
            'customers'=>(int)$this->pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
            'profit'=>(float)$this->pdo->query("SELECT COALESCE(SUM(CASE WHEN unit_price>cost_price THEN (unit_price-cost_price)*quantity ELSE 0 END),0) FROM sales WHERE status='Completed'")->fetchColumn(),
            'loss'=>(float)$this->pdo->query("SELECT COALESCE(SUM(CASE WHEN cost_price>unit_price THEN (cost_price-unit_price)*quantity ELSE 0 END),0) FROM sales WHERE status='Completed'")->fetchColumn(),
        ];
        $rows=$m->all();
        $title='Sales Reports';
        require __DIR__.'/../views/sales/reports.php';
    }
}
