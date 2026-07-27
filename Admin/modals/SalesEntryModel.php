<?php

class SalesEntryModel
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }
    public function getProducts($sale_id = 0)
    {
        $sale_id = (int)$sale_id;

        if($sale_id > 0)
        {
            return mysqli_query($this->con, "

                SELECT DISTINCT
                    p.p_id,
                    p.product_name

                FROM products p

                INNER JOIN stock s
                    ON s.product_id = p.p_id

                LEFT JOIN sales_details sd
                    ON sd.p_id = p.p_id
                    AND sd.s_id = '$sale_id'

                WHERE
                    p.status = '1'
                    AND
                    (
                        s.qty > 0
                        OR sd.s_id IS NOT NULL
                    )
                    AND s.batch_no IS NOT NULL
                    AND s.batch_no <> ''

                ORDER BY p.product_name ASC

            ");
        }

        return mysqli_query($this->con, "

            SELECT DISTINCT
                p.p_id,
                p.product_name

            FROM products p

            INNER JOIN stock s
                ON s.product_id = p.p_id

            WHERE
                p.status = '1'
                AND s.qty > 0
                AND s.batch_no IS NOT NULL
                AND s.batch_no <> ''

            ORDER BY p.product_name ASC

        ");
    }
    public function getParties()
    {
        return mysqli_query($this->con, "
            SELECT party_id, party_name
            FROM parties
            WHERE party_type='Customer'
            ORDER BY party_name ASC
        ");
    }

    public function getSaleDetails($sale_id)
{
    return mysqli_query($this->con,"
        SELECT
            sd.*,
            p.product_name
        FROM sales_details sd
        INNER JOIN products p
            ON p.p_id = sd.p_id
        WHERE sd.s_id='$sale_id'
        ORDER BY sd.sale_detail_id
    ");
}

    public function getSale($sale_id)
    {
        return mysqli_fetch_assoc(mysqli_query($this->con,"
            SELECT se.* , p.party_name,p.party_id
            FROM sales_entries se
            INNER JOIN parties p 
            ON se.c_id = p.party_id 
            WHERE s_id='$sale_id'
        "));
    }

      public function getBatches($product_id, $sale_id = 0)
        {
            $product_id = (int)$product_id;
            $sale_id    = (int)$sale_id;

            if($sale_id > 0)
            {
                return mysqli_query($this->con,"
                    SELECT
                        s.batch_no,
                        s.purchase_rate,
                        (s.qty + IFNULL(sd.qty,0)) AS available_qty

                    FROM stock s

                    LEFT JOIN sales_details sd
                        ON sd.p_id = s.product_id
                    AND sd.batch_no = s.batch_no
                    AND sd.s_id = '$sale_id'

                    WHERE s.product_id = '$product_id'
                    ORDER BY s.batch_no
                ");
            }

            return mysqli_query($this->con,"
                SELECT
                    batch_no,
                    purchase_rate,
                    qty AS available_qty
                FROM stock
                WHERE product_id='$product_id'
                AND qty > 0
                ORDER BY batch_no
            ");
        }

    public function store($post)
    {
        $supplier_id   = (int)$post['supplier_id'];
        $sales_date = mysqli_real_escape_string($this->con, $post['sales_date']);
        $remarks       = mysqli_real_escape_string($this->con, $post['remarks'] ?? '');

        $admin_id = isset($_SESSION['admin_id'])
            ? (int)$_SESSION['admin_id']
            : null;
        $total_qty    = 0;
        $total_amount = 0;

                    // Financial year or current year
            $year = date('Y', strtotime($sales_date));

            // Get last invoice
            $result = mysqli_query($this->con,"
                SELECT invoice_no
                FROM sales_entries
                ORDER BY s_id DESC
                LIMIT 1
            ");

            if(mysqli_num_rows($result) > 0)
            {
                $row = mysqli_fetch_assoc($result);

                $lastNo = (int)substr($row['invoice_no'], -5);

                $newNo = $lastNo + 1;
            }
            else
            {
                $newNo = 1;
            }

            $invoice_no = "SAL-".$year."-".str_pad($newNo,5,"0",STR_PAD_LEFT);

        mysqli_begin_transaction($this->con);

        try {

            // Calculate totals
            foreach ($post['product_id'] as $key => $product_id)
            {
                $qty    = (float)$post['qty'][$key];
                $rate   = (float)$post['rate'][$key];
                $amount = $qty * $rate;

                $total_qty    += $qty;
                $total_amount += $amount;
            }

            // Insert sales header
            $header = mysqli_query($this->con, "
                INSERT INTO `sales_entries`
                (
                    invoice_no,
                    c_id,
                    sale_date,
                    total_amt
                )
                VALUES
                (
                    '$invoice_no',
                    '$supplier_id',
                    '$sales_date',
                    '$total_amount'
                )
            ");

            if (!$header)
            {
                throw new Exception(mysqli_error($this->con));
            }

            $sales_id = mysqli_insert_id($this->con);

           foreach ($post['product_id'] as $key => $product_id)
            {
                $product_id = (int)$product_id;
                $batch_no   = mysqli_real_escape_string($this->con, $post['batch'][$key]);
                $qty        = (float)$post['qty'][$key];
                $rate       = (float)$post['rate'][$key];
                $amount     = $qty * $rate;

                if ($qty <= 0)
                {
                    continue;
                }

                // Insert Sales Details
                $detail = mysqli_query($this->con,"
                    INSERT INTO sales_details
                    (
                        s_id,
                        p_id,
                        batch_no,
                        qty,
                        rate,
                        amount
                    )
                    VALUES
                    (
                        '$sales_id',
                        '$product_id',
                        '$batch_no',
                        '$qty',
                        '$rate',
                        '$amount'
                    )
                ");

                if(!$detail)
                {
                    throw new Exception(mysqli_error($this->con));
                }

                // Stock Ledger
                $ledger = mysqli_query($this->con,"
                    INSERT INTO stock_ledger
                    (
                        ref_id,
                        product_id,
                        batch_no,
                        transaction_type,
                        stock_in,
                        stock_out,
                        amount,
                        rate,
                        transaction_date
                    )
                    VALUES
                    (
                        '$sales_id',
                        '$product_id',
                        '$batch_no',
                        'SALE',
                        0,
                        '$qty',
                        '$amount',
                        '$rate',
                        '$sales_date'
                    )
                ");

                if(!$ledger)
                {
                    throw new Exception(mysqli_error($this->con));
                }

                // Check Available Stock
                $check = mysqli_query($this->con,"
                    SELECT qty
                    FROM stock
                    WHERE product_id='$product_id'
                    AND batch_no='$batch_no'
                    LIMIT 1
                ");

                if(mysqli_num_rows($check) == 0)
                {
                    throw new Exception("Stock not found for Batch : ".$batch_no);
                }

                $stock = mysqli_fetch_assoc($check);

                if($stock['qty'] < $qty)
                {
                    throw new Exception("Insufficient stock for Batch : ".$batch_no);
                }

                // Reduce Stock
                $update = mysqli_query($this->con,"
                    UPDATE stock
                    SET qty = qty - '$qty'
                    WHERE product_id='$product_id'
                    AND batch_no='$batch_no'
                ");

                if(!$update)
                {
                    throw new Exception(mysqli_error($this->con));
                }
            }

            mysqli_commit($this->con);

            return true;

        } catch (Exception $e) {

            mysqli_rollback($this->con);

            return $e->getMessage();
        }
    }

    public function update($post, $sale_id)
    {
        $sale_id     = (int)$sale_id;
        $supplier_id = (int)$post['supplier_id'];
        $sales_date  = mysqli_real_escape_string($this->con, $post['sales_date']);

        mysqli_begin_transaction($this->con);

        try {
            // 1. Restore stock from the OLD details before removing them
            $oldDetails = mysqli_query($this->con, "
                SELECT p_id, batch_no, qty
                FROM sales_details
                WHERE s_id='$sale_id'
            ");

            while ($row = mysqli_fetch_assoc($oldDetails)) {
                mysqli_query($this->con, "
                    UPDATE stock
                    SET qty = qty + '{$row['qty']}'
                    WHERE product_id='{$row['p_id']}'
                    AND batch_no='" . mysqli_real_escape_string($this->con, $row['batch_no']) . "'
                ");
            }

            // 2. Remove old details + old ledger entries for this sale
            mysqli_query($this->con, "DELETE FROM sales_details WHERE s_id='$sale_id'");
            mysqli_query($this->con, "DELETE FROM stock_ledger WHERE ref_id='$sale_id' AND transaction_type='SALE'");

            // 3. Recalculate totals from the NEW submitted data
            $total_amount = 0;
            foreach ($post['product_id'] as $key => $product_id) {
                $qty  = (float)$post['qty'][$key];
                $rate = (float)$post['rate'][$key];
                $total_amount += $qty * $rate;
            }

            // 4. Update sales_entries header
            mysqli_query($this->con, "
                UPDATE sales_entries
                SET c_id='$supplier_id',
                    sale_date='$sales_date',
                    total_amt='$total_amount'
                WHERE s_id='$sale_id'
            ");

            // 5. Re-insert details + ledger + reduce stock (same as store())
            foreach ($post['product_id'] as $key => $product_id) {
                $product_id = (int)$product_id;
                $batch_no   = mysqli_real_escape_string($this->con, $post['batch'][$key]);
                $qty        = (float)$post['qty'][$key];
                $rate       = (float)$post['rate'][$key];
                $amount     = $qty * $rate;

                if ($qty <= 0) continue;

                mysqli_query($this->con, "
                    INSERT INTO sales_details (s_id, p_id, batch_no, qty, rate, amount)
                    VALUES ('$sale_id', '$product_id', '$batch_no', '$qty', '$rate', '$amount')
                ");

                mysqli_query($this->con, "
                    INSERT INTO stock_ledger
                    (ref_id, product_id, batch_no, transaction_type, stock_in, stock_out, amount, rate, transaction_date)
                    VALUES ('$sale_id', '$product_id', '$batch_no', 'SALE', 0, '$qty', '$amount', '$rate', '$sales_date')
                ");

                $check = mysqli_query($this->con, "
                    SELECT qty FROM stock
                    WHERE product_id='$product_id' AND batch_no='$batch_no' LIMIT 1
                ");

                if (mysqli_num_rows($check) == 0) {
                    throw new Exception("Stock not found for Batch : ".$batch_no);
                }

                $stock = mysqli_fetch_assoc($check);
                if ($stock['qty'] < $qty) {
                    throw new Exception("Insufficient stock for Batch : ".$batch_no);
                }

                mysqli_query($this->con, "
                    UPDATE stock SET qty = qty - '$qty'
                    WHERE product_id='$product_id' AND batch_no='$batch_no'
                ");
            }

            mysqli_commit($this->con);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->con);
            return $e->getMessage();
        }
    }
}