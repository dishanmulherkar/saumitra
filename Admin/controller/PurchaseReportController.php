<?php

require_once 'modals/PurchaseReportModel.php';

class PurchaseReportController
{
    private $model;

    public function __construct()
    {
        $this->model = new PurchaseReportModel();
    }

    public function index()
    {
        $start_date = $_GET['start_date'] ?? '';
        $end_date   = $_GET['end_date'] ?? '';
        $supplier_id = $_GET['supplier_id'] ?? '';
        $suppliers = $this->model->getSupplier();
        $sales = $this->model->getReport($start_date, $end_date,$supplier_id);

        include 'view/report/PurchaseReport.php';
    }

    public function getPurDetails()
    {
        $sale_id = (int)$_POST['sale_id'];

        $result = $this->model->getPurDetails($sale_id);

        if(mysqli_num_rows($result) == 0)
        {
            echo "<tr><td colspan='4' class='text-center'>No Data Found</td></tr>";
            exit;
        }

         $total_amount = 0;

        while($row = mysqli_fetch_assoc($result))
        {
            $total_amount += $row['amount'];
        
            ?>
            <tr>
                <td><?= $row['product_name']; ?></td>
                <td class="text-end"><?= $row['qty']; ?></td>
                <td class="text-end"><?= number_format($row['purchase_rate'],2); ?></td>
                <td class="text-end"><?= number_format($row['amount'],2); ?></td>
            </tr>
            <?php
        }
        ?>
         <!-- Total Row -->
        <tr class="table-secondary fw-bold">
            <td colspan="3" class="text-end"><strong>Total Amount</strong></td>
            <td class="text-end"><strong><?= number_format($total_amount, 2); ?></strong></td>
        </tr>
        <?php


    }

     public function Purchase_combine()
    {
        $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
       $supplier_id = $_GET['supplier_id'] ?? '';
        $suppliers = $this->model->getSupplier();
        $purchase = $this->model->getPurchaseCombineReport($start_date, $end_date,$supplier_id);
        include 'view/report/PurchaseReportProdWise.php';
    }

}