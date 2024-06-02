<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Car;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->user()->id)->with('car')->get();
        return ResponseFormatter::success($orders, 'Orders retrieved successfully');
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date',
        ]);

        if ($validation->fails()) {
            return ResponseFormatter::error(null, $validation->errors()->all(), 400);
        }

        $car = Car::findOrFail($request->car_id);

        if ($car->is_available == false) {
            return ResponseFormatter::error(null, 'Car is not available', 400);
        }

        $pickup_date = Carbon::parse($request->pickup_date);
        $return_date = Carbon::parse($request->return_date);

        $totalDays = $pickup_date->diffInDays($return_date) + 1;

        $totalPrice = $totalDays * $car->price_per_day;

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'car_id' => $request->car_id,
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
            'total_price' => $totalPrice,
        ]);

        $car->update(['is_available' => false]);

        return ResponseFormatter::success($order, 'Order created successfully');
    }

    public function returnCar($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'returned']);
        $order->car->update(['is_available' => true]);
        return ResponseFormatter::success($order, 'Car returned successfully');
    }
}
