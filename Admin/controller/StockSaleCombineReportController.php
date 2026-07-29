<?php

require_once 'modals/StockSaleCombineReportModel.php';

class StockSaleCombineReportController
{
    private $model;

    public function __construct()
    {
        $this->model = new StockSaleCombineReportModel();
    }

    public function index()
    {
         $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
        $sales = $this->model->getStockSalesReport($start_date, $end_date);
        include 'view/report/StockSaleCombine.php';
    }
}