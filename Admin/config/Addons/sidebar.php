<style>
      .img-thumbnail {
      padding: 0px;
      background-color: var(--bs-body-bg);
      border: 0px;
      border-radius: var(--bs-border-radius);
      max-width: 45%;
      height: auto;
      justify-content: center;
      margin-left: 55px;
      }
      .rounded-circle {
      border-radius: 0px !important;
      }
      .sidebar-logo {
      padding: 8px;
      background-color: #fff;
      }


      .toggle-icon{
         transition: transform .3s ease;
      }

      .toggle-icon.rotate{
         transform: rotate(180deg);
      }

</style>
    
<?php

$currentPage = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$currentPage = basename($currentPage);

$inventoryPages = [
    'products',
    'purchase_inward',
    'sales_entry'
];

$isInventoryOpen = in_array($currentPage, $inventoryPages);


$reportPages = [
    'sales_report',
    'purchase_report',
    'stock_report',
    'stock_report_combine'
];

$isReportOpen = in_array($currentPage, $reportPages);
?>

     <aside id="sidebar">
          <div class="h-100 bg-dark">
              <div class="sidebar-logo">
                <a href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>config/image/logo-sau.jpg" class="img-thumbnail rounded-circle" alt="..." width="500" height="236">
                </a>
             </div>
             <hr class="sidebar-divider my-0">
             <!-- sidebar navigation -->
             <ul class="sidebar-nav">
         
                   <ul class="nav nav-pills flex-column mt-4">

                   <!-- parties -->

                      <li class="nav-item">
                           <a href="<?= BASE_URL ?>parties" class="nav-link text-light report-link ">
                              <i class="fa-solid fa-warehouse" style= "margin-right: 10px;"></i>
                                   Party Master
                           </a>
                     </li> 
                      <!-- for Inventory  -->
                      <li class="nav-item">

                        <a class="nav-link text-white d-flex justify-content-between align-items-center <?= $isInventoryOpen ? '' : 'collapsed'; ?>"
                           data-bs-toggle="collapse"
                           data-bs-target="#inventoryMenu"
                           aria-expanded="<?= $isInventoryOpen ? 'true' : 'false'; ?>">

                           <span>
                                 <i class="fa-solid fa-warehouse"></i>
                                 <span class="ms-2">Inventory</span>
                           </span>

                           <i class="fa-solid fa-angle-down toggle-icon"></i>

                        </a>

                        <div class="collapse <?= $isInventoryOpen ? 'show' : ''; ?>" id="inventoryMenu">

                           <ul class="nav flex-column ms-4 mt-2">

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>products"
                                       class="nav-link text-light <?= $currentPage == 'products' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-box"></i>
                                       Product Master
                                    </a>
                                 </li>

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>purchase_inward"
                                       class="nav-link text-light <?= $currentPage == 'purchase_inward' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-cubes-stacked"></i>
                                       Purchase Entry
                                    </a>
                                 </li>

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>sales_entry"
                                       class="nav-link text-light <?= $currentPage == 'sales_entry' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-cart-shopping"></i>
                                       Sales Entry
                                    </a>
                                 </li>

                           </ul>

                        </div>

                     </li>
                     
                    <li class="nav-item">

                        <a class="nav-link text-white d-flex justify-content-between align-items-center <?= $isReportOpen ? '' : 'collapsed'; ?>"
                           data-bs-toggle="collapse"
                           data-bs-target="#reportMenu"
                           aria-expanded="<?= $isReportOpen ? 'true' : 'false'; ?>">

                           <span>
                                 <i class="fa-solid fa-chart-line"></i>&nbsp;&nbsp;
                                 <span class="ms-2">Reports</span>
                           </span>

                           <i class="fa-solid fa-angle-down toggle-icon"></i>

                        </a>

                        <div class="collapse <?= $isReportOpen ? 'show' : ''; ?>" id="reportMenu">

                           <ul class="nav flex-column ms-4 mt-2">

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>sales_report"
                                       class="nav-link text-light report-link <?= $currentPage == 'sales_report' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-file-invoice-dollar"></i>&nbsp;&nbsp;
                                       Sales Report
                                    </a>
                                 </li>

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>purchase_report"
                                       class="nav-link text-light report-link <?= $currentPage == 'purchase_report' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-file-import"></i>&nbsp;&nbsp;
                                       Purchase Report
                                    </a>
                                 </li>

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>stock_report"
                                       class="nav-link text-light report-link <?= $currentPage == 'stock_report' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-boxes-stacked"></i>&nbsp;&nbsp;
                                       Stock & Sales Report
                                    </a>
                                 </li>

                                 <li class="nav-item">
                                    <a href="<?= BASE_URL ?>stock_report_combine"
                                       class="nav-link text-light report-link <?= $currentPage == 'stock_report_combine' ? 'active' : ''; ?>">
                                       <i class="fa-solid fa-boxes-stacked"></i>&nbsp;&nbsp;
                                       Stock & Sale Combined Report
                                    </a>
                                 </li>

                           </ul>

                        </div>

                  </li> 

                    <div class="mt-auto p-3 border-top">

                           <a href="<?= BASE_URL ?>login/logout"
                              class="btn btn-outline-danger w-100 rounded-pill">

                              <i class="fa-solid fa-right-from-bracket me-2"></i>
                              Sign Out

                           </a>

                   </div>
                 </ul>
             </ul>
          </div>
     </aside>
     <!-- Main component -->
     <div class="main">
        <nav class="navbar  px-3 bg-dark border-bottom navbar-fixed-top">
           <!-- button for sidebar toggle -->
            <button class="btn btn-light" type="button"  data-bs-theme="light">
            <span class="navbar-toggler-icon"></span>
            </button>
            <a href="<?= BASE_URL ?>" style="text-decoration: none;">
            <h5 class="admin-txt" style="color:#fff;"> 
              
            </h5>
            </a>
        </nav>
       
     



   
    <!-- <script src="Addons/script.js"></script> -->

    <script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(btn){

        const icon = btn.querySelector('.toggle-icon');
        const targetSelector = btn.getAttribute('data-bs-target');
        const target = document.querySelector(targetSelector);

        if (!icon || !target) return;

        target.addEventListener('show.bs.collapse', function () {
            icon.classList.remove('fa-angle-down');
            icon.classList.add('fa-angle-up');
        });

        target.addEventListener('hide.bs.collapse', function () {
            icon.classList.remove('fa-angle-up');
            icon.classList.add('fa-angle-down');
        });

    });

});
</script>


