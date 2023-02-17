<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegisterRequest;
use App\Models\Store;
use App\Traits\Qr;
use App\Traits\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;

class StoreController extends Controller
{

    use Response, Qr;

    public function index()
    {
        $params  = request()->query();

        unset($params['page']);

        $admins = DB::table('users')->select('id')
            ->where(function ($q) {
                $q->where('user_type', '<>', 'Seller')
                    ->orWhere('id', Auth::user()->id);
            })
            ->pluck('id');

        $items = Store::select('name', 'address', 'status', 'id', 'qr')
            ->whereIn('user_id', $admins)
            ->where('status', 1);

        foreach ($params as $key => $value) if ($value != '') $items->where($key, 'LIKE', $value . "%");

        $res = $items->orderBy('id', 'Asc')->paginate(50);

        return $this->success($res);
    }

    public function get()
    {

        $store = Store::find(request()->get('id'));
        return $this->success($store);
    }

    public function register(StoreRegisterRequest $request)
    {
        $item = Store::create([
            'name' => request('name'),
            'status' => true,
            'address' => request('address'),
            'user_id' => Auth::user()->id,
            'quantity_elements' => request('quantity_elements'),
        ]);
        $item->qr =  $this->generateQr("qr" . 's' . $item->id, 'stores');
        $item->save();

        Log::info(' Store Created');
        return $this->success(['message' => 'Element created successfully', 'item' => $item], 201);
    }

    public function update(StoreRegisterRequest $request)
    {
        $store = Store::find(request('id'));
        $store->update([
            'name' => request('name'),
            'address' => request('address'),
        ]);

        Log::info(' Item Updated');
        return $this->success(['message' => 'Element update successfully', 'item' => $store], 200);
    }

    public function delete(Store $store)
    {

        $store->status = $store->status == 1 ? 0 : 1;

        $store->save();

        return $this->success(['message' => 'Element update successfully', 'item' => $store], 200);
    }

    public function downloadPdf($id)
    {
        $store = Store::find($id);
        $pdf = \PDF::loadView('pdfs.printqrstore', compact('store'));

        return $pdf->stream('qr.pdf');
    }
}
