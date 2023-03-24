<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class  BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function index($fields = ['*'], $with = [])
    {
        $params  = request()->query();
        unset($params['page']);
        $items = $this->model::select(...$fields)->with($with);
        foreach ($params as $key => $value) if ($value != '') $items->where($key, 'LIKE', $value . "%");
        return $items->orderBy('name', 'Asc')->paginate(20);
    }
    public function forSelect()
    {
        $params  = request()->query();
        unset($params['page']);
        $items = $this->model::select('name', 'id');
        foreach ($params as $key => $value) if ($value != '') $items->where($key, 'LIKE', $value . "%");
        $res = $items->orderBy('name', 'Asc')->limit(10)->get();
        return $res;
    }
    public function store()
    {
        $item = $this->model::create(request()->all());
        $item->save();
        Log::info(' Item Created');
        return $item;
    }
    public function update($item)
    {
        Log::info(' Item Updated');
        $item->update(request()->all());
        return 'item updated';
    }
    public function delete($item)
    {
        // $item = $this->get();
        // $item->delete = $item->delete == 1 ? 0 : 1;
        Log::info('Item Deleted');
        return $item->delete();
        // return  $item->save();
    }
    public function deletePermantly()
    {
        // $model = $this->get();
        // Log::info('Item Delete');
        // return $model->delete();
    }
}
