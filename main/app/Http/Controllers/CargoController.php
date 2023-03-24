<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Http\Requests\StoreCargoRequest;
use App\Http\Requests\UpdateCargoRequest;
use App\Repositories\CargoRepository;
use App\Traits\Response;
use App\Traits\verify;

class CargoController extends Controller
{
    use verify;
    use Response;

    protected $repository;
    protected $table;
    public function __construct()
    {
        $this->repository = new CargoRepository;
        $this->table = 'cargos';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendResponse($this->repository->index(['name', 'id', 'description']));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forSelect()
    {
        return $this->success($this->repository->forSelect());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCargoRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCargoRequest $request)
    {
        if (count($this->verifyName()))
            return $this->success([
                'message' => trans('coincidences'),
                'suggestions' => $this->verifyName()
            ]);

        return $this->success($this->repository->store());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cargo  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Cargo $cargo)
    {
        return $this->sendResponse($cargo);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCargoRequest  $request
     * @param  \App\Models\Cargo  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCargoRequest $request, Cargo $cargo)
    {
        return $this->success($this->repository->update($cargo));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cargo  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Cargo $cargo)
    {
        return $this->success($this->repository->delete($cargo));
    }
}
