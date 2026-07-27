<?php include 'view/layout/header.php'; ?>

<style>
    .mb-3 {
    margin-bottom: 1rem !important;
    padding: 13px;
   }
   h4{
    margin-bottom: .1rem;
    font-size: larger;
   }
   .bg-black {
    --bs-bg-opacity: 1;
    background-color: #063512 !important;
   }
   .table-dark {
    --bs-table-color: #fff;
    --bs-table-bg: #063512;
} 
.btn-info {
    --bs-btn-color: #ffffff;
    --bs-btn-bg: #0d6efd;
}
</style>


<div class="container-fluid mt-3">

    <div class="card">

        <div class="card-header bg-black text-white">
            <h4>Stock And Sales Report</h4>
        </div>

          <form method="GET" action="<?= BASE_URL ?>stock_report_combine">

    <div>
        <div class="row ml-3 mb-3">

            <div class="col-md-3">
                <label>Start Date</label>
                <input
                    type="date"
                    name="start_date"
                    class="form-control"
                    value="<?= $_GET['start_date'] ?? '' ?>">
            </div>

            <div class="col-md-3">
                <label>End Date</label>
                <input
                    type="date"
                    name="end_date"
                    class="form-control"
                    value="<?= $_GET['end_date'] ?? '' ?>">
            </div>

            <div class="col-md-2 mt-4">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>

        </div>
    </div>
<hr></hr>
    </form> 

        <div class="card-body">

        <table class="table table-bordered table-striped" id="reportTable">

            <thead class="table-dark">

                <tr>

                    <th>#</th>

                    <th>Product Name</th>
                    
                    <th class="text-end">Opening Qty</th>
                    <th class="text-end">Purchase Qty</th>

                    <!-- <th class="text-end">Purchase Rate</th> -->

                    <th class="text-end">Purchase Amount</th>

                    <th class="text-end">Sales Qty</th>

                    <!-- <th class="text-end">Sales Rate</th> -->

                    <th class="text-end">Sales Amount</th>

                    <th class="text-end">Closing Qty</th>

                    <th class="text-end">Closing Amount</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $i = 1;

            $totalPurchaseQty = 0;
            $totalPurchaseAmount = 0;

            $totalSalesQty = 0;
            $totalSalesAmount = 0;

            $totalClosingQty = 0;
            $totalClosingAmount = 0;

            while($row = mysqli_fetch_assoc($sales))
            {

                $totalPurchaseQty += $row['purchase_qty'];
                $totalPurchaseAmount += $row['purchase_amount'];

                $totalSalesQty += $row['sales_qty'];
                $totalSalesAmount += $row['sales_amount'];

                $totalClosingQty += $row['closing_qty'];
                $totalClosingAmount += $row['closing_amount'];

            ?>

                <tr>

                    <td><?= $i++; ?></td>

                    <td><?= $row['product_name']; ?></td>


                     <td class="text-end"><?= $row['opening_qty']; ?></td>
                    <td class="text-end"><?= $row['purchase_qty']; ?></td>

                    <!-- <td class="text-end">
                        ₹ <?= number_format($row['purchase_rate'],2); ?>
                    </td> -->

                    <td class="text-end">
                        ₹ <?= number_format($row['purchase_amount'],2); ?>
                    </td>

                    <td class="text-end"><?= $row['sales_qty']; ?></td>

                    <!-- <td class="text-end"><?= $row['sales_rate']; ?></td> -->

                    <td class="text-end">
                        ₹ <?= number_format($row['sales_amount'],2); ?>
                    </td>

                    <td class="text-end"><?= $row['closing_qty']; ?></td>

                    <td class="text-end">
                        ₹ <?= number_format($row['closing_amount'],2); ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

            <tfoot class="table-dark">

                <tr>

                    <th colspan="3" class="text-end">
                        Grand Total
                    </th>

                    <th class="text-end">
                        <?= number_format($totalPurchaseQty,2); ?>
                    </th>
                    
                    <th class="text-end">
                        ₹ <?= number_format($totalPurchaseAmount,2); ?>
                    </th>

                    <th class="text-end">
                        <?= number_format($totalSalesQty,2); ?>
                    </th>
                    
                    <th class="text-end">
                        ₹ <?= number_format($totalSalesAmount,2); ?>
                    </th>

                    <th class="text-end">
                        <?= number_format($totalClosingQty,2); ?>
                    </th>

                    <th class="text-end">
                        ₹ <?= number_format($totalClosingAmount,2); ?>
                    </th>

                </tr>

            </tfoot>

        </table>
        

        </div>

    </div>

</div>




<?php include 'view/layout/footer.php'; ?>

<script>
$(document).ready(function () {

    var table = $('#reportTable').DataTable({

        pageLength: 10,

        dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>' +
             'rt' +
             '<"row mt-3"<"col-md-6"B><"col-md-6 text-end"ip>>',

        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                footer: true,
                title: 'Stock & Sales Report',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]

    });

});
</script>