<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests\Auth\LoginReuest;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class loginController extends Controller
{
    public function objeto_a_array($data){
        if (is_array($data) || is_object($data)){
            $result = array();
            foreach ($data as $key => $value){$result[$key] = $this->objeto_a_array($value);}
            return $result;
        }
        return $data;
    }
    public function posLogin(Request $request){
        $dadsad = (isset($request['haders']) && $request['haders'] != "" ? "" . (trim($request['haders'])) . "" : "" );           
        if ($dadsad=="Ox_mSak@t~r}uh_GoerfQly_=EM$4iIYk#v4oFguL)TY2b0~O[") {
            if (is_array($request['datos']) || is_object($request['datos'])){
                $result = array();
                foreach ($request['datos'] as $key => $value){
                    $result[$key] = $this->objeto_a_array($value);
                }
                $result; 
            }
            try{

                $Email    = (isset($result['email']) && $result['email'] != "" ? "'" . (trim($result['email'])) . "'" : "");           
                $password    = (isset($result['password']) && $result['password'] != "" ? "'" . (trim($result['password'])) . "'" : "");           
                $logindata[]=array(
                    'tCorreo' => $Email,
                     'tpassword'=>$password
                  );
                  $csszcsscw = $logindata[0];
                
                if(!$token= JWTAuth::attempt($csszcsscw)){
                    return response()->json([
                        'error'=>'invalid credencial'
                    ],400);
                }
            }


            catch(JWTExeption $e){
                return response()->json([
                    'error'=>'no create credencial'
                ],500); 
            }
            return response()->json(compact('token'));       
        }
    }
    
    public function poscontras(Request $request)
    {
    $dadsad = (isset($request['haders']) && $request['haders'] != "" ? "" . (trim($request['haders'])) . "" : "" );           
    if ($dadsad=="Ox_mSak@t~r}uh_GoerfQly_=EM$4iIYk#v4oFguL)TY2b0~O[") {
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach ($request['datos'] as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
        }
        $ecodUsuario    = (isset($result['loginEcodUsuarios']) && $result['loginEcodUsuarios'] != "" ? "'" . (trim($result['loginEcodUsuarios'])) . "'" : "");           
        $password    = (isset($result['contrasena']) && $result['contrasena'] != "" ? "'" . (trim($result['contrasena'])) . "'" : "");           
        $selectcontra="SELECT count(*) AS dl FROM relusuariocorreo ruc
        LEFT JOIN bitcorreos bc ON bc.ecodCorreo = ruc.ecodCorreo
        WHERE ruc.ecodUsuario =".$ecodUsuario."
        AND bc.tpassword =".$password;
        $sqlcontra = DB::select(DB::raw($selectcontra));
        return response()->json(['sql'=>$sqlcontra[0]]);
        }
    }
    public function geta(Request $request){
        return response()->json(($sql));
    }
public function getc(Request $request){
    

    $sql = "c";
    return response()->json(($sql));
}

function register(Request $request) {
   
    $user =User::create([
        'ecodCorreo' =>$request->ecodCorreo,
        'tCorreo' =>$request->tCorreo,
        'tpassword' =>($request->tpassword)
    ]);    
    return response()->json([
        'user'=>$user,
    ],200);

}

    function login(LoginReuest $request) {
        $user=User::all()->where('tCorreo','12345678902@gmail.com' )->first();
      $token=JWTAuth::fromUser($user);
       return response()->json([
        'user'=>$user,
        'token'=>$token
    ],200);
    }
}
