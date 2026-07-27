<?php

include 'modals/PurchaseInwardModel.php';

class PurchaseInwardController
{
    private $model;

    public function __construct($con)
    {
        $this->model = new PurchaseEntryModel($con);
    }

    public function index()
    {
        $ROW = null;

        $Products = $this->model->getProducts();
        $Parties  = $this->model->getParties();

        include 'view/inventory/purchase_inward.php';
    }


       public function edit($sale_id)
    {
        $header = "Edit Sales";

        $Parties = $this->model->getParties();
        $Products = $this->model->getProducts($sale_id);
        $getPurchase       = $this->model->getPurchase($sale_id);
        $saleDetails  = $this->model->getSaleDetails($sale_id);
         $soldQty     = $this->model->getSoldQty($sale_id);

        include 'view/inventory/purchase_inward.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $result = $this->model->store($_POST);

            if ($result === true)
            {
                header('Location: ' . BASE_URL . 'purchase_inward?success=1');
                exit;
            }
            else
            {
                header('Location: ' . BASE_URL . 'purchase_inward?error=1&msg=' . urlencode($result));
                exit;
            }
        }
    }

  public function update()
{
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: ' . BASE_URL . 'purchase_inward');
        exit;
    }

    $result = $this->model->update($_POST);

    if ($result === true) {
        header('Location: ' . BASE_URL . 'purchase_inward?update=1');
    } else {
        header('Location: ' . BASE_URL . 'purchase_inward?error=1&msg=' . urlencode($result));
    }
    exit;
}
}