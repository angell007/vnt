<?php

namespace App\Repositories;

use App\Models\Cargo;

class CargoRepository extends BaseRepository  implements RepositoryInterface
{
    public function __construct()
    {
        $model = new Cargo;
        parent::__construct($model);
    }
}
