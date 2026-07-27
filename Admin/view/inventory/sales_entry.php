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
<form method="POST"
      action="<?= isset($sale['s_id'])
            ? BASE_URL . 'sales_entry/update/' . $sale['s_id']
            : BASE_URL . 'sales_entry/store'; ?>">

        <!-- Hidden aggregate fields: kept as direct children of the form, -->
        <!-- NOT inside a <tr>, so the browser can't foster-parent them out. -->
        <input type="hidden" id="total_qty" name="total_qty" value="0">
        <input type="hidden" id="grand_total" name="grand_total" value="0">
        <input type="hidden" id="editing_sale_id" name="sale_id"
               value="<?= isset($sale['s_id']) ? (int)$sale['s_id'] : ''; ?>">

       <div class="purchase-header d-flex align-items-center gap-4 flex-wrap mb-3">

    <!-- Title -->
    <h4 class="mb-0 me-3">
        <i class="fa-solid fa-cart-plus"></i> Sales Entry
    </h4>

    <!-- Customer -->
    <div class="d-flex align-items-center">
        <label class="me-2 mb-0 fw-semibold">Customer</label>
        <select class="form-select supplier_id" name="supplier_id" style="width:280px;" required>
            <option value="">Select Customer</option>

            <?php foreach ($Parties as $supplier) { ?>
                <option value="<?= $supplier['party_id']; ?>"
                    <?= (isset($sale['party_id']) && $sale['party_id'] == $supplier['party_id']) ? 'selected' : ''; ?>>
                    <?= $supplier['party_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- Sales Date -->
    <div class="d-flex align-items-center">
        <label class="me-2 mb-0 fw-semibold">Date</label>
        <input type="date"
               id="sales_date"
               name="sales_date"
               class="form-control sales_date"
               style="width:170px;"
               max="<?= date('Y-m-d'); ?>"
               value="<?= isset($sale['sale_date']) ? date('Y-m-d', strtotime($sale['sale_date'])) : date('Y-m-d'); ?>"
               required>
    </div>

    <!-- Save Button -->
    <button type="submit" class="btn btn-success ms-auto">
        <i class="fa fa-save"></i>
        <?= isset($sale['s_id']) ? 'Update Sales' : 'Submit Sales'; ?>
    </button>

</div>
        <!-- Sales Table -->

        <div class="table-responsive">

            <table class="table table-bordered align-middle purchase-table">

                <thead>

                <tr>
                    <th style="width:20%">Product</th>
                    <th>Batch</th>
                    <th>Qty</th>
                    <th>Sales Rate</th>
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


                     <td>
                        <select class="form-select form-select-sm batch">
                            <option value="">Select Batch</option>
                        </select>
                    </td>


                    <td>
                    <input type="number" class="form-control form-control-sm qty " step="0.001" min="0">
                    </td>


                    <td>
                    <input type="number" class="form-control form-control-sm rate" min="0" step="0.01">
                    </td>


                    <td>
                    <input class="form-control form-control-sm amount text-end"
                        value="0.00"
                        readonly>
                    </td>

                    <td class="text-center">

                    <button type="button" 
                            class="btn btn-success btn-sm add-row">

                    <i class="fa fa-plus"></i>

                    </button>

                    </td>
                </tr>

                 <?php if (!empty($saleDetails)): ?>
                    <?php foreach ($saleDetails as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['product_name']) ?>
                                <input type="hidden" name="product_id[]" value="<?= $d['p_id'] ?>">
                            </td>
                            <td><?= htmlspecialchars($d['batch_no']) ?>
                                <input type="hidden" name="batch[]" value="<?= $d['batch_no'] ?>">
                            </td>
                            <td><?= $d['qty'] ?>
                                <input type="hidden" name="qty[]" value="<?= $d['qty'] ?>">
                            </td>
                            <td><?= $d['rate'] ?>
                                <input type="hidden" name="rate[]" value="<?= $d['rate'] ?>">
                            </td>
                            <td class="fw-bold amount-cell"><?= number_format($d['amount'], 2) ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm edit-row">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                    <i class="fa fa-trash"></i>
                                </button>
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
$(function () {

    // ------------------------------------------------------------------
    // Constants / date bounds
    // ------------------------------------------------------------------
    const today = new Date();
    const minDate = new Date();
    minDate.setDate(today.getDate() - 45);

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    $('.sales_date').attr({
        min: formatDate(minDate),
        max: formatDate(today)
    });

    // ------------------------------------------------------------------
    // Auto-hide flash messages
    // ------------------------------------------------------------------
    setTimeout(function () {
        $('.alert').fadeOut('slow');
        let url = new URL(window.location.href);
        url.searchParams.delete('success');
        url.searchParams.delete('error');
        window.history.replaceState({}, document.title, url.pathname);
    }, 5000);

    // ------------------------------------------------------------------
    // Select2 init
    // ------------------------------------------------------------------
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Select Product",
        allowClear: true
    });

    $(document).on('select2:select', '.product', function () {
        let batch = $(this).closest('tr').find('.batch');
        batch.focus();
        setTimeout(function () {
            batch.trigger('mousedown');
        }, 50);
    });

    $(document).on('change', '.batch', function () {
        $(this).closest('tr').find('.qty').focus();
    });

    $(document).on('keydown', '.qty', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('.rate').focus();
        }
    });

    $(document).on('keydown', '.rate', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('.add-row').click();
        }
    });

    // ------------------------------------------------------------------
    // Live stock ledger
    // ------------------------------------------------------------------
    // Tracks how much of each batch is already sitting in the cart, so the
    // "available" figure used for validation is the server's stock figure
    // MINUS whatever this cart has already committed to other rows.
    // This is rebuilt from the DOM every time the cart changes, so it can
    // never silently drift out of sync with what's actually in the table.
    let cartReserved = {};

    // Batch numbers are NOT guaranteed unique across different products
    // (different suppliers/lots commonly reuse the same batch number), so
    // the reservation key must include the product id, not just the batch.
    function reservationKey(productId, batchNo) {
        return productId + '|' + batchNo;
    }

    function rebuildCartReserved(excludeRowEl) {
        cartReserved = {};
        $('.purchase-table tbody tr').not('.entry-row').each(function () {
            if (excludeRowEl && this === excludeRowEl) return;
            let product = $(this).find('input[name="product_id[]"]').val();
            let batch = $(this).find('input[name="batch[]"]').val();
            let qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            if (batch) {
                let key = reservationKey(product, batch);
                cartReserved[key] = (cartReserved[key] || 0) + qty;
            }
        });
    }

    // Single source of truth for "how much of this batch, for this product,
    // is actually still free" — raw server stock minus whatever this cart
    // has already committed elsewhere. Used both for validation and for the
    // label text shown in the dropdown, so the two can never disagree.
    function computeAvailable(productId, batchNo, rawStock) {
        let reserved = cartReserved[reservationKey(productId, batchNo)] || 0;
        return Math.max((parseFloat(rawStock) || 0) - reserved, 0);
    }

    // Available stock for whatever batch is currently selected in the entry row.
    function getAvailableStock(entryRow) {
        let selected = entryRow.find('.batch option:selected');
        let raw = parseFloat(selected.data('raw-stock')) || 0;
        let productId = entryRow.find('.product').val();
        return computeAvailable(productId, selected.val(), raw);
    }

    // Rewrites every batch option's visible "(Available : X)" label so the
    // dropdown itself reflects what's already been added to the cart —
    // not just a value used silently behind the scenes for validation.
    function refreshBatchAvailability(entryRow) {
        let productId = entryRow.find('.product').val();
        entryRow.find('.batch option').each(function () {
            let opt = $(this);
            if (!opt.val()) return; // skip the placeholder option
            let raw = opt.data('raw-stock');
            if (raw === undefined) return;
            let avail = computeAvailable(productId, opt.val(), raw);
            opt.text(opt.val() + ' (Available : ' + avail + ')');
        });
    }

    // ------------------------------------------------------------------
    // Product change -> load batches
    // ------------------------------------------------------------------
    $(document).on('change', '.product', function () {
        let row = $(this).closest('tr');
        let product_id = $(this).val();
        let sale_id = $('#editing_sale_id').val();

        row.find('.batch').html('<option value="">Loading...</option>');
        row.find('.qty').val('');
        row.find('.rate').val('');
        row.find('.amount').val('0.00');

        if (product_id === "") {
            row.find('.batch').html('<option value="">Select Batch</option>');
            return;
        }

        $.ajax({
            url: "<?= BASE_URL ?>sales_entry/getBatches",
            type: "POST",
            data: { product_id: product_id, sale_id: sale_id },
            dataType: "json",
            success: function (res) {
                let html = '<option value="">Select Batch</option>';

                $.each(res, function (i, item) {
                    html += `
                    <option
                        value="${item.batch_no}"
                        data-raw-stock="${item.available_qty}"
                        data-purchase="${item.purchase_rate}">
                        ${item.batch_no} (Available : ${item.available_qty})
                    </option>`;
                });

                row.find('.batch').html(html);

                // Labels above show the raw server figure; adjust them down
                // for anything this cart has already reserved against this
                // product+batch before the user ever sees the dropdown.
                refreshBatchAvailability(row);

                // Restore edit values after AJAX completes
                if (row.data('editing')) {
                    row.find('.batch')
                        .val(row.data('batch'))
                        .trigger('change');

                    row.find('.qty').val(row.data('qty'));
                    row.find('.rate').val(row.data('rate'));

                    row.find('.amount').val(
                        (
                            (parseFloat(row.data('qty')) || 0) *
                            (parseFloat(row.data('rate')) || 0)
                        ).toFixed(2)
                    );

                    row.removeData('editing batch qty rate');
                }
            },
            error: function () {
                row.find('.batch').html('<option value="">Select Batch</option>');
                alert('Could not load batches for this product. Please try again.');
            }
        });
    });

    // Batch change -> suggest a sale rate from purchase rate
    $(document).on('change', '.batch', function () {
        let row = $(this).closest('tr');
        let purchase = parseFloat($(this).find(':selected').data('purchase')) || 0;
        let saleRate = purchase * 1.05;

        row.find('.rate').val(saleRate.toFixed(2));
        row.find('.qty').trigger('keyup');
    });

    // ------------------------------------------------------------------
    // Qty / Rate change -> recompute amount, clamp against live stock
    // ------------------------------------------------------------------
    function recalcRowAmount(row) {
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let rate = parseFloat(row.find('.rate').val()) || 0;
        let available = getAvailableStock(row);

        if (qty > available) {
            let availableDisplay = Math.round(available * 1000) / 1000; // trim float noise, keep up to 3 decimals
            alert("Only " + availableDisplay + " quantity available.");
            row.find('.qty').val(availableDisplay);
            qty = availableDisplay;
            row.find('.qty').focus().select();
        }

        let amount = qty * rate;
        row.find('.amount').val(amount.toFixed(2));
        return amount;
    }

    $(document).on('keyup change', '.qty, .rate', function () {
        let row = $(this).closest('tr');
        recalcRowAmount(row);
        calculateTotal();
    });

    // ------------------------------------------------------------------
    // Edit / Add / Delete row
    // ------------------------------------------------------------------
    let editRow = null;

    $(document).on('click', '.edit-row', function () {
        // Prevent editing another row before saving the current edit
        if (editRow && editRow[0] !== $(this).closest('tr')[0]) {
            alert("Please save or cancel the current edit first.");
            return;
        }

        editRow = $(this).closest('tr');

        let row = $('.entry-row');

        row.data({
            editing: true,
            batch: editRow.find('input[name="batch[]"]').val(),
            qty: editRow.find('input[name="qty[]"]').val(),
            rate: editRow.find('input[name="rate[]"]').val()
        });

        // Free up this row's reserved qty while it's being edited, so the
        // stock-available check doesn't count it against itself.
        rebuildCartReserved(editRow[0]);
        refreshBatchAvailability(row);

        row.find('.product')
            .val(editRow.find('input[name="product_id[]"]').val())
            .trigger('change');

        $('.add-row').html('<i class="fa fa-save"></i>');

        calculateTotal();
    });

    function isDuplicate(product, batch) {
        let exists = false;
        $('.purchase-table tbody tr').not('.entry-row').each(function () {
            if (editRow && $(this)[0] === editRow[0]) return true; // skip row being edited
            let oldProduct = $(this).find('input[name="product_id[]"]').val();
            let oldBatch = $(this).find('input[name="batch[]"]').val();
            if (oldProduct == product && oldBatch == batch) {
                exists = true;
                return false;
            }
        });
        return exists;
    }

    $(document).on('click', '.add-row', function () {
        let row = $('.entry-row');
        let product = row.find('.product').val();
        let productName = row.find('.product option:selected').text();
        let batch = row.find('.batch').val();
        let qty = row.find('.qty').val();
        let rate = row.find('.rate').val();

        setTimeout(function () {
            row.find('.product').select2('open');
        }, 100);

        if (product === "") { alert("Please select product"); return; }
        if (batch === "") { alert("Please select batch"); return; }
        if (qty === "" || rate === "") { alert("Enter Qty and Rate"); return; }

        qty = parseFloat(qty) || 0;
        rate = parseFloat(rate) || 0;

        if (qty <= 0) { alert("Quantity must be greater than 0"); return; }
        if (rate <= 0) { alert("Rate must be greater than 0"); return; }

        if (isDuplicate(product, batch)) {
            alert("This product is already in the cart. Please edit the existing row instead.");
            return;
        }

        let available = getAvailableStock(row);
        if (qty > available) {
            let availableDisplay = Math.round(available * 1000) / 1000;
            // alert("Only " + availableDisplay + " quantity available.");
            row.find('.qty').val(availableDisplay);
            qty = availableDisplay;
            row.find('.qty').focus().select();
            recalcRowAmount(row);
            calculateTotal();
            return;
        }

        let amount = (qty * rate).toFixed(2);

        let html = `
        <tr>
            <td>${productName}
                <input type="hidden" name="product_id[]" value="${product}">
            </td>
            <td>
                ${batch}
                <input type="hidden" name="batch[]" value="${batch}">
            </td>
            <td>
                ${qty}
                <input type="hidden" name="qty[]" value="${qty}">
            </td>
            <td>
                ${rate}
                <input type="hidden" name="rate[]" value="${rate}">
            </td>
            <td class="fw-bold amount-cell">${amount}</td>
            <td>
            <button type="button" class="btn btn-primary btn-sm edit-row">
            <i class="fa fa-edit"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm delete-row">
            <i class="fa fa-trash"></i>
            </button>
            </td>
        </tr>
        `;

        if (editRow) {
            editRow.replaceWith(html);
            editRow = null;
            row.removeData('editing batch qty rate');
            $('.add-row').html('<i class="fa fa-plus"></i>');
        } else {
            $('.purchase-table tbody').append(html);
        }

        // Clear entry row
        row.find('.product').val(null).trigger('change');
        row.find('.batch').html('<option value="">Select Batch</option>');
        row.find('.qty').val('');
        row.find('.rate').val('');
        row.find('.amount').val('0.00');

        rebuildCartReserved();
        refreshBatchAvailability(row);
        calculateTotal();
    });

    $(document).on('click', '.delete-row', function () {
        let row = $(this).closest('tr');

        // If the row being deleted is the one currently mid-edit,
        // reset edit state so the form doesn't get stuck.
        if (editRow && editRow[0] === row[0]) {
            editRow = null;
            $('.add-row').html('<i class="fa fa-plus"></i>');
            $('.entry-row').removeData('editing batch qty rate');
        }

        row.remove();
        rebuildCartReserved();
        refreshBatchAvailability($('.entry-row'));
        calculateTotal();
    });

    // ------------------------------------------------------------------
    // Totals — computed from the actual submitted qty[]/rate[] values,
    // never from on-screen text, so it can't drift out of sync.
    // ------------------------------------------------------------------
    function calculateTotal() {
        let totalQty = 0;
        let totalAmount = 0;

        $('.purchase-table tbody tr').not('.entry-row').each(function () {
            let qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            let rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            totalQty += qty;
            totalAmount += qty * rate;
        });

        $('.purchase-footer h3').eq(0).text(totalQty);
        $('.purchase-footer h3').eq(1).text("₹ " + totalAmount.toFixed(2));

        $('#total_qty').val(totalQty);
        $('#grand_total').val(totalAmount.toFixed(2));
    }

    $('form').on('submit', function (e) {
        if ($('.purchase-table tbody tr:not(.entry-row)').length === 0) {
            alert("Please add at least one product");
            e.preventDefault();
            return false;
        }
    });

    // Initial state
    rebuildCartReserved();
    calculateTotal();
});
</script>
