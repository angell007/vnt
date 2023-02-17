<?php

namespace App\Http\Controllers;

use App\Http\Requests\ElementStoreRequest;
use App\Models\Element;
use App\Models\Inventory;
use App\Models\Store;
use App\Traits\Qr;
use App\Traits\Response;
use Illuminate\Support\Facades\File;


use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ElementController extends Controller
{
    use Response, Qr;

    public function first(){
        
         $elemetns = Element::all();
        foreach ($elemetns as $elemetn) {
             $elemetn->qr =  $this->generateQr("qr" . $elemetn->id, 'items');
             $elemetn->save();
        }
        
    }
    
    public function index(Store $store)
    {
        $params  = request()->query();
        
        unset($params['page']);
        
        $items = Element::select('name', 'sku', 'sheet_size', 'packing', 'status', 'id', 'photo')->where('delete', 0);
        
        foreach ($params as $key => $value) if($value != '') $items->where($key, 'LIKE', $value . "%");
        
        $res = $items->orderBy('name' , 'Asc')->paginate(20);
        
        return $this->success($res);
    }
    

    public function get()
    {
        $element = Element::find(request()->get('id'));
        return $this->success($element);
    }

    public function register(ElementStoreRequest $request)
    {
         $item = Element::create([
            'name' => request('name'),
             'sku' => request('sku'),
             'sheet_size' => request('size'),
            'packing' => request('packing'),
             'material' => request('material'),
             'reference' => request('sku'),
             'status' => 1,
         ]);
        
        if(request('file') != '') {
            
                    $split = explode(',', substr(request('file'), 5), 2);
                    $mime = $split[0];
                    $data = $split[1];
                    $mime_split_without_base64 = explode(';', $mime, 2);
                    $mime_split = explode('/', $mime_split_without_base64[0], 2);
                    $file = base64_decode($data);
                    
                    if (count($mime_split) == 2) $extension = $mime_split[1];
                    
                    $safeName = time() .'.'. $extension;
                    $success = file_put_contents(public_path().'/imgs/imgproducts/' .  $safeName , $file);
        
                    $item->photo =  $safeName;
        }

         $item->qr =  $this->generateQr("qr" . $item->id, 'items');
         $item->save();

                                \Log::info(' Item Created' );
        return $this->success(['message' => 'Element created successfully', 'item' => $item], 201);
    }

    public function update(ElementStoreRequest $request, Element $element)
    {
        
         $item = Element::find(request()->get('id'));

         $item->update([
                    'name' => request('name'),
                    'sku' => request('sku'),
                    'sheet_size' => request('size'),
                    'packing' => request('packing'),
                    'material' => request('material'),
                    'reference' => request('sku'),
                    // 'status' => 1,
         ]);
        
        if(request('file') != '') {
            
                    $split = explode(',', substr(request('file'), 5), 2);
                    $mime = $split[0];
                    $data = $split[1];
                    $mime_split_without_base64 = explode(';', $mime, 2);
                    $mime_split = explode('/', $mime_split_without_base64[0], 2);
                    $file = base64_decode($data);
                    
                    if (count($mime_split) == 2) $extension = $mime_split[1];
                    
                    $safeName = time() .'.'. $extension;
                    $success = file_put_contents(public_path().'/imgs/imgproducts/' .  $safeName , $file);
                    
                    File::delete(public_path() . '/imgs/imgproducts/' . $item->photo);
                    
                    $item->photo =  $safeName;
        }

         $item->save();

                                \Log::info(' Item Updated' );
        return $this->success(['message' => 'Element updated successfully', 'item' => $item], 201);
        
        
    }

    public function delete(Element $element)
    {

        $element->delete = $element->delete == 1 ? 0 : 1;
        
        $inventories = Inventory::where('check', 0 )->get();
        
    //     foreach($inventories as $inventory){
            
    //   \DB::table('element_inventory')->where('inventory_id', $inventory->id)->where('element_id', $element->id)->delete();
            
    //     }
        
        $element->save();

        return $this->success(['message' => 'element update successfully', 'item' => $element], 200);
    }
    
    
     public function changuecheked()
    {
        
       $res = \DB::table('element_inventory')->where('element_id',request()->get('itemiId') )->where('inventory_id',request()->get('invId') )->first();
       

       $res->checked  =  $res->checked == 1 ? 0 : 1 ; 
       
       
       \DB::table('element_inventory')->where('id', $res->id)->update(['checked' => $res->checked]);

        return $this->success(['message' => 'Element update successfully', 'item' =>  $res ], 200);
    }
    
    public function changuestatus()
    {

        $element = Element::find(request()->get('id'));

         if ($element->status == 'activo')  $status =  2;
         if ($element->status == 'pendiente')  $status =  0;
         if ($element->status == 'inactivo')  $status =  1;

        $element->update(['status' => $status]);

        return $this->success(['message' => 'Element update successfully', 'item' => $element], 200);
    }


    public function downloadPdf($id)
    {
        $elemetn = Element::find($id);
        $pdf = PDF::loadView('pdfs.printqr', compact('elemetn'));
        return $pdf->stream('qr.pdf');
    }
}
