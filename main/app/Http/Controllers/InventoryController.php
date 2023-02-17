<?php

namespace App\Http\Controllers;

use App\Models\Element;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\User;
use App\Traits\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailInventory;
use App\Mail\EmailChange;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InventoryController extends Controller
{

    use Response;
    public function first()
    {
        $inventory = Inventory::create([
            'store_id' => 0,
            'date' => Carbon::now(),
            'user_id' => 0,
            // 'user_id' => Auth::user()->id,
            'read' => 0
        ]);


        $elemetns = Element::all();
        foreach ($elemetns as $elemetn) {
            DB::table('element_inventory')->insert([
                'element_id' => $elemetn->id,
                'inventory_id' => $inventory->id,
                'quantity' => 1,
                'alert' => false,
                'missing' => 0,
            ]);
        }
    }


    public function register(Store $store)
    {
        // $existItems = Inventory::with(['elements' => function ($q) {
        //     $q->where('status', 1);
        // }, 'store'])->where('store_id', $store->id)->latest()->first();
        // if (!$existItems) 

        $existItems = Inventory::with(['elements' => function ($q) {
            $q->where('status', 1);
            $q->where('delete', 0);
        }, 'store'])->where('store_id', 0)->first();


        $newItems = [];

        $items = request()->get("items");

        $inventory = Inventory::create([
            'store_id' => $store->id,
            'date' => Carbon::now(),
            // 'user_id' => 1,
            'user_id' => Auth::user()->id,
            'read' => 0
        ]);

        foreach ($items as $item) {

            $missing = 0;
            $alert = false;

            $id = preg_replace('/[^0-9]/', '', $item['qr']);
            array_push($newItems, $id);

            if ($existItems) {
                $oldItems = $existItems->elements()->get();
                if ($oldItem = $oldItems->find($id)) {
                    $missing = $item['quantity'] - $oldItem->quantities->quantity;
                }
            }

            if (Element::find($id)) {
                DB::table('element_inventory')->insert([
                    'element_id' => $id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $item['quantity'],
                    'alert' => $alert,
                    'missing' => $missing,
                    'checked' => $missing == 0 ? 1 : 0,

                ]);
            }
        }


        if ($existItems) {
            $oldItems = $existItems->elements()->get();
            if (isset($id)) if ($oldItem = $oldItems->find($id)) $missing = $oldItem->quantities->quantity - $item['quantity'];
            $oldItemsArray = json_decode(json_encode($existItems->elements()->pluck('elements.id')), true);

            $missings =  array_diff($oldItemsArray, $newItems);

            $store->count =  $store->count +  count($missings);
            $store->save();

            if ($missings) {
                foreach ($missings as $value) {

                    $oldItem = $oldItems->find($value);
                    $missing = $oldItem->quantities->quantity;

                    DB::table('element_inventory')->insert([
                        'element_id' => $value,
                        'inventory_id' => $inventory->id,
                        'quantity' => 0,
                        'alert' => true,
                        'missing' => $missing,
                        'checked' => $missing == 0 ? 1 : 0,
                    ]);

                    $oldItem->count = $oldItem->count + 1;
                    $oldItem->save();
                }
            }
        }


        //Comparision with old inventory

        if ($inventory->id == 1) return $this->success(['message' => 'Inventory created successfully'], 200);

        $data = ['email' => 'Sandra@mosaicandtiledepot.com', 'vendor' => $inventory->user->name, 'code' => $inventory->id];
        Mail::to($data['email'])->send(new EmailInventory($data));
        Log::info(' Inventory Created');
        return $this->success(['message' => 'Inventory created successfully'], 200);
    }

    public function update($store)
    {
        $store = Store::find($store);

        $items = request()->get("items");

        $inventory = Inventory::where('store_id', $store->id)->latest()->first();

        $inventory->elements()->detach();


        $existItems = Inventory::with(['elements' => function ($q) {
            $q->where('status', 1);
            $q->where('delete', 0);
        }, 'store'])->where('store_id', 0)->first();


        $newItems = [];

        foreach ($items as $item) {

            $missing = 0;
            $alert = false;

            $id = preg_replace('/[^0-9]/', '', $item['qr']);
            array_push($newItems, $id);

            if ($existItems) {
                $oldItems = $existItems->elements()->get();
                if ($oldItem = $oldItems->find($id)) {
                    $missing = $item['quantity'] - $oldItem->quantities->quantity;
                }
            }

            if (Element::find($id)) {
                DB::table('element_inventory')->insert([
                    'element_id' => $id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $item['quantity'],
                    'alert' => $alert,
                    'missing' => $missing,
                    'checked' => $missing == 0 ? 1 : 0,

                ]);
            }
        }


        if ($existItems) {
            $oldItems = $existItems->elements()->get();
            if (isset($id)) if ($oldItem = $oldItems->find($id)) $missing = $oldItem->quantities->quantity - $item['quantity'];
            $oldItemsArray = json_decode(json_encode($existItems->elements()->pluck('elements.id')), true);

            $missings =  array_diff($oldItemsArray, $newItems);

            $store->count =  $store->count +  count($missings);
            $store->save();

            if ($missings) {
                foreach ($missings as $value) {

                    $oldItem = $oldItems->find($value);
                    $missing = $oldItem->quantities->quantity;

                    DB::table('element_inventory')->insert([
                        'element_id' => $value,
                        'inventory_id' => $inventory->id,
                        'quantity' => 0,
                        'alert' => true,
                        'missing' => $missing,
                        'checked' => $missing == 0 ? 1 : 0,
                    ]);

                    $oldItem->count = $oldItem->count + 1;
                    $oldItem->save();
                }
            }
        }

        $inventory->updated_at = Carbon::now();
        $inventory->save();
        //Comparision with old inventory
        return $this->success(['message' => 'Inventory update successfully'], 200);
    }

    public function last(Store $store)
    {

        $items = Inventory::with(['elements' => function ($q) {
            $q->where('status', 1)->select('name', 'sku', 'qr', 'photo');
        }, 'store'])->where('store_id', $store->id)->latest()->first();

        if (!$items) $items['store'] = $store;

        return $this->success($items, 200);
    }


    public function reportAllStores(Store $store)
    {
        $stores = Store::get(['name', 'id', 'count']);
        $elements = Element::get(['name', 'id', 'count']);
        $elementsCount = Element::count();

        $inventories = [];
        $alerts = 0;
        // $missings = 0;
        // $storesMissings = 0;
        $storesAlerts = 0;

        $storesCount = Store::count();
        $storesCountActives = Store::where('status', 1)->count();

        $storesRotatives = Store::orderBy('count', 'Desc')->take(5)->get(['name', 'id', 'count']);
        $elementsRotatives = Element::orderBy('count', 'Desc')->take(5)->get(['name', 'id', 'count']);

        $inventoriesCount = Inventory::where('store_id', '<>', 0)->count();

        foreach ($stores->pluck('id') as $store) {

            $inventory = Inventory::with(['elements' => function ($q) {
                $q->where('status', 1);
            }, 'store'])->where('store_id', $store)->latest()->first();
            if ($inventory) $inventories[] = $inventory;
        }

        foreach ($inventories as $inventory) {

            $flag1 = false;
            // $flag2 = false;

            foreach ($inventory['elements'] as $element) {
                if ($element->quantities->alert > 0) {
                    $alerts += 1;
                    $flag1 = true;
                }
                // if ($element->quantities->missing < 0) {
                //     $missings += 1;
                //     $flag2 = true;
                // }
            }


            if ($flag1) $storesAlerts += 1;
            // if ($flag2) $storesMissings += 1;
        }

        return $this->success(['elements' => $elementsCount, 'stores' =>  $storesCount, 'storesCount' => $storesCountActives, 'inventories' => $inventoriesCount, 'sotresRotatives' => $storesRotatives, 'elementsRotatives' => $elementsRotatives], 200);

        // return $this->success(['stores' =>  $storesCount, 'inventories' => $inventoriesCount, 'storesWithMissings' => $storesMissings, 'storesWithAlerts' => $storesAlerts,  'missings' => $missings, 'alerts' => $alerts], 200);
    }


    public function stores()
    {
        $user = Auth::user(); // assuming the current user is authenticated
        $role = $user->user_type; // assuming the user has a 'role' field

        $items = Store::select('name', 'id', 'qr')
            ->when($role == 'Warehouse', function ($query) {
                // if the user is a warehouse, get all stores
                return $query;
            }, function ($query) use ($user) {
                // if the user is a seller, get only the stores with the user_id same as the current user's id
                return $query->where('user_id', $user->id);
            })
            ->where('status', 1)
            ->get();


        return $this->success($items, 200);
    }



    public function getElement($qr)
    {
        $data = null;
        if (preg_match("/qrs/", $qr)) return $this->success('No data', 201);
        $qr = preg_replace('/[^0-9]/', '', $qr);
        if ($data = Element::find($qr))
            return $this->success($data, 200);
    }

    public function getElements()
    {
        $data = Element::select('id', 'name', 'qr', 'photo', 'reference', 'sku')->get();
        return $this->success($data, 200);
    }


    public function unreaded()
    {
        $items = Inventory::with(['user', 'elements' => function ($q) {
            $q->where('status', 1);
        }, 'store'])->where('read', 0)->where('store_id', '<>', 0)->get();
        return $this->success($items, 200);
    }

    public function markasread($id)
    {
        $items = Inventory::with(['user', 'elements' => function ($q) {
            $q->where('status', 1);
        }, 'store'])->where('user_id', $id)->update([
            'read' => 1
        ]);
        return $this->success($items, 200);
    }

    public function owners($id)
    {
        $items = Inventory::with(['user', 'elements' => function ($q) {
            $q->where('status', 1);
        }, 'store'])->where('user_id', $id)->OrderBy('id', 'desc')->get();
        return $this->success($items, 200);
    }


    public function myowners($id)
    {
        $items = Inventory::with(['user', 'elements' => function ($q) {
            $q->where('status', 1);
        }, 'store'])
            ->where('user_id', $id)
            ->OrderBy('id', 'desc')->get();
        return $this->success($items, 200);
    }


    public function check($id)
    {

        $inv = Inventory::with('user')->find($id);

        $inv->check =  $inv->check == 1 ? 0 : 1;

        $inv->save();

        $data = ['email' => $inv->user->email, 'admin' => Auth()->user()->name, 'code' => $inv->id];
        Mail::to($data['email'])->send(new EmailChange($data));
        Log::info(' Inventory Change');


        return $this->success(['message' => 'Element update successfully', 'item' => $inv], 200);
    }


    public function alls()
    {
        $stores = null;
        $vendors = null;
        $code = null;
        $checked = null;


        if (request('vendors') && request('vendors') != '0') $vendors = explode(',', request('vendors') ?: null);
        if (request('stores')) $stores  = explode(',', request('stores') ?: null);
        if (request('code')) $code  = request('code');
        if (request('checked') == 1) $checked  = 1;
        if (request('checked') === '0') $checked  = 'false';

        $items = Inventory::with(['user', 'elements' => function ($q) {
            $q->where('delete', 0)->orderBy('quantity', 'ASC');
        }, 'store'])
            ->where('store_id', '<>', 0)
            ->when($stores, function ($q) use ($stores) {
                $q->whereIn('store_id', $stores);
            })
            ->when($vendors, function ($q) use ($vendors) {
                $q->whereIn('user_id', $vendors);
            })
            ->when(request('vendors') == '0', function ($q) use ($vendors) {
                $q->where('user_id', Auth::user()->id);
            })
            ->when($code, function ($q) use ($code) {
                $q->where('id', $code);
            })
            ->when($checked == 1 || $checked == 'false', function ($q) use ($checked) {
                if ($checked == 'false') $checked == 0;
                $q->where('check', $checked);
            })
            ->OrderBy('created_at', 'Desc')->get();

        return $this->success($items, 200);
    }

    public function downloadPdf($id)
    {
        $inventory = Inventory::with(['user', 'elements' => function ($q) {
            $q->orderBy('missing', 'DESC');
        }, 'store'])
            ->where('id', $id)->OrderBy('id', 'desc')->first();

        $pdf = PDF::loadView('pdfs.inventory', compact('inventory'));
        return $pdf->stream('inventory.pdf');
    }
}
