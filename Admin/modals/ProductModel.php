<?php

class ProductModel
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function getAll()
    {
        return mysqli_query($this->con, "
            SELECT *
            FROM products
            ORDER BY product_name ASC
        ");
    }

    public function getById($id)
    {
        $id = intval($id);
        $res = mysqli_query($this->con, "SELECT * FROM products WHERE p_id = '$id'");
        return mysqli_fetch_assoc($res);
    }

    public function insert($data, $status)
    {
        $pn = mysqli_real_escape_string($this->con, trim($data['product_name']));
       

        // Insert without product_code
        $insert = mysqli_query($this->con, "
            INSERT INTO products
            (product_name, status)
            VALUES
            ('$pn','$status')
        ");

        if(!$insert)
        {
            return false;
        }

        return true;
    }

    public function update($id, $data, $status)
    {
        $id  = intval($id);
        $pn  = mysqli_real_escape_string($this->con, trim($data['product_name']));
        

        return mysqli_query($this->con, "
            UPDATE products SET 
                product_name='$pn', status='$status' 
            WHERE p_id = '$id'
        ");
    }

    public function delete($id)
    {
        $id = intval($id);
        // Check Sales
        $sales = mysqli_query($this->con,"
            SELECT COUNT(*) as total 
            FROM sales_details 
            WHERE p_id ='$id'
        ");
        $salesRow = mysqli_fetch_assoc($sales);

        // Check Purchase
        $purchase = mysqli_query($this->con,"
            SELECT COUNT(*) as total 
            FROM purchase_entry_details 
            WHERE product_id ='$id'
        ");
        $purchaseRow = mysqli_fetch_assoc($purchase);

        if($salesRow['total'] > 0 || $purchaseRow['total'] > 0)
        {
            return false;
        }

        // Safe delete

        return mysqli_query($this->con,"
            DELETE FROM products 
            WHERE p_id='$id'
        ");

    }
    public function checkDuplicate($name, $id = null)
    {
        $name = mysqli_real_escape_string($this->con, $name);
        $query = "SELECT p_id FROM products WHERE product_name = '$name'";
        if ($id) $query .= " AND p_id != '" . intval($id) . "'";
        return mysqli_query($this->con, $query);
    }

    public function insertProduct($pn,  $st)
    {
        $pn = mysqli_real_escape_string($this->con, $pn);
        

        return mysqli_query($this->con, "
            INSERT INTO products (product_name, status) 
            VALUES ('$pn', '$st')
        ");
    }

    public function exists($product_name)
    {
        $pn = mysqli_real_escape_string($this->con, $product_name);
        $res = mysqli_query($this->con, "SELECT p_id FROM products WHERE product_name = '$pn'");
        return mysqli_num_rows($res) > 0;
    }
}