<?php

class StockSaleCombineReportModel
{
    private $con;

    public function __construct()
    {
        global $con;      // or include your db connection
        $this->con = $con;
    }

   public function getStockSalesReport($start_date = '', $end_date = '')
{
    return mysqli_query($this->con, "

        SELECT

            p.product_name,

            /* Opening Qty */
            SUM(
                CASE
                    WHEN sl.created_at < '$start_date 00:00:00'
                    THEN sl.stock_in - sl.stock_out
                    ELSE 0
                END
            ) AS opening_qty,

            /* Opening Amount */
            IFNULL(
                ROUND(
                    SUM(
                        CASE
                            WHEN sl.created_at < '$start_date 00:00:00'
                            THEN sl.stock_in - sl.stock_out
                            ELSE 0
                        END
                    )
                    *
                    (
                        SUM(
                            CASE
                                WHEN sl.transaction_type='PURCHASE'
                                THEN sl.amount
                                ELSE 0
                            END
                        )
                        /
                        NULLIF(
                            SUM(
                                CASE
                                    WHEN sl.transaction_type='PURCHASE'
                                    THEN sl.stock_in
                                    ELSE 0
                                END
                            ),
                            0
                        )
                    ),
                2),
            0) AS opening_amount,

            /* Purchase Rate */
            IFNULL(
                ROUND(
                    SUM(
                        CASE
                            WHEN sl.transaction_type='PURCHASE'
                            AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                 AND '$end_date 23:59:59'
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
                                THEN sl.stock_in
                                ELSE 0
                            END
                        ),
                        0
                    ),
                2),
            0) AS purchase_rate,

            /* Sales Rate (Average) */
            IFNULL(
                ROUND(
                    SUM(
                        CASE
                            WHEN sl.transaction_type='SALE'
                            AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                 AND '$end_date 23:59:59'
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
                                THEN sl.stock_out
                                ELSE 0
                            END
                        ),
                        0
                    ),
                2),
            0) AS sales_rate,

            /* Purchase Qty */
            SUM(
                CASE
                    WHEN sl.transaction_type='PURCHASE'
                    AND sl.created_at BETWEEN '$start_date 00:00:00'
                                         AND '$end_date 23:59:59'
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
                    THEN sl.amount
                    ELSE 0
                END
            ) AS purchase_amount,

            /* Sales Qty */
            SUM(
                CASE
                    WHEN sl.transaction_type='SALE'
                    AND sl.created_at BETWEEN '$start_date 00:00:00'
                                         AND '$end_date 23:59:59'
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
                    THEN sl.amount
                    ELSE 0
                END
            ) AS sales_amount,

            /* Closing Qty */
            SUM(
                CASE
                    WHEN sl.created_at <= '$end_date 23:59:59'
                    THEN sl.stock_in - sl.stock_out
                    ELSE 0
                END
            ) AS closing_qty,

            /* Closing Amount */
            IFNULL(
                ROUND(
                    SUM(
                        CASE
                            WHEN sl.created_at <= '$end_date 23:59:59'
                            THEN sl.stock_in - sl.stock_out
                            ELSE 0
                        END
                    )
                    *
                    (
                        SUM(
                            CASE
                                WHEN sl.transaction_type='PURCHASE'
                                THEN sl.amount
                                ELSE 0
                            END
                        )
                        /
                        NULLIF(
                            SUM(
                                CASE
                                    WHEN sl.transaction_type='PURCHASE'
                                    THEN sl.stock_in
                                    ELSE 0
                                END
                            ),
                            0
                        )
                    ),
                2),
            0) AS closing_amount

        FROM stock_ledger sl

        INNER JOIN products p
            ON p.p_id = sl.product_id

        GROUP BY
            sl.product_id

        HAVING
            opening_qty <> 0
            OR purchase_qty <> 0
            OR sales_qty <> 0
            OR closing_qty <> 0

        ORDER BY
            p.product_name

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