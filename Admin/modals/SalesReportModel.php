<?php

class SalesReportModel
{
    private $con;

    public function __construct()
    {
        global $con;      // or include your db connection
        $this->con = $con;
    }

      public function getSalesReport($start_date = '', $end_date = '')
    {

        $where = "";

        if(!empty($start_date) && !empty($end_date))
        {
            $where = " WHERE s.sale_date BETWEEN '$start_date' AND '$end_date' ";
        }

        return mysqli_query($this->con,"
            SELECT
                s.s_id,
                s.invoice_no,
                s.sale_date,
                p.party_name,
                s.total_amt
            FROM sales_entries s
            LEFT JOIN parties p
                ON p.party_id = s.c_id
                  $where
            ORDER BY s.sale_date DESC
        ");
    }

    public function getSaleDetails($sale_id)
    {
        $sale_id = (int)$sale_id;

        return mysqli_query($this->con,"
            SELECT
                d.*,
                p.product_name
            FROM `sales_details` d
            INNER JOIN products p
                ON p.p_id = d.p_id
            WHERE d.s_id = '$sale_id'
            ORDER BY d.sale_detail_id ASC
        ");
    }
}