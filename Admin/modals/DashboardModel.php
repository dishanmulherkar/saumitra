<?php
class DashboardModel {
    private $db;

    public function __construct($con) {
        $this->db = $con;
    }

    public function TotalSupplier()
    {
        $sql = "SELECT COUNT(*) AS total_supplier FROM parties  where party_type = 'Supplier'";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($result);
     }

     public function TotalCustomer()
    {
        $sql = "SELECT COUNT(*) AS total_customer FROM parties  where party_type = 'Customer'";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($result); 
    }

    public function TotalProduct()
    {
        $sql = "SELECT COUNT(*) AS total_product FROM products ";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($result); 
    }
}
?>