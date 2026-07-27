<?php
// Include the model file
require_once 'modals/DashboardModel.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class DashboardController {
    private $db;
    private $model;

    public function __construct($con) {
        $this->db = $con;
        $this->model = new DashboardModel($this->db);
    }

    public function index() {
        $TotalSupplier = $this->model->TotalSupplier();
        $TotalCustomer = $this->model->TotalCustomer();
        $TotalProduct = $this->model->TotalProduct();
         include 'view/dashboard/index.php'; 
    }
        
}
?>