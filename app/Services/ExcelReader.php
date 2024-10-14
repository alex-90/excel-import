<?php

namespace App\Services;
use App\Models\ProductData;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\ProductDataImport;
use Illuminate\Support\Collection;

class ExcelReader
{
    private $import;

    public function __construct(
        private string $path,
        private bool $testMode = false
    )
    {
    
    }

    public function parse()
    {                
        $res = [
            'errors' => [],
        ];

        try {
            $this->import = new ProductDataImport;
            $this->import->import($this->path);

            $errors = $this->import->err;
        
            $res['errors'] = $errors->map(function(\Maatwebsite\Excel\Validators\Failure $err){
                return "Row: " . $err->row() . ': ' . $err->errors()[0];
            });            
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    
        }

        $res['data'] = $this->import->data;

        return $res;
    }

    public function save()
    {
        if (!$this->testMode){
            $this->import->data->map(function(Collection $row){
    
                $model = new ProductData;
                
                $model->strProductName = $row['product_name'];
                $model->strProductDesc = $row['product_description'];
                $model->strProductCode = $row['product_code'];
                $model->dtmDiscontinued = $row['discontinued'] ? date('Y-m-d H:i:s') : null;
                $model->stock = $row['stock'];
                $model->price = $row['cost_in_gbp'];
    
                $model->save();
            });
        }

        return $this->import->data->count();
    }

}
