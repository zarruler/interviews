<?php

namespace App\Http\Controllers;

use App\Models\{CatalogProduct, CatalogCategory};
use App\Rules\{TokenExists, CategoryExists, ProductExists};

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CatalogProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  integer product ID
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['bail', 'required', 'integer', new ProductExists()],
        ]);
        if ($validator->fails())
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);

        $product = CatalogProduct::find($id)->toArray();
        $product['categories'] = CatalogProduct::find($id)->categories()->get()->toArray();

        return response()->json(['status'=>__('errors.status_ok'), 'data'=>$product], Response::HTTP_OK);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  integer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->route('id');
        $validator = Validator::make(array_merge(['id' => $id], $request->all()), [
            'id'        => ['bail', 'nullable', 'integer', new CategoryExists()],
            'name'      => 'bail|required|max:255',
            'price'     => 'bail|required|regex:/^\d+(\.\d{1,2})?$/|max:8',
            'api_token' => ['bail', 'required', 'string', new TokenExists()],

        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $product = new CatalogProduct;
            $product->name = $validator->getData()['name'];
            $product->price = $validator->getData()['price'];
            $product->save();

            // if adding product to the category then attaching it to category in the pivot table.
            if(!empty($id))
                $product->categories()->attach($id);

        } catch(QueryException $e){
            return response()->json( ['status' =>__('errors.status_error'),
                'message'=>__('errors.db_any')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status'=>__('errors.status_ok'), 'id'=>$product->id], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogProduct  $catalogProduct
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogProduct $catalogProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogProduct  $catalogProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogProduct $catalogProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->route('id');
        $validator = Validator::make(array_merge(['id' => $id], $request->all()), [
            'id'        => ['bail', 'required', 'integer', new ProductExists()],
            'name'      => 'required|string|max:255',
            'price'     => 'required|regex:/^\d+(\.\d{1,2})?$/|max:8',
            'api_token' => 'required|string|exists:users,api_token',
        ] );

        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $product = CatalogProduct::find($id);
            $product->name = $request->input('name');
            $product->price = $request->input('price');
            $product->save();
        } catch(QueryException $e){
            return response()->json( ['status' =>__('errors.status_error'),
                'message'=> __('errors.db_any')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status'=>__('errors.status_ok'), 'id'=>$product->id], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $validator = Validator::make(array_merge(['id' => $id], $request->all()), [
            'id' => 'required|integer',
            'api_token' => ['required', 'string', new TokenExists()],
        ] );

        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        if(!CatalogProduct::destroy($id)){
            return response()->json(['status'=>__('errors.status_error')], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status'=>__('errors.status_ok')], Response::HTTP_OK);

    }
}
