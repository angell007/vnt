<?php

namespace App\Traits;

use Illuminate\Http\Response as Respon;
use Illuminate\Support\Facades\File;

trait Response
{
    public function saveFile()
    {
        if (request('file') != '') {

            $split = explode(',', substr(request('file'), 5), 2);
            $mime = $split[0];
            $data = $split[1];
            $mime_split_without_base64 = explode(';', $mime, 2);
            $mime_split = explode('/', $mime_split_without_base64[0], 2);
            $file = base64_decode($data);

            if (count($mime_split) == 2) $extension = $mime_split[1];

            $safeName = time() . '.' . $extension;
            $success = file_put_contents(public_path() . '/imgs/imgproducts/' .  $safeName, $file);

            // File::delete(public_path() . '/imgs/imgproducts/' . $item->photo);

            return $safeName;
        }
    }
}
