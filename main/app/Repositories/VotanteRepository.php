<?php

namespace App\Repositories;

use App\Models\Votante;

class VotanteRepository extends BaseRepository  implements RepositoryInterface
{
    public function __construct()
    {
        $model = new Votante;
        parent::__construct($model);
    }
}
