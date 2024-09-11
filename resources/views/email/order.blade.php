<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">
    @if($mailData['userType'] == 'customer')
    <h1 class="text-primary">Thanks for your order</h1>
    <h2>Your order id is:##{{$mailData['order']->id}}</h2>
    @else
    <h1 class="text-primary">You have receive an order</h1>
    <h2>Order Id:##{{$mailData['order']->id}}</h2>
    @endif 

    <h1 class="h5 mb-3">Shipping Address</h1>
    <address>
        <strong>{{$mailData['order']->first_name . ' ' . $mailData['order']->last_name }}</strong><br>
        {{ $mailData['order']->address }}<br>
        {{ $mailData['order']->city }},{{ $mailData['order']->zip }} {{getCountryInfo($mailData['order']->countries_id)->name}}<br>
        Phone: {{$mailData['order']->mobile }}<br>
    </address>

    <h2>product</h2>
    <div class="card-body table-responsive p-3">
        <table  cellpadding="3" cellspacing="3" border="0" width="700">
            <thead>

                <tr style="background: #ccc">
                    <th>Product</th>
                    <th width="100">Price</th>
                    <th width="100">Qty</th>
                    <th width="100">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mailData['order']->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>Rs. {{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rs. {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <th colspan="3" align="right" >Subtotal:</th>
                    <td>Rs. {{ number_format($mailData['order']->subtotal, 2) }}</td>
                </tr>

                <tr>
                    <th colspan="3" align="right" >Shipping:</th>
                    <td>Rs. 20.00</td>
                </tr>
                <tr>
                    @if(!empty($mailData['order']->total_tax))
                    <th colspan="3" align="right">Total Tax:</th>
                    <td>Rs. {{ number_format($mailData['order']->total_tax, 2) }}</td>
                @else
                    <th colspan="3" align="right">Discount:</th>
                    <td>Rs. 00</td>
                @endif
                </tr>
                <tr>
                    @if(!empty($mailData['order']->discount))
                    <th colspan="3" align="right">Discount:</th>
                    <td>Rs. {{ number_format($mailData['order']->discount, 2) }}</td>
                @else
                    <th colspan="3" align="right">Discount:</th>
                    <td>Rs. 00</td>
                @endif
                </tr>
                <tr>
                    <th colspan="3" align="right" >Grand Total:</th>
                    <td>Rs. {{ number_format($mailData['order']->grand_total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>