<?php

include 'modals/SalesEntryModel.php';

class SalesEntryController
{
    private $model;

    public function __construct($con)
    {
        $this->model = new SalesEntryModel($con);
    }

    public function index()
    {
        $ROW = null;

        $Products = $this->model->getProducts();
        $Parties  = $this->model->getParties();

        include 'view/inventory/sales_entry.php';
    }

    public function getBatches()
    {
        if(isset($_POST['product_id']))
        {
            $product_id = (int)$_POST['product_id'];
            $sale_id    = isset($_POST['sale_id']) && $_POST['sale_id'] !== '' 
                            ? (int)$_POST['sale_id'] 
                            : null;

            $result = $this->model->getBatches($product_id, $sale_id);

            $data = [];

            while($row = mysqli_fetch_assoc($result))
            {
                $data[] = $row;
            }

            echo json_encode($data);
            exit;
        }
    }


    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $result = $this->model->store($_POST);

            if ($result === true)
            {
                header('Location: ' . BASE_URL . 'sales_entry?success=1');
                exit;
            }
            else
            {
                header('Location: ' . BASE_URL . 'sales_entry?error=1&msg=' . urlencode($result));
                exit;
            }
        }
    }

    public function edit($sale_id)
    {
        $header = "Edit Sales";

        $Parties = $this->model->getParties();
        $Products = $this->model->getProducts($sale_id);

        $sale       = $this->model->getSale($sale_id);
        $saleDetails  = $this->model->getSaleDetails($sale_id);

        include 'view/inventory/sales_entry.php';
    }

    public function update($sale_id)
    {
        $result = $this->model->update($_POST, $sale_id);

        if ($result === true) {
            header("Location: " . BASE_URL . "sales_entry?success=1");
        } else {
            header("Location: " . BASE_URL . "sales_entry/edit/$sale_id?error=1");
        }
    }
}