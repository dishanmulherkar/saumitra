<?php

class StockReportModel
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
                    sl.batch_no,

                    /* Opening Stock */
                    SUM(
                        CASE
                            WHEN sl.created_at < '$start_date 00:00:00'
                            THEN sl.stock_in - sl.stock_out
                            ELSE 0
                        END
                    ) AS opening_qty,

                    ROUND(
                        (
                            SUM(
                                CASE
                                    WHEN sl.created_at < '$start_date 00:00:00'
                                    THEN sl.stock_in - sl.stock_out
                                    ELSE 0
                                END
                            )
                        ) *
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
                        2
                    ) AS opening_amount,

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
                                2
                            ),
                        0) AS purchase_rate,

                    /* Sales Rate */
                    IFNULL(
                        MAX(
                            CASE
                                WHEN sl.transaction_type='SALE'
                                AND sl.created_at BETWEEN '$start_date 00:00:00'
                                                    AND '$end_date 23:59:59'
                                THEN sl.rate
                            END
                        ),
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
                    ROUND(
                        (
                            SUM(
                                CASE
                                    WHEN sl.created_at <= '$end_date 23:59:59'
                                    THEN sl.stock_in - sl.stock_out
                                    ELSE 0
                                END
                            )
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
                        2
                    ) AS closing_amount

                FROM stock_ledger sl

                INNER JOIN products p
                    ON p.p_id = sl.product_id

                WHERE
                    sl.batch_no IS NOT NULL
                    AND sl.batch_no <> ''

                GROUP BY
                    sl.product_id,
                    sl.batch_no

                HAVING
            opening_qty <> 0
            OR purchase_qty <> 0
            OR sales_qty <> 0
            OR closing_qty <> 0

                ORDER BY
                    p.product_name,
                    sl.batch_no

            ");
        }

}