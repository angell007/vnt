<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function index();
    public function forSelect();
    // public function get(Model $item);
    public function store();
    public function update(Model $item);
    public function delete(Model $item);
}
