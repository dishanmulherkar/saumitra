<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'config/db.php';


// include 'config/Addons';
$url = trim($_GET['url'] ?? 'dashboard', '/');

$parts = explode('/', $url);

$page   = $parts[0] ?? 'dashboard';
$action = $parts[1] ?? 'index';
$id     = $parts[2] ?? null;

$currentPage = $url;

$publicRoutes = ['login'];

// Not logged in → allow only login pages
if (!isset($_SESSION['admin_id']) && !in_array($page, $publicRoutes)) {
    header("Location: " . BASE_URL . "login");
    exit;
}

// Already logged in → don't allow login page, but allow logout
if (
    isset($_SESSION['admin_id']) &&
    $page == 'login' &&
    $action != 'logout'
) {
    header("Location: " . BASE_URL . "dashboard");
    exit;
}


switch($page)
{
    case 'login':
        include 'controller/LoginController.php';

        $controller =
            new LoginController($con);

         if (method_exists($controller, $action)) {
        $controller->$action($id);
            } else {
                $controller->index();
            }

        break;

    case 'admin':

        include 'controller/AdminController.php';

        $controller =
            new AdminController($con);

         if (method_exists($controller, $action)) {
        $controller->$action($id);
            } else {
                $controller->index();
            }

        break;

    case 'purchase_inward':

        include 'controller/PurchaseInwardController.php';

        $controller =
            new PurchaseInwardController($con);

         if (method_exists($controller, $action)) {
        $controller->$action($id);
            } else {
                $controller->index();
            }

        break;


    case 'products':
        include 'controller/ProductController.php';
        $controller = new ProductController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;

   

    case 'parties':
        include 'controller/PartyController.php';
        $controller = new PartyController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;    

    case 'sales_entry':
        include 'controller/SalesEntryController.php';
        $controller = new SalesEntryController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;   

     case 'sales_report':
        include 'controller/SalesReportController.php';
        $controller = new SalesReportController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;   

    case 'purchase_report':
        include 'controller/PurchaseReportController.php';
        $controller = new PurchaseReportController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;   

    case 'stock_report':
        include 'controller/StockReportController.php';
        $controller = new StockReportController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;   

    case 'stock_report_combine':
        include 'controller/StockSaleCombineReportController.php';
        $controller = new StockSaleCombineReportController($con);
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;   


    case 'dashboard':
    default:
        // Fixed folder name from 'controllers' to 'controller'
        include 'controller/DashboardController.php'; 
        
        // Passing $con so your dashboard can fetch database stats
        $controller = new DashboardController($con); 
        
        if (method_exists($controller, $action)) {
            $controller->$action($id);
        } else {
            $controller->index();
        }
        break;
}