<?php

namespace App\Http\Controllers;

use App\Models\CatalogCategory;
use App\Rules\{TokenExists, CategoryExists};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;


class CatalogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = CatalogCategory::all();
        return response()->json(['status'=>__('errors.status_ok'), 'data'=>$categories], Response::HTTP_OK);
    }

    /**
     * Display a listing of products in some category.
     *
     * @param  integer Category ID to show products from
     * @return \Illuminate\Http\Response
     */
    public function listProducts($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['bail', 'required', 'integer', new CategoryExists()],
        ]);
        if ($validator->fails())
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);

        $products = CatalogCategory::find($id)->products()->get();

        return response()->json(['status'=>__('errors.status_ok'), 'data'=>$products], Response::HTTP_OK);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|max:255',
            'api_token' => ['required', 'string', new TokenExists()],
        ] );

        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                                     'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $category = new CatalogCategory;
            $category->title = $validator->getData()['name'];
            $category->save();
        } catch(QueryException $e){
            return response()->json( ['status' =>__('errors.status_error'),
                                      'message'=>__('errors.db_any')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status'=>__('errors.status_ok'), 'id'=>$category->id], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ]);
        if ($validator->fails())
            return response()->json(['status'=>__('errors.status_error'),
                                     'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);

        $details = CatalogCategory::where(['id' => $id])->get();

        // if no results it is not an error, just absence of category with that id
        return response()->json(['status'=>__('errors.status_ok'), 'data'=>$details], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'api_token' => ['required', 'string', new TokenExists()],
        ] );

        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $category = CatalogCategory::find($id);
            $category->title = $request->input('name');
            $category->save();
        } catch(QueryException $e){
            return response()->json( ['status' =>__('errors.status_error'),
                'message'=> __('errors.db_any')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status'=>__('errors.status_ok'), 'id'=>$category->id], Response::HTTP_OK);
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

        if(!CatalogCategory::destroy($id)){
            return response()->json(['status'=>__('errors.status_error')], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status'=>__('errors.status_ok')], Response::HTTP_OK);

    }
}
