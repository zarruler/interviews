<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use App\Rules\PasswordMatch;


class UserController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'bail|required|email|exists:users|max:100',
            'password'  => ['bail','required','alpha_dash','max:30'],
        ] );

        if(!$validator->fails()){
            $validator->after(function ($validator) use ($request) {
                $pwdMatch = new PasswordMatch($request->input('email'));
                if(!$pwdMatch->passes('email', $request->input('password'))){
                    $validator->errors()->add('password', $pwdMatch->message());
                }
            });
        }


        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('email', '=', $request->input('email'))->first();

        return response()->json(['status'=>__('errors.status_ok'), 'api_token'=>$user->api_token], Response::HTTP_OK);

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
            'name'     => 'bail|required|regex:/^[\pL\s\-]+$/u|max:100',
            'password'  => 'bail|required|alpha_dash|max:30',
            'email'     => 'bail|required|email|unique:users|max:100',
        ] );

        if ($validator->fails()) {
            return response()->json(['status'=>__('errors.status_error'),
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = new User;
            $user->name = $validator->getData()['name'];
            $user->password = Hash::make($validator->getData()['password']);
            $user->email = $validator->getData()['email'];
            $user->api_token = md5token();
            $user->save();
        } catch(QueryException $e){
            return response()->json( ['status' =>__('errors.status_error'),
                'message'=>__('errors.db_any')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['status'=>__('errors.status_ok'), 'api_token'=>$user->api_token], Response::HTTP_OK);
    }

}
