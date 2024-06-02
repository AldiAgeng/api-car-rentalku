<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($request->search) {
            $cars = Car::search($request->search)
                ->orderBy($sortField, $sortOrder)
                ->paginate($perPage);
        } else {
            $cars = Car::query()
                ->orderBy($sortField, $sortOrder)
                ->paginate($perPage);
        }

        return ResponseFormatter::success(
            $cars,
            'Data list cars berhasil diambil'
        );
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'merk' => 'required',
            'model' => 'required',
            'number_plate' => 'required',
            'price_per_day' => 'required|integer',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($validation->fails()) {
            return ResponseFormatter::error(
                $validation->errors()->all(),
                'Gagal menambahkan mobil',
                400
            );
        }

        $file = $request->file('image');
        $filename = $file->getClientOriginalName();
        $file->move(public_path('storage/images'), $filename);

        $car = Car::create([
            'merk' => $request->merk,
            'model' => $request->model,
            'number_plate' => $request->number_plate,
            'price_per_day' => $request->price_per_day,
            'image' => $filename,
        ]);

        return ResponseFormatter::success(
            $car,
            'Berhasil ditambahkan'
        );
    }
}
