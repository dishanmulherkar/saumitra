<?php

require_once 'modals/SalesReportProdWise_mdl.php';

class SalesReportProdWise_ctl
{
    private $model;

    public function __construct()
    {
        $this->model = new SalesReportProdWise();
    }

    public function index()
    {
        $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
        $customer_id = $_GET['customer_id'] ?? '';
        $customers = $this->model->getCustomers();
        $sales = $this->model->getStockSalesReport($start_date, $end_date,$customer_id);
        include 'view/report/SalesReportProdWise.php';
    }
}