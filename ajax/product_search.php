<?php
require_once '../includes/auth.php';
require_login();
require '../config/database.php';
require '../app/models/ProductModel.php';
header('Content-Type: application/json; charset=utf-8');
$q=trim($_GET['q']??'');
if($q===''){echo json_encode(['success'=>true,'data'=>[]]);exit;}
try{echo json_encode(['success'=>true,'data'=>(new ProductModel($pdo))->search($q)],JSON_UNESCAPED_UNICODE);}catch(Throwable $e){http_response_code(500);echo json_encode(['success'=>false,'message'=>'Search failed.']);}
