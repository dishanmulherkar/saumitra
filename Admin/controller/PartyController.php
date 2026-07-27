<?php
include 'modals/PartyModel.php';

class PartyController
{
    private $model;

    public function __construct($con)
    {
        $this->model = new PartyModel($con);
    }

    public function index()
    {
        $ROW  = null;
        $list = $this->model->getAll();
        include 'view/Party/party.php';
    }

    public function edit($id)
    {
        $ROW  = $this->model->getById($id);
        $list = $this->model->getAll();
        include 'view/Party/party.php';
    }

    public function store()
    {
        if (mysqli_num_rows($this->model->checkDuplicate($_POST['party_name'])) > 0) {
            header("Location: " . BASE_URL . "parties?duplicate=1");
            exit;
        }

        $status = in_array($_POST['status'], ['Active', 'Inactive']) ? $_POST['status'] : 'Active';
        $success = $this->model->insert($_POST, $status);

        header("Location: " . BASE_URL . "parties?success=created");
        exit;
    }

    public function update($id)
    {
        if (mysqli_num_rows($this->model->checkDuplicate($_POST['party_name'], $id)) > 0) {
            header("Location: " . BASE_URL . "parties?duplicate=1");
            exit;
        }

        $status = in_array($_POST['status'], ['Active', 'Inactive']) ? $_POST['status'] : 'Active';
        $success = $this->model->update($id, $_POST, $status);

        header("Location: " . BASE_URL . "parties?success=updated");
        exit;
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header("Location: " . BASE_URL . "parties?deleted=1");
        exit;
    }
   
}