<?php

class SalesReportProdWise
{
    private $con;

    public function __construct()
    {
        global $con;      // or include your db connection
        $this->con = $con;
    }

     public function getCustomers()
    {
        $sql = "SELECT party_id, party_name
                FROM parties
                WHERE party_type = 'Customer'
                ORDER BY party_name ASC";

        return mysqli_query($this->con, $sql);
    }

   public function getStockSalesReport($start_date = '', $end_date = '', $customer_id = '')
    {
        $customerWhere = "";

        if (!empty($customer_id)) {
            $customerWhere = " AND se.c_id = '$customer_id'";
        }
        $sql = "

            SELECT
                p.product_name,
                /* Sales Rate (Average) */
                IFNULL(
                    ROUND(
                        SUM(
                            CASE
                                WHEN sl.transaction_type='SALE'
                                AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                    AND '$end_date 23:59:59'
                                                     $customerWhere
                                THEN sl.amount
                                ELSE 0
                            END
                        )
                        /
                        NULLIF(
                            SUM(
                                CASE
                                    WHEN sl.transaction_type='SALE'
                                    AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                        AND '$end_date 23:59:59'
                                                         $customerWhere
                                    THEN sl.stock_out
                                    ELSE 0
                                END
                            ),
                            0
                        ),
                    2),
                0) AS sales_rate,

                /* Sales Qty */
                SUM(
                    CASE
                        WHEN sl.transaction_type='SALE'
                        AND sl.created_at BETWEEN '$start_date 00:00:00'
                                            AND '$end_date 23:59:59'
                                             $customerWhere
                        THEN sl.stock_out
                        ELSE 0
                    END
                ) AS sales_qty,

                /* Sales Amount */
                SUM(
                    CASE
                        WHEN sl.transaction_type='SALE'
                        AND sl.created_at BETWEEN '$start_date 00:00:00'
                                            AND '$end_date 23:59:59'
                                             $customerWhere
                        THEN sl.amount
                        ELSE 0
                    END
                ) AS sales_amount


            FROM stock_ledger sl

            INNER JOIN products p
                ON p.p_id = sl.product_id

            LEFT JOIN sales_entries se
                ON se.s_id = sl.ref_id
                AND sl.transaction_type = 'SALE'

            GROUP BY
                sl.product_id

            HAVING
             sales_qty <> 0

            ORDER BY
                p.product_name

        ";
        return mysqli_query($this->con,$sql);
    }
    
}