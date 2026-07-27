<?php

class PurchaseEntryModel
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function getProducts()
    {
        return mysqli_query($this->con, "
            SELECT p_id, product_name
            FROM products
            ORDER BY product_name ASC
        ");
    }

    public function getParties()
    {
        return mysqli_query($this->con, "
            SELECT party_id, party_name
            FROM parties
            WHERE party_type='Supplier'
            ORDER BY party_name ASC
        ");
    }

    public function getPurchase($sale_id)
    {
        return mysqli_fetch_assoc(mysqli_query($this->con,"
            SELECT pe.* , p.party_name,p.party_id
            FROM purchase_entry pe
            INNER JOIN parties p 
            ON pe.supplier_id = p.party_id 
            WHERE purchase_id='$sale_id'
        "));
    }

        public function getSaleDetails($sale_id)
        {
            return mysqli_query($this->con,"
                SELECT
                    pd.*,
                    p.product_name
                FROM purchase_entry_details pd
                INNER JOIN products p
                    ON p.p_id = pd.product_id
                WHERE pd.purchase_id='$sale_id'
                ORDER BY pd.detail_id
            ");
        }

        public function getSoldQty($purchase_id)
{
    $purchase_id = (int)$purchase_id;

    $purchase = mysqli_fetch_assoc(mysqli_query($this->con, "
        SELECT batch_no FROM purchase_entry WHERE purchase_id='$purchase_id'
    "));

    if (!$purchase) {
        return [];
    }

    $batch_no = $purchase['batch_no'];

    $details = mysqli_query($this->con, "
        SELECT product_id, qty
        FROM purchase_entry_details
        WHERE purchase_id='$purchase_id'
    ");

    $sold = [];

    while ($row = mysqli_fetch_assoc($details))
    {
        $product_id   = $row['product_id'];
        $purchasedQty = (float)$row['qty'];

        $stockRow = mysqli_fetch_assoc(mysqli_query($this->con, "
            SELECT qty FROM stock
            WHERE product_id='$product_id' AND batch_no='$batch_no'
            LIMIT 1
        "));

        $currentStock = $stockRow ? (float)$stockRow['qty'] : 0;
        $soldQty      = $purchasedQty - $currentStock;

        $sold[$product_id] = $soldQty > 0 ? $soldQty : 0;
    }

    return $sold;
}

    public function store($post)
    {
        $supplier_id   = (int)$post['supplier_id'];
        $purchase_date = mysqli_real_escape_string($this->con, $post['purchase_date']);
        $remarks       = mysqli_real_escape_string($this->con, $post['remarks'] ?? '');

        $admin_id = isset($_SESSION['admin_id'])
            ? (int)$_SESSION['admin_id']
            : null;

        // Generate batch number: DDMMYY
        $batch_no = date('dmy', strtotime($purchase_date));

        $total_qty    = 0;
        $total_amount = 0;

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

            // Insert purchase header
            $header = mysqli_query($this->con, "
                INSERT INTO purchase_entry
                (
                    batch_no,
                    supplier_id,
                    purchase_date,
                    total_qty,
                    total_amount
                )
                VALUES
                (
                    '$batch_no',
                    '$supplier_id',
                    '$purchase_date',
                    '$total_qty',
                    '$total_amount'
                )
            ");

            if (!$header)
            {
                throw new Exception(mysqli_error($this->con));
            }

            $purchase_id = mysqli_insert_id($this->con);

            // Insert details and stock ledger
            foreach ($post['product_id'] as $key => $product_id)
            {
                $product_id = (int)$product_id;
              
                $qty        = (float)$post['qty'][$key];
                $rate       = (float)$post['rate'][$key];
                $amount     = $qty * $rate;

                if ($qty <= 0)
                {
                    continue;
                }

                // Purchase Details
                $detail = mysqli_query($this->con,"
                    INSERT INTO purchase_entry_details
                    (
                        purchase_id,
                        product_id,
                        qty,
                        purchase_rate,
                        amount
                    )
                    VALUES
                    (
                        '$purchase_id',
                        '$product_id',
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
                        '$purchase_id',
                        '$product_id',
                        '$batch_no',
                        'PURCHASE',
                        '$qty',
                        0,
                        '$amount',
                        '$rate',
                        '$purchase_date'
                    )
                ");

                if(!$ledger)
                {
                    throw new Exception(mysqli_error($this->con));
                }

                // Check Stock
                $check = mysqli_query($this->con,"
                    SELECT stock_id
                    FROM stock
                    WHERE product_id='$product_id'
                    AND batch_no='$batch_no'
                    LIMIT 1
                ");

                if(mysqli_num_rows($check) > 0)
                {
                    // Increase Existing Stock
                    $update = mysqli_query($this->con,"
                        UPDATE stock
                        SET
                            qty = qty + '$qty',
                            purchase_rate = '$rate'
                        WHERE
                            product_id='$product_id'
                            AND batch_no='$batch_no'
                    ");

                    if(!$update)
                    {
                        throw new Exception(mysqli_error($this->con));
                    }
                }
                else
                {
                    // Create New Stock Record
                    $insert = mysqli_query($this->con,"
                        INSERT INTO stock
                        (
                            product_id,
                            batch_no,
                            qty,
                            purchase_rate
                        )
                        VALUES
                        (
                            '$product_id',
                            '$batch_no',
                            '$qty',
                            '$rate'
                        )
                    ");

                    if(!$insert)
                    {
                        throw new Exception(mysqli_error($this->con));
                    }
                }
            }

            mysqli_commit($this->con);
            return true;
        } catch (Exception $e) {

            mysqli_rollback($this->con);

            return $e->getMessage();
        }
    }

    public function update($post)
{
    $purchase_id   = (int)$post['purchase_id'];
    $supplier_id   = (int)$post['supplier_id'];
    $purchase_date = mysqli_real_escape_string($this->con,$post['purchase_date']);
    $remarks       = mysqli_real_escape_string($this->con,$post['remarks'] ?? '');

    $batch_no = date('dmy',strtotime($purchase_date));

    $total_qty    = 0;
    $total_amount = 0;

    mysqli_begin_transaction($this->con);

    try
    {
        //==========================
        // Calculate New Totals
        //==========================
        foreach($post['product_id'] as $key=>$product_id)
        {
            $qty    = (float)$post['qty'][$key];
            $rate   = (float)$post['rate'][$key];
            $amount = $qty * $rate;

            $total_qty += $qty;
            $total_amount += $amount;
        }

        //==========================
        // Get Old Purchase Details
        //==========================
        $oldDetails = mysqli_query($this->con,"
            SELECT *
            FROM purchase_entry_details
            WHERE purchase_id='$purchase_id'
        ");

        while($old=mysqli_fetch_assoc($oldDetails))
        {
            $product_id=$old['product_id'];
            $oldQty=$old['qty'];

            // Current Stock
            $stock=mysqli_query($this->con,"
                SELECT qty
                FROM stock
                WHERE product_id='$product_id'
                AND batch_no='$batch_no'
                LIMIT 1
            ");

            if(mysqli_num_rows($stock))
            {
                $stockRow=mysqli_fetch_assoc($stock);

                $currentStock=(float)$stockRow['qty'];

                // Sold Qty
                $soldQty=$oldQty-$currentStock;

                // Find New Qty
                $newQty=0;

                foreach($post['product_id'] as $k=>$pid)
                {
                    if($pid==$product_id)
                    {
                        $newQty=(float)$post['qty'][$k];
                        break;
                    }
                }

                // Don't allow reducing below sold qty
                if($newQty < $soldQty)
                {
                    throw new Exception(
                        "Cannot reduce purchase quantity.\n\nProduct ID : ".$product_id.
                        "\nAlready Sold : ".$soldQty
                    );
                }

                // Remove Old Purchase Qty from Stock
                $update=mysqli_query($this->con,"
                    UPDATE stock
                    SET qty=qty-'$oldQty'
                    WHERE product_id='$product_id'
                    AND batch_no='$batch_no'
                ");

                if(!$update)
                {
                    throw new Exception(mysqli_error($this->con));
                }
            }
        }

        //==========================
        // Delete Old Details
        //==========================
        $delete=mysqli_query($this->con,"
            DELETE
            FROM purchase_entry_details
            WHERE purchase_id='$purchase_id'
        ");

        if(!$delete)
        {
            throw new Exception(mysqli_error($this->con));
        }

        //==========================
        // Delete Old Ledger
        //==========================
        $deleteLedger=mysqli_query($this->con,"
            DELETE
            FROM stock_ledger
            WHERE ref_id='$purchase_id'
            AND transaction_type='PURCHASE'
        ");

        if(!$deleteLedger)
        {
            throw new Exception(mysqli_error($this->con));
        }

        //==========================
        // Update Purchase Header
        //==========================
        $header=mysqli_query($this->con,"
            UPDATE purchase_entry
            SET
                total_qty='$total_qty',
                total_amount='$total_amount',
                remarks='$remarks'
            WHERE purchase_id='$purchase_id'
        ");

        if(!$header)
        {
            throw new Exception(mysqli_error($this->con));
        }
                //==========================
        // Insert New Details
        //==========================
        foreach($post['product_id'] as $key=>$product_id)
        {
            $product_id = (int)$product_id;

            $qty    = (float)$post['qty'][$key];
            $rate   = (float)$post['rate'][$key];
            $amount = $qty * $rate;

            if($qty <= 0)
            {
                continue;
            }

            // Purchase Details
            $detail=mysqli_query($this->con,"
                INSERT INTO purchase_entry_details
                (
                    purchase_id,
                    product_id,
                    qty,
                    purchase_rate,
                    amount
                )
                VALUES
                (
                    '$purchase_id',
                    '$product_id',
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
            $ledger=mysqli_query($this->con,"
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
                    '$purchase_id',
                    '$product_id',
                    '$batch_no',
                    'PURCHASE',
                    '$qty',
                    0,
                    '$amount',
                    '$rate',
                    '$purchase_date'
                )
            ");

            if(!$ledger)
            {
                throw new Exception(mysqli_error($this->con));
            }

            //==========================
            // Update Stock
            //==========================
            $check=mysqli_query($this->con,"
                SELECT stock_id
                FROM stock
                WHERE product_id='$product_id'
                AND batch_no='$batch_no'
                LIMIT 1
            ");

            if(mysqli_num_rows($check))
            {
                $update=mysqli_query($this->con,"
                    UPDATE stock
                    SET
                        qty = qty + '$qty',
                        purchase_rate='$rate'
                    WHERE
                        product_id='$product_id'
                        AND batch_no='$batch_no'
                ");

                if(!$update)
                {
                    throw new Exception(mysqli_error($this->con));
                }
            }
            else
            {
                $insert=mysqli_query($this->con,"
                    INSERT INTO stock
                    (
                        product_id,
                        batch_no,
                        qty,
                        purchase_rate
                    )
                    VALUES
                    (
                        '$product_id',
                        '$batch_no',
                        '$qty',
                        '$rate'
                    )
                ");

                if(!$insert)
                {
                    throw new Exception(mysqli_error($this->con));
                }
            }
        }

        mysqli_commit($this->con);

        return true;

    }
    catch(Exception $e)
    {
        mysqli_rollback($this->con);

        return $e->getMessage();
    }
}



}