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
        $start_date = $_GET['start_date'] ?? '';
        $end_date   = $_GET['end_date'] ?? '';
        $sales = $this->model->getStockSalesReport($start_date, $end_date);
        include 'view/report/StockReport.php';
    }

      public function getSaleDetails()
    {
        $sale_id = (int)$_POST['sale_id'];
        $result = $this->model->getSaleDetails($sale_id);

        if(mysqli_num_rows($result) == 0)
        {
            echo "<tr><td colspan='5' class='text-center'>No Data Found</td></tr>";
            exit;
        }

        $total_amount = 0;

        while($row = mysqli_fetch_assoc($result))
        {
            $total_amount += $row['amount'];
            ?>
            <tr>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['batch_no']; ?></td>
                <td class="text-end"><?= $row['qty']; ?></td>
                <td class="text-end"><?= number_format($row['rate'], 2); ?></td>
                <td class="text-end"><?= number_format($row['amount'], 2); ?></td>
            </tr>
            <?php
        }
        ?>

        <!-- Total Row -->
        <tr class="table-secondary fw-bold">
            <td colspan="4" class="text-end"><strong>Total Amount</strong></td>
            <td class="text-end"><strong><?= number_format($total_amount, 2); ?></strong></td>
        </tr>

        <?php
    }
}