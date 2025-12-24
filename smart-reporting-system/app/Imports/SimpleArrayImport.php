<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class SimpleArrayImport implements ToArray
{
    /**
     * @param array $array
     * @return array
     */
    public function array(array $array)
    {
        return $array;
    }
}


