<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            "address" => 'required',
            "birthday" => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }

        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            "addres" => $request->get("address"),
            "birthday" => $request->get("birthday"),
        ];

        try {
            $insert = User::create($data);
            return Response()->json([
                "status" => true,
                'message' => 'Data berhasil ditambahkan'
            ]);
        } catch (Exception $e) {
            return Response()->json(["status" => false, 'message' => $e]);
        };
    }
    function getUser()
    {
        try {
            $user = User::get();
            return Response()->json([
                "status" => true,
                'message' => 'berhasil load data user',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return Response()->json([
                'status' => false,
                'message' => 'gagal load data user.' . $e,
            ]);
        }
    }
    function getDetailUser($id)
    {
        try {
            $user = User::where('id', $id)->first();
            return Response()->json([
                'status' => true,
                'message' => 'berhasil load data detail user',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return Response()->json([
                'status' => false,
                'message' => 'gagal load data detail user' . $e,
            ]);
        }
    }
    function update_User($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($id)],
            "address" => 'required',
            "birthday" => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            "addres" => $request->get("address"),
            "birthday" => $request->get("birthday"),
        ];
        try{
            $update = User::where('id',$id)->update($data);
            return Response()->json([
                "status" => true,
               'message' => 'Data berhasil diupdate'
            ]);
        }catch(Exception $e){
            return Response()->json([
                'status' => false,
               'message' => 'gagal update data user' . $e,
            ]);
        }
    }
    function hapus_user($id){
        try{
            User::where('id',$id)->delete();
            return Response()->json([
                "status" => true,
               'message' => 'Data berhasil dihapus'
            ]);
        }catch(Exception $e){
            return Response()->json([
                'status' => false,
               'message' => 'gagal hapus data user.' . $e,
            ]);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }


        $user = Auth::guard('api')->user();
        return response()->json([
                'status' => true,
                'message'=>'Sukses login',
                'data'=>$user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'status' => true,
            'message' => 'Sukses logout',
        ]);
    }

}
