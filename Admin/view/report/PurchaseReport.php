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
 .blue{
    --bs-table-color: #fff;
    --bs-table-bg: #0c3f19;
} 
</style>

<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header bg-black text-white">
            <h4>Purchase Report Bill Wise</h4>
        </div>
          <form method="GET" action="<?= BASE_URL ?>purchase_report_bill">
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

                     <div class="col-md-3">
                        <label class="form-label" style= "margin-bottom: 0px;">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select">
                            <option value="">All Supplier</option>

                            <?php while($supplier = mysqli_fetch_assoc($suppliers)) { ?>
                                <option value="<?= $supplier['party_id']; ?>"
                                    <?= ($supplier_id == $supplier['party_id']) ? 'selected' : ''; ?>>
                                    <?= $supplier['party_name']; ?>
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
    </form>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="reportTable">

                <thead class="blue">

                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i=1;
                $grandAmount = 0;
                while($row=mysqli_fetch_assoc($sales))
                {
              $grandAmount += $row['total_amount'];
               ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $row['batch_no']; ?></td>
                        <td><?= date('d-m-Y',strtotime($row['purchase_date'])); ?></td>
                        <td><?= $row['party_name']; ?></td>
                        <td>₹ <?= number_format($row['total_amount'],2); ?></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm sale-details"
                                    data-id="<?= $row['purchase_id']; ?>">
                                <i class="fa fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
                 <tfoot>
                    <tr class="table-dark">
                        <th colspan="4" class="text-end">
                            Grand Total
                        </th>
                        <th>₹ <?= number_format($grandAmount,2) ?></th>
                        <th></th>
                    </tr>
                    </tfoot>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="saleDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                 <a href="#" id="editSaleBtn" class="btn btn-warning btn-sm" style = "margin-bottom: 10px;">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="saleDetailsBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'view/layout/footer.php'; ?>

<script>
$('#reportTable').DataTable({
    pageLength: 25,
    lengthMenu: [
        [25, 50, 100],
        ['25', '50', '100']
    ],
    responsive: true
});

$(document).on('click','.sale-details',function(){

    let sale_id = $(this).data('id');
    

      $('#editSaleBtn').attr(
        'href',
        '<?= BASE_URL ?>purchase_inward/edit/' + sale_id
    );


    $.ajax({
        url : "<?= BASE_URL ?>purchase_report_bill/getPurDetails",
        type : "POST",
        data : {sale_id:sale_id},
        success:function(res)
        {
            $('#saleDetailsBody').html(res);
            $('#saleDetailsModal').modal('show');
        }

    });

});
</script>