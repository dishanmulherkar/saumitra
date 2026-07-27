<?php 
$pageTitle = "Product Management";
include 'view/layout/header.php'; 
?>


        <div id="container">
                    <!-- Logout -->
                    <div class="detail">
                        <!-- <a href="<?= BASE_URL ?>login/logout">
                            <button type="button" class="btn btn-secondary btn-sm">Logout</button>
                        </a> -->
                    </div>
                    <hr style="margin-top:10px; margin-bottom:10px; border-top:1px solid #333;">

                    <!-- ===  Flash Messages  === -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Product saved successfully!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['deleted'])): ?>
                        <div class="alert alert-info">Product deleted successfully!</div>
                    <?php endif; ?>
                   <?php if (isset($_GET['error']) && $_GET['error'] != 'used'): ?>
                        <div class="alert alert-danger">
                            Something went wrong. Please try again.
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['duplicate'])): ?>
                        <div class="alert alert-warning">Product name already exists!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['toggled'])): ?>
                        <div class="alert alert-info">Product status updated.</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['imported'])): ?>
                        <div class="alert alert-success">
                            Import complete &mdash;
                            <strong><?php echo intval($_GET['imported']); ?></strong> product(s) added,
                            <strong><?php echo intval($_GET['skipped']); ?></strong> skipped (duplicates / invalid rows).
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['import_error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_GET['import_error'] === 'nofile'
                                ? 'No file selected.'
                                : 'Invalid file type — please upload a <strong>.csv</strong> or <strong>.xlsx</strong> file.'; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Section header with Import button -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="mb-0"><?php echo $ROW ? 'Edit' : 'Add'; ?> Product</h3>
                        <button type="button"
                                class="btn btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#importModal">
                            Import
                        </button>
                    </div>

                    <!-- =====================  FORM  ===================== -->
                    <form method="post"
                          action="<?= isset($ROW)
                            ? BASE_URL.'products/update/'.$ROW['p_id']
                            : BASE_URL.'products/store'; ?>">

                        <div class="container border px-3 py-3">
                            <div class="row">

                                <!-- Product Name -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="product_name" class="form-control"
                                            placeholder="Enter Product Name" required
                                            value="<?php echo $ROW ? htmlspecialchars($ROW['product_name']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="">Select Status</option>
                                                <option value="1" <?php echo ($ROW && $ROW['status'] == 1) ? 'selected' : ''; ?>>
                                                    Active
                                                </option>
                                                <option value="0" <?php echo ($ROW && $ROW['status'] == 0) ? 'selected' : ''; ?>>
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                            </div><!-- /row -->

                        <!-- Buttons -->
                        <div class="row">
                            <div class="py-2 px-3">
                                <button type="submit" name="save" class="btn btn-success btn-sm" style="width:130px;">
                                    <?php echo $ROW ? 'Update' : 'Save'; ?>
                                </button>
                                <?php if ($ROW): ?>
                                    <a href="<?= BASE_URL ?>products" class="btn btn-secondary btn-sm ml-2" style="width:130px;">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </form>
                    <!-- =================  /FORM  ================= -->

                    <!-- =================  TABLE  ================= -->
                    <div class="table_container table-responsive pt-4">
                        <table class="table table-hover" id="productTable">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="text-center" style="width:50px;">Sr.</th>
                                    <th>Product Name</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $key  = 1;
                               
                                while ($row = mysqli_fetch_assoc($list)):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $key; ?></td>
                                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>

                                    <td><?php if($row['status'] == 1){ echo "Active";  }else { echo "Inactive";  } ?></td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <!-- Edit -->
                                        <a href="<?= BASE_URL ?>products/edit/<?php echo $row['p_id']; ?>"
                                           class="btn btn-warning btn-sm ml-1" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <!-- Delete -->
                                        <a href="<?= BASE_URL ?>products/delete/<?php echo $row['p_id']; ?>"
                                           class="btn btn-danger btn-sm ml-1" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php $key++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- ===============  /TABLE  =============== -->

                </div><!-- /#container -->

<!-- =================  IMPORT MODAL  ================= -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog"
     aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Excel / CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="post" action="<?= BASE_URL ?>products/import" enctype="multipart/form-data">
                <div class="modal-body">

                    <!-- Column guide -->
                    <div class="alert alert-light border" style="font-size:13px; margin-bottom:15px;">
                        <strong><i class="fa-solid fa-circle-info mr-1"></i> Column Order</strong>
                        <small class="text-muted">(row 1 = header, skipped automatically)</small>
                        <table class="table table-bordered table-sm mt-2 mb-1">
                            <thead class="thead-light">
                                <tr>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>product_name</td>
                                </tr>
                            </tbody>
                        </table>
                        <small class="text-muted">
                            &bull; <em>status</em> column is optional — defaults to <strong>Active</strong><br>
                            &bull; Rows with duplicate product names are automatically skipped
                        </small>
                    </div>

                    <!-- File chooser -->
                    <div class="form-group">
                        <label><strong>Choose File</strong>
                            <small class="text-muted">&nbsp;Accepted: .xlsx &nbsp;|&nbsp; .csv</small>
                        </label>
                        <input type="file" name="import_file" class="form-control-file"
                               accept=".xlsx,.csv" required>
                    </div>

                    <!-- Sample download -->
                    <a href="<?= BASE_URL ?>products/downloadSample" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-download mr-1"></i> Download Sample CSV
                    </a>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Import
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- ===============  /IMPORT MODAL  =============== -->


<?php 
// 3. Include the bottom layout and scripts
include 'view/layout/footer.php'; 
?>

<script>
    // DataTable init
   new DataTable('#productTable', {
    layout: {
        topStart: {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    title: 'Product Master',
                    filename: 'Product_Master',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5] // Excludes Action column
                    }
                }
            ]
        }
    },
    pageLength: 25
});

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.display = 'none';
        });
    }, 5000);
</script>

