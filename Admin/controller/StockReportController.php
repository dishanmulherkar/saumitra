<?php

require_once 'modals/StockReportModel.php';

class StockReportController
{
    private $model;

    public function __construct()
    {
        $this->model = new StockReportModel();
    }

     public function index()
    {
        $start_date = isset($_GET['start_date']) && $_GET['start_date'] != ''
            ? $_GET['start_date']
            : date('Y-m-01');
        $end_date = isset($_GET['end_date']) && $_GET['end_date'] != ''
            ? $_GET['end_date']
            : date('Y-m-t');
        $sales = $this->model->getStockSalesReport($start_date, $end_date);

        include 'view/report/StockReport.php';
    }

}