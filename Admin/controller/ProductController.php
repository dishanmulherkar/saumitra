<?php
include 'modals/ProductModel.php';

class ProductController
{
    private $model;

    public function __construct($con)
    {
        $this->model = new ProductModel($con);
    }

    public function index()
    {
        $ROW  = null;
        $list = $this->model->getAll();
        include 'view/inventory/product.php';
    }

    public function edit($id)
    {
        $ROW  = $this->model->getById($id);
        $list = $this->model->getAll();
        include 'view/inventory/product.php';
    }

    public function store()
    {
        if (mysqli_num_rows($this->model->checkDuplicate($_POST['product_name'])) > 0) {
            header("Location: " . BASE_URL . "products?duplicate=1");
            exit;
        }

        $status = in_array($_POST['status'], ['1', '0']) ? $_POST['status'] : '1';
        $success = $this->model->insert($_POST, $status);

        header("Location: " . BASE_URL . "products?success=created");
        exit;
    }

    public function update($id)
    {
        if (mysqli_num_rows($this->model->checkDuplicate($_POST['product_name'], $id)) > 0) {
            header("Location: " . BASE_URL . "products?duplicate=1");
            exit;
        }

        $status = in_array($_POST['status'], ['1', '0']) ? $_POST['status'] : '0';
        $success = $this->model->update($id, $_POST, $status);

        header("Location: " . BASE_URL . "products?success=updated");
        exit;
    }

    public function delete($id)
    {
       $result = $this->model->delete($id);

        if($result)
        {
            header("Location: ".BASE_URL."products?success=deleted");
        }
        else
        {
            header("Location: ".BASE_URL."products?error=used");
        }

        exit;
    }


 public function import()
{
    $file = $_FILES['import_file'] ?? null;

    if (!$file || empty($file['name'])) {
        header("Location: ".BASE_URL."products?import_error=nofile");
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ext == "csv") {

        $handle = fopen($file['tmp_name'], "r");

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

    } elseif ($ext == "xlsx") {

        $rows = $this->readXLSX($file['tmp_name']);

    } else {

        header("Location: ".BASE_URL."products?import_error=type");
        exit;
    }

    $inserted = 0;
    $skipped = 0;

    foreach ($rows as $i => $row) {

        // Skip Header
        if ($i == 0)
            continue;

        $product = trim($row[0] ?? '');

        $status = trim($row[1] ?? '');

        if ($status == '')
            $status = '1';

        if ($product == '')
            continue;

        if ($this->model->exists($product)) {
            $skipped++;
            continue;
        }

        $this->model->insertProduct($product, $status);

        $inserted++;
    }

    header("Location: ".BASE_URL."products?imported=$inserted&skipped=$skipped");
    exit;
}

        private function readXLSX($file)
        {
            $zip = new ZipArchive();

            if ($zip->open($file) !== TRUE) {
                return [];
            }

            // Shared Strings
            $sharedStrings = [];

            $ssXml = $zip->getFromName('xl/sharedStrings.xml');

            if ($ssXml) {

                $ss = simplexml_load_string($ssXml);

                foreach ($ss->si as $si) {

                    if (isset($si->t)) {

                        $sharedStrings[] = (string)$si->t;

                    } else {

                        $text = '';

                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }

                        $sharedStrings[] = $text;
                    }
                }
            }

            // Sheet
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

            $zip->close();

            if (!$sheetXml) {
                return [];
            }

            $sheet = simplexml_load_string($sheetXml);

            $rows = [];

            foreach ($sheet->sheetData->row as $xRow) {

                $cellRefs = [];

                foreach ($xRow->c as $c) {
                    $cellRefs[] = (string)$c['r'];
                }

                if (empty($cellRefs)) {
                    continue;
                }

                $maxCol = max(array_map([$this, 'xlsxColIndex'], $cellRefs));

                $row = array_fill(0, $maxCol + 1, '');

                foreach ($xRow->c as $c) {

                    $idx = $this->xlsxColIndex((string)$c['r']);

                    $value = (string)$c->v;

                    if ((string)$c['t'] === 's') {
                        $value = $sharedStrings[(int)$value] ?? '';
                    }

                    $row[$idx] = trim($value);
                }

                $rows[] = $row;
            }

            return $rows;
        }

        private function xlsxColIndex($cellRef)
        {
            $letters = preg_replace('/[0-9]/', '', strtoupper($cellRef));

            $index = 0;

            for ($i = 0; $i < strlen($letters); $i++) {
                $index = ($index * 26) + (ord($letters[$i]) - ord('A') + 1);
            }

            return $index - 1;
        }

            public function downloadSample()
            {
                header('Content-Type: text/xlsx');
                header('Content-Disposition: attachment; filename="product_sample.xlsx"');

                $fp = fopen('php://output', 'w');

                // Header
                fputcsv($fp, [
                    'Product Name',
                    'Status'
                ]);

                // Sample Rows
                fputcsv($fp, ['Jeera', 'Active']);
                fputcsv($fp, ['Ashwagandha', 'Active']);
                fputcsv($fp, ['Turmeric', 'Active']);
                fputcsv($fp, ['Ginger', 'Active']);
                fputcsv($fp, ['Cumin', 'Inactive']);

                fclose($fp);
                exit;
            }
}