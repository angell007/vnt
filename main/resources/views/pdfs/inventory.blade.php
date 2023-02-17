<!DOCTYPE html>
<html>

<head>
    <title>Generate And Download PDF File Using dompdf</title>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!--<link rel="stylesheet" href="{{asset('bootstrap.min.css')}}">-->

</head>

<body width="100%" style="padding: 0; font-size: 12pt; font-family: Arial, Helvetica, sans-serif;">

    <div class="container">

        <div class="row mb-3">

            <div class="col-md-6">

                <span> <b> Code: </b> {{$inventory->id}}</span>
                <br>
                <span> <b> Shelf: </b> {{$inventory->store->name}}</span>
                <br>
                <span> <b>Address:</b> {{$inventory->store->address}}</span>
                <br>

            </div>

            <div class="col-md-6">


                <span><b>Checked : {{$inventory->check ? 'Si' : 'No' }}
                        <br>
                        <span><b>Vendor:</b> {{$inventory->user->name }}</span>
                        <br>
                        <span><b>Created at:</b> {{$inventory->created_at }}</span>
                        <br>
                        <span><b>Updated at:</b> {{$inventory->updated_at }}</span>

            </div>
        </div>

        <div class="table-responsive ">
            <table class="table text-center table-xs">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Reference - sku</th>
                        <th>Size</th>
                        <th>Available</th>
                        <th>Existence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventory->elements as $item)
                            @if ( $item->quantities->quantity == 0 )


                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->sku}}</td>
                        <td>{{$item->sheet_size}}</td>

                        <td>  {{$item->status == 0 ?  'in stock' : 'Sold out'}} </td>

                        <td class="text">
                            <label> {{$item->quantities->quantity !== 0 ? 'in stock' : 'Restock' }} </label>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>