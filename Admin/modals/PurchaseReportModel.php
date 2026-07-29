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

    public function getPurchaseCombineReport($start_date = '', $end_date = '' ,$supplier_id = '')
    {
         $supplierCondition = "";

    if (!empty($supplier_id)) {
        $supplierCondition = " AND pe.supplier_id = '$supplier_id'";
    }
        $sql =  "
            SELECT
                p.product_name,
                /* Purchase Rate */
                IFNULL(
                    ROUND(
                        SUM(
                            CASE
                                WHEN sl.transaction_type='PURCHASE'
                                AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                    AND '$end_date 23:59:59'
                                                     $supplierCondition
                                THEN sl.amount
                                ELSE 0
                            END
                        )
                        /
                        NULLIF(
                            SUM(
                                CASE
                                    WHEN sl.transaction_type='PURCHASE'
                                    AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                        AND '$end_date 23:59:59'
                                                         $supplierCondition
                                    THEN sl.stock_in
                                    ELSE 0
                                END
                            ),
                            0
                        ),
                    2),
                0) AS purchase_rate,

                /* Purchase Qty */
                SUM(
                    CASE
                        WHEN sl.transaction_type='PURCHASE'
                        AND sl.created_at BETWEEN '$start_date 00:00:00'
                                            AND '$end_date 23:59:59'
                                             $supplierCondition
                        THEN sl.stock_in
                        ELSE 0
                    END
                ) AS purchase_qty,

                /* Purchase Amount */
                SUM(
                    CASE
                        WHEN sl.transaction_type='PURCHASE'
                        AND sl.created_at BETWEEN '$start_date 00:00:00'
                                            AND '$end_date 23:59:59'
                                             $supplierCondition
                        THEN sl.amount
                        ELSE 0
                    END
                ) AS purchase_amount
            FROM stock_ledger sl
            INNER JOIN products p
                ON p.p_id = sl.product_id

            LEFT JOIN purchase_entry pe
                ON pe.purchase_id = sl.ref_id
                AND sl.transaction_type = 'PURCHASE'
                
            GROUP BY
                sl.product_id
            HAVING
                purchase_qty <> 0
            ORDER BY
                p.product_name
        ";
        return mysqli_query($this->con,$sql);
    }
}