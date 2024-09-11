<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BillingAddress;
use App\Models\Client;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Product;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function create()
    {

        return view('admin.invoice.create' );
    }
    public function getProductsdata()
    {
        $products = Product::all() ;// Fetch all products

        return response()->json($products);
    }









    public function store(Request $request)
    {
    

        $validatedData = $request->validate([
            'selectedProduct' => 'required|array',
            'selectedProduct.*' => 'required|integer|exists:products,id',
            'new_Qty' => 'required|array',
            'new_Qty.*' => 'required|integer|min:1',
            'extraDiscount' => 'required|numeric',
            'grandtotal' => 'required|numeric',
            'payment_mode' => 'required|string',
            // Add other validation rules for client details
        ]);

        $client = Client::find($request->client_id);
        Log::info('Client ID from request: ' . $request->client_id);

        // Check if the client exists
        if (!$client) {
            // Handle the case where the client does not exist
            return response()->json([
                'status' => false,
                'message' => 'Client not found. Please select a valid client.'
            ], 404);
        }

        // Create a new order associated with the client
        $order = new Order();
        $order->client_id = $client->id; // Use the client's ID
        $order->discount = $request->totalDiscount;
        $order->total_tax = $request->totalTax;
        $order->discount = $request->totalDiscount; // Save total discount
        $order->grand_total = $request->grandtotal;
        $order->subtotal = $request->subtotal;

        $order->first_name = $client->name; // Use client's name
        $order->email = $client->email; // Use client's email
        $order->mobile = $client->phone_number; // Use client's phone number
        $order->address = $client->address; // Use client's address
        $order->city = $client->city; // Use client's city
        $order->zip = $client->zip; // Use client's zip code

        // Save the order
        $order->save();
        foreach ($request->selectedProduct as $index => $productId) {
            $quantity = $request->new_Qty[$index];
            $discount = floatval($request->discount[$index]);
            $product = Product::find($productId);

            // Retrieve the discount value from the request



            // Calculate the amount before discount
            $amountBeforeDiscount = (($product->price * $quantity) + ($product->price * $quantity) * 0.18);

            // Calculate the discount amount
            $discountAmount = ($amountBeforeDiscount * $discount) / 100;

            // Calculate the discounted amount
            $discountedAmount = $amountBeforeDiscount - $discountAmount;

            // Calculate tax
            // $tax =  $discountedAmount;

            // Calculate the total amount
            $totalAmount = $discountedAmount;

            // Round the total amount to 2 decimal places
            $totalAmount = round($totalAmount, 2);

            // Create and save the order item
            $orderItem = new Order_item();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $productId;
            $orderItem->name = $product->title;
            $orderItem->qty = $quantity;
            $orderItem->discount = $discount;
            $orderItem->price = $product->price;
            $orderItem->total = $totalAmount;
            $orderItem->save();
        }

        return redirect()->route('getBill');
        // } catch (\Exception $e) {
        //     // Log the exception
        // Log::error('Error saving order: ' . $e->getMessage());

        //     // Return an error response
        //     return response()->json(['message' => 'An error occurred while processing the order.'], 500);
        // }
        // Your current code here


    }
    //     


    public function getBill()
    {
        // Retrieve the latest order along with its associated items
        $latestOrder = Order::with('items')->latest()->first();

        // Ensure that an order exists
        if ($latestOrder) {
            // Retrieve the order data
            $order = $latestOrder->toArray();

            // Retrieve the order items associated with the order
            $orderItems = $latestOrder->items->toArray();
        } else {
            // If no order exists, set empty arrays
            $order = [];
            $orderItems = [];
        }

        // Combine order data and order items data into a single variable
        $orderData = [
            'order' => $order,
            'orderItems' => $orderItems,
        ];


        // Pass the combined data to the view
        return view('admin.invoice.invoiceBill', compact('orderData'));
    }



    public function  viewPDF()
    {
        $latestOrder = Order::with('items')->latest()->first();

        // Ensure that an order exists
        if ($latestOrder) {
            // Retrieve the order data
            $order = $latestOrder->toArray();

            // Retrieve the order items associated with the order
            $orderItems = $latestOrder->items->toArray();
        } else {
            // If no order exists, set empty arrays
            $order = [];
            $orderItems = [];
        }

        // Combine order data and order items data into a single variable
        $orderData = [
            'order' => $order,
            'orderItems' => $orderItems,
        ];

        $pdf = PDF::loadView('admin.pdf.pdfview', compact('orderData'))->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    public function downloadPDF(){
        $latestOrder = Order::with('items')->latest()->first();

        // Ensure that an order exists
        if ($latestOrder) {
            // Retrieve the order data
            $order = $latestOrder->toArray();

            // Retrieve the order items associated with the order
            $orderItems = $latestOrder->items->toArray();
        } else {
            // If no order exists, set empty arrays
            $order = [];
            $orderItems = [];
        }

        // Combine order data and order items data into a single variable
        $orderData = [
            'order' => $order,
            'orderItems' => $orderItems,
        ];
        $pdf = PDF::loadView('admin.pdf.pdfview', compact('orderData'))->setPaper('a4', 'portrait');
         
        return $pdf->download('admin.pdf.pdfview');

    }




}
