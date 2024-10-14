<?php

namespace App\Imports;

use App\Models\ProductData;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

class ProductDataImport implements WithHeadingRow, WithValidation, ToCollection, WithBatchInserts, WithSkipDuplicates, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, RemembersRowNumber;

    public $data;
    public $err;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $discontinued = $row['discontinued'] === 'yes';

            $row['discontinued'] = $discontinued;
        }

        $this->data = $rows;
    }

    public function rules(): array
    {
        return [
            'stock' => [
                'integer',
                'bail',
                'min:10',
            ],
            'cost_in_gbp' => [
                'bail',
                'numeric',
                'min:5',
                'max:1000',
            ],
        ];
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$f)
    {
        $this->err = collect($f);
    }

    public function onError(\Throwable $e)
    {

    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function uniqueBy()
    {
        return 'product_code';
    }

    public function getRow()
    {
        return $this->row;
    }
}
