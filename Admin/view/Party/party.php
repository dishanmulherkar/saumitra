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
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Something went wrong. Please try again.</div>
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
                        <h3 class="mb-0"><?php echo $ROW ? 'Edit' : 'Add'; ?> Party</h3>
                        
                    </div>

                    <!-- =====================  FORM  ===================== -->
                    <form method="post"
                          action="<?= isset($ROW)
                            ? BASE_URL.'parties/update/'.$ROW['party_id']
                            : BASE_URL.'parties/store'; ?>">

                        <div class="container border px-3 py-3">
                            <div class="row">

                                <!-- Party Name -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Party Name</label>
                                        <input type="text" name="party_name" class="form-control"
                                            placeholder="Enter Party Name" required
                                            value="<?php echo $ROW ? htmlspecialchars($ROW['party_name']) : ''; ?>">
                                    </div>
                                </div>

                                 <!-- Type  -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Party Type</label>
                                        <select name="party_type" class="form-control" required>
                                            <option value="">Select Party Type</option>
                                            <option value="Supplier" <?= $ROW && $ROW['party_type'] == 'Supplier' ? 'selected' : '' ?>>Supplier</option>
                                            <option value="Customer" <?= $ROW && $ROW['party_type'] == 'Customer' ? 'selected' : '' ?>>Customer</option>
                                            <option value="Both" <?= $ROW && $ROW['party_type'] == 'Both' ? 'selected' : '' ?>>Both</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="1" <?= $ROW && $ROW['status'] == '1' ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= $ROW && $ROW['status'] == '0' ? 'selected' : '' ?>>Inactive</option>
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
                                    <a href="party" class="btn btn-secondary btn-sm ml-2" style="width:130px;">Cancel</a>
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
                                    <th>Party Name</th>
                                    <th>Party Type</th>
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
                                    <td><?php echo htmlspecialchars($row['party_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['party_type']); ?></td>
                                    <td>
                                        <?php if ($row['status'] == '1'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    <td class="text-center" style="white-space:nowrap;">
                                       
                                        <!-- Edit -->
                                        <a href="<?= BASE_URL ?>parties/edit/<?php echo $row['party_id']; ?>"
                                           class="btn btn-warning btn-sm ml-1" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <!-- Delete -->
                                    </td>
                                </tr>
                                <?php $key++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- ===============  /TABLE  =============== -->

                </div><!-- /#container -->
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
                    title: 'Party Master',
                    filename: 'Party_Master',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4] // Excludes Action column
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

