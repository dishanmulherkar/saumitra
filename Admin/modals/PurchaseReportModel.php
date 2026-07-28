<?php

class PurchaseReportModel
{
    private $con;

    public function __construct()
    {
        global $con;      // or include your db connection
        $this->con = $con;
    }

    public function getReport($start_date = '', $end_date = '', $supplier_id = '')
    {
        $where = " WHERE 1=1 ";

        if (!empty($start_date) && !empty($end_date)) {
            $where .= " AND p.purchase_date BETWEEN '$start_date' AND '$end_date'";
        }

        // Supplier filter
        if (!empty($supplier_id)) {
            $where .= " AND pa.party_id = '$supplier_id'";
        }

        return mysqli_query($this->con, "
            SELECT
                p.purchase_id,
                p.batch_no,
                p.purchase_date,
                pa.party_name,
                p.total_qty,
                p.total_amount
            FROM purchase_entry p
            LEFT JOIN parties pa
                ON pa.party_id = p.supplier_id
            $where
            ORDER BY p.purchase_date DESC, p.purchase_id DESC
        ");
    }

     public function getSupplier()
    {
        $sql = "SELECT party_id, party_name
                FROM parties
                WHERE party_type = 'Supplier'
                ORDER BY party_name ASC";

        return mysqli_query($this->con, $sql);
    }


   public function getPurDetails($purchase_id)
    {
        $purchase_id = (int)$purchase_id;

        return mysqli_query($this->con,"
            SELECT
                d.*,
                p.product_name
            FROM purchase_entry_details d
            INNER JOIN products p
                ON p.p_id = d.product_id
            WHERE d.purchase_id = '$purchase_id'
            ORDER BY d.detail_id ASC
        ");
    }
}