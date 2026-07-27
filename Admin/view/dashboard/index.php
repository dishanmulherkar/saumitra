<?php 
// 1. Set the dynamic title for the header
$pageTitle = "Saumitra Dashboard"; 

// 2. Include the top layout
include 'view/layout/header.php'; 

?>
<div>
<h3>Dashboard Overview</h3>
 <a href="<?= BASE_URL ?>login/logout" style="float:right; margin-top: -30px;">
    <button type="button" class="btn btn-secondary btn-sm">Logout</button>
</a>
</div>

<div class="row mt-4">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Supplier</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php echo $TotalSupplier['total_supplier']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                           <?php echo $TotalProduct['total_product']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Customers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php echo $TotalCustomer['total_customer']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php 
// 3. Include the bottom layout and scripts
include 'view/layout/footer.php'; 
?>