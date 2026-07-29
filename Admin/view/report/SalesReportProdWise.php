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
            <h4>Sales Report</h4>
        </div>

          <form method="GET" action="<?= BASE_URL ?>salesreportprodwise">

    <div>
        <div class="row ml-3 mb-3">

            <div class="col-md-3">
                <label>Start Date</label>
                <input
                    type="date"
                    name="start_date"
                    id = "start_date"
                    class="form-control"
                    value="<?= $_GET['start_date'] ?? '' ?>">
            </div>

            <div class="col-md-3">
                <label>End Date</label>
                <input
                    type="date"
                    name="end_date"
                    id = "end_date"
                    class="form-control"
                    value="<?= $_GET['end_date'] ?? '' ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label" style= "margin-bottom: 0px;">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select">
                    <option value="">All Customers</option>

                    <?php while($customer = mysqli_fetch_assoc($customers)) { ?>
                        <option value="<?= $customer['party_id']; ?>"
                            <?= ($customer_id == $customer['party_id']) ? 'selected' : ''; ?>>
                            <?= $customer['party_name']; ?>
                        </option>
                    <?php } ?>
                </select>
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

                    <th class="text-end">Sales Qty</th>

                    <th class="text-end">Sales Amount</th>


                </tr>

            </thead>

            <tbody>

            <?php

            $i = 1;

           

            $totalSalesQty = 0;
            $totalSalesAmount = 0;

            $totalClosingQty = 0;
            $totalClosingAmount = 0;

            while($row = mysqli_fetch_assoc($sales))
            {
               
                $totalSalesQty += $row['sales_qty'];
                $totalSalesAmount += $row['sales_amount'];


            ?>

                <tr>

                    <td><?= $i++; ?></td>

                    <td><?= $row['product_name']; ?></td>

                    <td class="text-end"><?= $row['sales_qty']; ?></td>

                    <!-- <td class="text-end"><?= $row['sales_rate']; ?></td> -->

                    <td class="text-end">
                        ₹ <?= number_format($row['sales_amount'],2); ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

            <tfoot class="table-dark">

                <tr>

                    <th colspan="2" class="text-end">
                        Grand Total
                    </th>

                    <th class="text-end">
                        <?= number_format($totalSalesQty,2); ?>
                    </th>
                    
                    <th class="text-end">
                        ₹ <?= number_format($totalSalesAmount,2); ?>
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

var startDate = $('#start_date').val();
var endDate = $('#end_date').val();

    var table = $('#reportTable').DataTable({

        pageLength: 25,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],

        dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>' +
             'rt' +
             '<"row mt-3"<"col-md-6"B><"col-md-6 text-end"ip>>',

        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                footer: true,
                title: 'Sales Report',
                filename: 'Sales_Report_' + startDate + '_to_' + endDate,
                messageTop: 'Period: ' + startDate + ' to ' + endDate,
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]

    });

});
</script>