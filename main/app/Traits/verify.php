<?php

namespace App\Traits;

use Illuminate\Http\Response as Respon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait verify
{

    public function verifyName()
    {
        if (request()->get('verify', 'false') == 'false')
            return $this->verify();
        return [];
    }
    public function verify()
    {

        $name = request()->get('name');
        $coincidences = collect([]);
        $items = DB::table($this->table)->select('name')->get();
        foreach ($items as $item) {

            $coincidencia = 0;
            similar_text($name, $item->name, $coincidencia);
            if ($coincidencia >= 75) {
                $coincidences->push($item->name);
            }
        }

        return  $coincidences;
    }
}
