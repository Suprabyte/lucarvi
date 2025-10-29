<?php

namespace App\Imports;

use App\Models\Dato;
use Maatwebsite\Excel\Concerns\ToModel;

class DatosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Dato([
            //
        ]);
    }
}
