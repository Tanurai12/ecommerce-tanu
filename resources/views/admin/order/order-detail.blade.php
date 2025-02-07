@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            @include('admin.message')
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('order.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <h1 class="h5 mb-3">Shipping Address</h1>
                                    <address>
                                        <strong>{{ $order->first_name . ' ' . $order->last_name }}</strong><br>
                                        {{ $order->address }}<br>
                                        {{ $order->city }},{{ $order->zip }}<br>
                                        Phone: {{ $order->mobile }}<br>
                                        Email: {{ $order->email }}
                                    </address>
                                    <strong>Shipped Date</strong>
                                    <time datetime="2019-10-01">
                                        @if (!empty($order->shipped_date))
                                            {{ \Carbon\Carbon::parse($order->shipped_date)->format('d M, Y') }}
                                        @else
                                            n/a
                                        @endif
                                    </time>
                                </div>



                                <div class="col-sm-4 invoice-col">
                                    {{-- <b>Invoice #007612</b><br> --}}
                                    <br>
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b>{{ $order->grand_total }}<br>
                                    <b>Status:</b>
                                    @if ($order->status == 'pending')
                                        <span class="badge bg-danger">Pending</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-info">Shipped</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-success">deliverd</span>
                                        @elseif($order->status == 'placed')
                                        <span class="badge bg-primary">Placed</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-warning">cancelled</span>
                                        
                                    @endif

                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>

                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItems as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>Rs. {{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>Rs. {{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td>Rs. {{ number_format($order->subtotal, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td>Rs. 20.00</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td>Rs. {{ number_format($order->grand_total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <form action="" method="post" name="changeOrderStatusForm" id="changeOrderStatusForm">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                        </option>
                                        <option value="placed" {{ $order->status == 'placed' ? 'selected' : '' }}>Placed
                                        </option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="">Shipped Date</label>
                                    <input value="{{ $order->shipped_date }}" type="date" name="shipped_date"
                                        id="shipped_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        <div class="card-body">

                            <form action="" method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                                @csrf
                                <h2 class="h4 mb-3">Send Inovice Email</h2>
                                <div class="mb-3">
                                    <select name="userType" id="userType" class="form-control">
                                        <option value="customer">Customer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
@endsection

@section('customjs')
    <script>
        $('#changeOrderStatusForm').submit(function(event) {
            event.preventDefault();

            if(confirm("Are you sure want change status")){
                $.ajax({
                url: '{{ route("order.changeOrderStatus", $order->id) }}',
                type: 'POST',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        
                        window.location.href="{{route('order.detail',$order->id)}}"
                    } else {
                        alert('Failed to update order status');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Failed to update order status');
                }
            });
            }

           
        });
 
        $('#sendInvoiceEmail').submit(function(event){
            event.preventDefault();

            if(confirm("Are you sure want to send email")){
                $.ajax({
                url: '{{ route("order.sendInvoiceEmail",$order->id) }}',
                type: 'POST',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    
                    window.location.href="{{route('order.detail',$order->id)}}"
                    
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Failed to update order status');
                }
            });   
            }

            
        });
    </script>
@endsection
