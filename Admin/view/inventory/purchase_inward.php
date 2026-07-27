 <?php 
// 3. Include the bottom layout and scripts
include 'view/layout/header.php'; 
?>
 <link rel="stylesheet" href="<?= BASE_URL ?>config/Addons/purchase.css">
<style>
        .detail {
            display: flex;
            justify-content: flex-end; 
            padding: 6px; 
        }

        /* Remove number input arrows */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

.purchase-table {
    font-size: 13px;
}

.purchase-table th,
.purchase-table td {
    padding: 3px 6px;
    vertical-align: middle;
}

.purchase-table .form-control,
.purchase-table .form-select {
    height: 28px;
    padding: 2px 6px;
    font-size: 13px;
}

.purchase-table .btn {
    height: 26px;
    min-width: 26px;
    padding: 2px 6px;
    font-size: 12px;
}

.purchase-table .btn i {
    font-size: 11px;
}
</style>
 
<div id="container">
                    <hr style="margin-top: 10px; margin-bottom: 10px; border-top: 1px solid #333;">               
                    <!-- ===  Flash Messages  === -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Product saved successfully!</div>
                    <?php endif; ?>
                     <?php if (isset($_GET['update'])): ?>
                        <div class="alert alert-success">Product update successfully!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['deleted'])): ?>
                        <div class="alert alert-info">Product deleted successfully!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Something went wrong. Please try again.</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['duplicate'])): ?>
                        <div class="alert alert-warning">Product name already exists!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['toggled'])): ?>
                        <div class="alert alert-info">Product status updated.</div>
                    <?php endif; ?>


    <div class="purchase-card">
        <?php $isEdit = isset($getPurchase['purchase_id']); ?>

            <form action="<?= $isEdit ? BASE_URL . 'purchase_inward/update/' . $getPurchase['purchase_id'] : BASE_URL . 'purchase_inward/store'; ?>" method="POST">

                <?php if ($isEdit): ?>
                    <input type="hidden" name="purchase_id" value="<?= $getPurchase['purchase_id']; ?>">
                <?php endif; ?>

                <div class="purchase-header d-flex align-items-center gap-4 flex-wrap mb-3">

                    <!-- Title -->
                    <h4 class="mb-0 me-3">
                        <i class="fa-solid fa-cart-plus"></i>
                        <?= $isEdit ? 'Edit Purchase' : 'Purchase Entry'; ?>
                    </h4>

                    <!-- Supplier -->
                    <div class="d-flex align-items-center">
                        <label class="me-2 mb-0 fw-semibold">Supplier</label>

                        <select class="form-select supplier_id"
                                name="supplier_id"
                                style="width:280px;"
                                <?= $isEdit ? 'disabled' : ''; ?>
                                required>

                            <option value="">Select Supplier</option>

                            <?php foreach ($Parties as $supplier) { ?>
                                <option value="<?= $supplier['party_id']; ?>"
                                    <?= (isset($getPurchase['party_id']) && $getPurchase['party_id'] == $supplier['party_id']) ? 'selected' : ''; ?>>
                                    <?= $supplier['party_name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <?php if($isEdit){ ?>
                            <input type="hidden"
                                name="supplier_id"
                                value="<?= $getPurchase['party_id']; ?>">
                        <?php } ?>
                    </div>

                    <!-- Purchase Date -->
                    <div class="d-flex align-items-center">
                        <label class="me-2 mb-0 fw-semibold">Date</label>

                        <input type="date"
                            id="purchase_date"
                            name="purchase_date"
                            class="form-control purchase_date"
                            style="width:170px;"
                            max="<?= date('Y-m-d'); ?>"
                            value="<?= isset($getPurchase['purchase_date']) ? date('Y-m-d', strtotime($getPurchase['purchase_date'])) : date('Y-m-d'); ?>"
                            <?= $isEdit ? 'readonly' : ''; ?>
                            required>
                    </div>

                    <!-- Submit Button -->
                    <div style="display: flex; gap: 8px; margin-left: auto;">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i>
                            <?= $isEdit ? 'Update Purchase' : 'Submit Purchase'; ?>
                        </button>
                        
                        <button type="button" class="btn btn-secondary cancel-edit-btn" style="display: none;">
                            <i class="fa fa-times"></i>
                            Cancel Edit
                        </button>
                    </div>

                </div>
                <!-- Purchase Table -->
                <div class="table-responsive">

                    <table class="table table-bordered align-middle purchase-table">

                        <thead>
                            <tr>
                                <th style="width:20%">Product</th>
                                <th>Batch</th>
                                <th>Qty</th>
                                <th>Purchase Rate</th>
                                <th>Amount</th>
                                <th style="width:80px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Entry Row -->
                            <tr class="entry-row">

                                <td>
                                    <select class="form-select form-select-sm product select2">
                                        <option value="">Select Product</option>
                                        <?php 
                                        foreach ($Products as $product) {
                                            echo "<option value='{$product['p_id']}'>{$product['product_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </td>

                                <td  class="text-center">
                                    <input class="form-control form-control-sm batch"
                                        id="batch_no"
                                        readonly>
                                </td>

                                <td>
                                    <input type="number"
                                        class="form-control form-control-sm qty"
                                        step="0.001"
                                        min="0">
                                </td>

                                <td>
                                    <input type="number" class="form-control form-control-sm rate">
                                </td>

                                <td>
                                    <input class="form-control form-control-sm amount text-end"
                                        value="0.00"
                                        readonly>
                                </td>
                                    <input type="hidden" name="total_qty" id="total_qty">
                                    <input type="hidden" name="grand_total" id="grand_total">
                                    
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-success btn-sm add-row">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <button type="button"
                                            id="cancelEdit"
                                            class="btn btn-secondary"
                                            style="display:none;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Edit Row Added  -->
                            <?php if (!empty($saleDetails)): ?>
                                <?php foreach ($saleDetails as $d): $sold = $soldQty[$d['product_id']] ?? 0; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($d['product_name']) ?>
                                            <input type="hidden" name="product_id[]" value="<?= $d['product_id'] ?>">
                                        </td>
                                        <td><?= htmlspecialchars($getPurchase['batch_no']) ?>
                                            <input type="hidden" name="batch[]" value="<?= $getPurchase['batch_no'] ?>">
                                        </td>
                                        <td><?= $d['qty'] ?>
                                            <input type="hidden" name="qty[]" value="<?= $d['qty'] ?>" data-sold="<?= $sold ?>">
                                        </td>
                                        <td><?= $d['purchase_rate'] ?>
                                            <input type="hidden" name="rate[]" value="<?= $d['purchase_rate'] ?>">
                                        </td>
                                        <td class="fw-bold amount-cell"><?= number_format($d['amount'], 2) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm edit-row"><i class="fa fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div> 

            </form>
            <!-- Footer -->
            <div class="purchase-footer">
                <div>
                    <h6>Total Qty</h6>
                    <h3>0</h3>
                </div>
                <div>
                    <h6>Grand Total</h6>
                    <h3>₹ 0.00</h3>
                </div>
            </div>
    </div>
</div>
<?php 
include 'view/layout/footer.php'; 
?>

<script>
let currentBatch = "";
function updateBatch() {
    let date = new Date($('#purchase_date').val());

    if (!isNaN(date)) {
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let year = String(date.getFullYear()).slice(-2);

        currentBatch = day + month + year;

        $('.batch').val(currentBatch).trigger('change');
    }
}

$('#purchase_date').on('change', function () {
    updateBatch();          // When user changes date
});

const today = new Date();

const minDate = new Date();
minDate.setDate(today.getDate() - 45);

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

$('.purchase_date').attr({
    min: formatDate(minDate),
    max: formatDate(today)
});

$(document).on('input', '.qty', function () {

    let value = $(this).val();

    if (value.indexOf('.') !== -1) {
        let parts = value.split('.');

        if (parts[1].length > 3) {
            $(this).val(parts[0] + '.' + parts[1].substring(0, 3));
        }
    }

});

$('.product').on('select2:select', function () {
    $('.qty').focus();
});

$(document).on('keydown', '.qty', function(e){
    if(e.key === 'Enter'){
        e.preventDefault();
        $(this).closest('tr').find('.rate').focus();
    }
});

$(document).on('keydown', '.rate', function(e){
    if(e.key === 'Enter'){
        e.preventDefault();
        $(this).closest('tr').find('.add-row').click();
    }
});

// Auto-hide messages
setTimeout(function(){

    $('.alert').fadeOut('slow');
    let url = new URL(window.location.href);
    url.searchParams.delete('success');
    url.searchParams.delete('error');

    window.history.replaceState({}, document.title, url.pathname);

},5000);

$(document).ready(function(){


// Global variables
let editing = false;
let editingRow = null;
    updateBatch();

    // calculate amount
    $(document).on('keyup','.qty,.rate',function(){

        let row = $(this).closest('tr');
        let qty  = parseFloat(row.find('.qty').val()) || 0;
        let rate = parseFloat(row.find('.rate').val()) || 0;
        let amount = qty * rate;
        row.find('.amount').val(amount.toFixed(2));
        calculateTotal();

    });

    // Show/hide the "already sold" warning while typing qty in the entry row
    $(document).on('input','.entry-row .qty', function () {
        let sold = parseFloat($(this).attr('data-sold')) || 0;
        let val  = parseFloat($(this).val()) || 0;

        $(this).closest('td').find('.sold-warning').remove();

        if (sold > 0 && val < sold) {
            $(this).closest('td').append(
                '<small class="text-danger sold-warning d-block">Already sold: ' + sold + '. Qty can\'t go below this.</small>'
            );
        }
    });

    // Add Product Row
   $(document).on('click','.add-row',function(){

        let row = $('.entry-row');
        let product = row.find('.product').val();
        let productName = row.find('.product option:selected').text();
        let batch = row.find('.batch').val();
        let qty = row.find('.qty').val();
        let rate = row.find('.rate').val();
        let amount = row.find('.amount').val();
        let sold = parseFloat(row.find('.qty').attr('data-sold')) || 0;

        if(product==""){
            alert("Please select product");
            return;
        }

        if(batch==""){
            alert("Please select purchase date to generate batch number");
            return;
        }

        if(qty=="" || rate==""){
            alert("Enter Qty and Rate");
            return;
        }

        if(parseFloat(qty) < sold){
            alert("Cannot reduce below already sold quantity ("+sold+").");
            return;
        }

        // Duplicate check only while adding
        if(!editing)
        {
            let exists = false;

            $('.purchase-table tbody tr').not('.entry-row').each(function(){

                let oldProduct = $(this).find('input[name="product_id[]"]').val();

                if(oldProduct == product)
                {
                    exists = true;
                    return false;
                }

            });

            if(exists)
            {
                alert("This product is already in the cart.");
                return;
            }
        }

        let html = `
        <tr>
            <td> ${productName} <input type="hidden" name="product_id[]" value="${product}"> </td>

            <td> ${batch} <input type="hidden" name="batch[]" value="${batch}"> </td>

            <td> ${qty} <input type="hidden" name="qty[]" value="${qty}" data-sold="${sold}"> </td>

            <td> ${rate} <input type="hidden" name="rate[]" value="${rate}"> </td>

            <td class="fw-bold amount-cell"> ${amount}</td>

            <td class="text-center">
                <button type="button" class="btn btn-primary btn-sm edit-row">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm delete-row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>

        </tr>
        `;

        //==========================
        // Add OR Update
        //==========================

        if(editing)
        {

            editingRow.replaceWith(html);
            editing = false;
            editingRow = null;
               row.find('.product').prop('disabled', true).trigger('change.select2');
            $('#cancelEdit').hide();
        }
        else
        {
            $('.purchase-table tbody').append(html);
        }
        //==========================
        // Clear Entry
        //==========================
        row.find('.product').val('').trigger('change');
        row.find('.batch').empty().trigger('change');
        row.find('.qty').val('');
        row.find('.rate').val('');
        row.find('.amount').val('0.00');
        row.find('.qty').attr('data-sold',0);
        row.find('.sold-warning').remove();

        calculateTotal();

        setTimeout(function(){
            row.find('.product').select2('open');
        },100);

    });

    // Edit Row
    $(document).on('click', '.edit-row', function () {

        if (editing) {
            alert("Please Save or Cancel the current edit first.");
            return;
        }

        editing = true;
        editingRow = $(this).closest('tr');
        let row = $('.entry-row');
        let sold = parseFloat(editingRow.find('input[name="qty[]"]').attr('data-sold')) || 0;
        row.find('.qty').attr('data-sold', sold);
        row.find('.product').val(editingRow.find('input[name="product_id[]"]').val()).trigger('change');

        setTimeout(function () {
            row.find('.batch').val(editingRow.find('input[name="batch[]"]').val()).trigger('change');
            row.find('.qty').val(editingRow.find('input[name="qty[]"]').val());
            row.find('.rate').val(editingRow.find('input[name="rate[]"]').val());
            row.find('.amount').val(
                (
                    parseFloat(row.find('.qty').val()) *
                    parseFloat(row.find('.rate').val())
                ).toFixed(2)
            );
        },300);
        editingRow.hide();
        $('#cancelEdit').show();
    });
    // Cancel Row edited
    $('#cancelEdit').click(function(){

        if(editingRow)
        {
            editingRow.show();
        }
        editing=false;
        editingRow=null;

        $('.entry-row').find('input').val('');
        $('.product').val('').trigger('change');
        $('.batch').empty().trigger('change');
        $('#cancelEdit').hide();
    });

    // Prevent Selecting Product
  $('.product').on('select2:opening', function (e) {

    if (editing) {
        e.preventDefault();
        alert("You cannot change the product while editing. Please Save or Cancel first.");
    }

});


    // Delete Row
    $(document).on('click', '.delete-row', function () {

        let tr = $(this).closest('tr');

        let sold = parseFloat(
            tr.find('input[name="qty[]"]').attr('data-sold')
        ) || 0;

        let product = tr.find('td:first').text().trim();

        if (sold > 0) {
            alert(
                product +
                "\n\nAlready Sold: " + sold +
                "\n\nThis item cannot be deleted."
            );
            return;
        }

        if (confirm("Delete " + product + "?")) {
            tr.remove();
            calculateTotal();
        }

    });

    // Total Calculation
    function calculateTotal()
    {
        let totalQty = 0;
        let totalAmount = 0;
        $('.purchase-table tbody tr').not('.entry-row').each(function () {
            let qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            let rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            totalQty += qty;
            totalAmount += qty * rate;
        });
        $('.purchase-footer h3:eq(0)').text(totalQty);

        $('.purchase-footer h3:eq(1)').text("₹ "+totalAmount.toFixed(2));

        $('#total_qty').val(totalQty);
        $('#grand_total').val(totalAmount);
    }

    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Select Product",
        allowClear: true
    });

    $('form').on('submit',function(e){
        if($('.purchase-table tbody tr:not(.entry-row)').length == 0)
        {
            alert("Please add at least one product");
            e.preventDefault();
            return false;
        }
    });

    calculateTotal();
});
</script>


