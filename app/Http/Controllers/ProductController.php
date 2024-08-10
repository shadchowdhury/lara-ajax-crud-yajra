<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button data-bs-toggle="modal" data-bs-target="#editModal" class="btn-edit btn btn-info btn-sm" value="' . $row->id . '"><i class="fa fa-edit"></i></button> ';
                    $btn .= '<button data-bs-toggle="modal" data-bs-target="#deleteModal" class="btn-delete btn btn-danger btn-sm" value="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manage');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'description' => ['required']
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            "status" => "Product Saved Successfully"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::find($id);

        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required'],
            'description' => ['required']
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;

        $product->update();

        return response()->json([
            "status" => "Product Updated Successfully"
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $product->delete();

        if (!$product) {
            return response()->json([
                "status" => "failed",
                "msg" => "Something went wrong!"
            ], 210);
        } else {
            return response()->json([
                "status" => "success",
                "msg" => "Product Deleted Successfully"
            ], 201);
        }
    }
}
