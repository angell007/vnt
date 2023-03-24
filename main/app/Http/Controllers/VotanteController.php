<?php

namespace App\Http\Controllers;

use App\Models\Votante;
use App\Http\Requests\StoreVotanteRequest;
use App\Http\Requests\UpdateVotanteRequest;
use App\Repositories\VotanteRepository;
use App\Traits\Response;
use App\Traits\verify;
use Stevebauman\Location\Facades\Location as Locality;

class VotanteController extends Controller
{
    use verify;
    use Response;

    protected $repository;
    protected $table;
    public function __construct()
    {
        $this->repository = new VotanteRepository;
        $this->table = 'cargos';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $ip = request()->ip();
        $data = Locality::get($ip);
        dd(request()->ip());

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
     * @param  \App\Http\Requests\StoreVotanteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreVotanteRequest $request)
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
     * @param  \App\Models\Votante  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Votante $cargo)
    {
        return $this->sendResponse($cargo);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVotanteRequest  $request
     * @param  \App\Models\Votante  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateVotanteRequest $request, Votante $cargo)
    {
        return $this->success($this->repository->update($cargo));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Votante  $cargo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Votante $cargo)
    {
        return $this->success($this->repository->delete($cargo));
    }
}
