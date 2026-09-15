<?php
require_once __DIR__ . '/../models/UserModel.php';
class ProfileController {
    public function __construct(private PDO $pdo) {}
    public function index(): void { $model=new UserModel($this->pdo);$id=(int)$_SESSION['user']['id'];$u=$model->find($id);if(!$u){session_destroy();redirect_to(project_base_url().'/login.php');}
        if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$username=trim($_POST['username']??'');$name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$current=$_POST['current_password']??'';$new=$_POST['new_password']??'';
            if(!preg_match('/^[A-Za-z0-9_.-]{3,30}$/',$username)||!preg_match('/^[A-Za-z][A-Za-z .\'-]{1,99}$/',$name)||!filter_var($email,FILTER_VALIDATE_EMAIL)){flash('error','Please enter a valid username, name and email.');redirect_to(project_base_url().'/profile/index.php');}
            $password=null;if($new!==''){if(strlen($new)<6||!password_verify($current,$model->passwordFor($id))){flash('error','Current password must be correct and the new password must be at least 6 characters.');redirect_to(project_base_url().'/profile/index.php');}$password=$new;}
            try{$model->updateOwnProfile($id,$username,$name,$email,$password);$_SESSION['user']['username']=$username;$_SESSION['user']['name']=$name;$_SESSION['user']['email']=$email;flash('success','Your profile was updated successfully.');}catch(PDOException $e){flash('error','Username or email already exists.');}redirect_to(project_base_url().'/profile/index.php');
        }
        $title='My Profile'; if(($_SESSION['user']['role']??'')==='Admin'){require __DIR__.'/../views/profile/index.php';}else{require __DIR__.'/../views/profile/role.php';}
    }
}
