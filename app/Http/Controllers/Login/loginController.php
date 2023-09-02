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
   
    function posLogin(LoginReuest $request) {
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
                $exito = 1;
                $Email      = (isset($result['email']) && $result['email'] != "" ? "'" . (trim($result['email'])) . "'" : "");           
                if (preg_match('/^[a-zA-Z0-9.,"]+$/u', $result['password']) == 1) {
                    $password = (isset($result['password']) && $result['password'] != "" ? "'" . (trim($result['password'])) . "'" : "");
                }
                else { 
                    return response()->json([
                    'mensaje'=>"No dijite caracteres especiales ni espacios",
                ],401); 
                }
                $selectEcodCorreo = "SELECT * FROM bitcorreo bc WHERE bc.tCorreo =".$Email."AND bc.tpassword =".$password;
                $sqlEcodCorreo = DB::select(DB::raw($selectEcodCorreo)); 
                if($sqlEcodCorreo){
                    foreach ($sqlEcodCorreo as $key => $v){
                        $resultadosEcodCorreo[]=array(
                            'ecodCorreo'  => ($v->ecodCorreo   ? $v->ecodCorreo    : ""),
                        );
                    }
                    $ecodCorreo   = (isset($resultadosEcodCorreo[0]['ecodCorreo']) && $resultadosEcodCorreo[0]['ecodCorreo'] != "" ? "'" . (trim($resultadosEcodCorreo[0]['ecodCorreo'])) . "'" : "");                                 $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$ecodCorreo;
                    $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario));          

                    foreach ($sqlEcodUsuario as $key => $v){
                        $resultadosecodUsuario[]=array(
                            'ecodUsuario'  => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
                        );
                    }

                    $ecodUsuario = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");             
                    $selectact="SELECT ce.tNombre as Estatus, ctu.tNombre AS TipoUsuario FROM catusuarios cu 
                    LEFT JOIN catestatus ce ON ce.ecodEstatus = cu.ecodEstatus
                    LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario = cu.ecodTipoUsuario
                    WHERE cu.ecodUsuario=".$ecodUsuario;
                    $sqlact = DB::select(DB::raw($selectact));
                    foreach ($sqlact as $key => $v){
                        $resultadosact[]=array(
                            'Estatus'  => ($v->Estatus   ? $v->Estatus    : ""),
                            'TipoUsuario'  => ($v->TipoUsuario   ? $v->TipoUsuario    : ""),
                        );
                    }
                    $Estatus = (isset($resultadosact[0]['Estatus']) && $resultadosact[0]['Estatus'] != "" ? "'" . (trim($resultadosact[0]['Estatus'])) . "'" : "");             
                    if ($Estatus == "'Activo'") {
                        $user=User::all()->where('tCorreo', $result['email'] )->first();
                        $token=JWTAuth::fromUser($user);
                        $tokenv   = (isset($token) && $token != "" ? "'" . (trim($token)) . "'" : "");             
                        $insert=" CALL `stpInsertarLogin`(".$ecodCorreo.",".$tokenv.")";
                        $response = DB::select($insert);
                        $selectMenu="SELECT cm.tNombre AS Menu, cm.Iconos,cs.tNombre AS submenuNombre, cs.tUrl as urlSubMenu, ctp.tNombre AS Permisos, ctp.tNombreCorto AS PermisosCorto, cct.tNombre AS nombreController, cct.turl AS urlController 
                        FROM relusuariomenusubmenupermisoscontroller rumspc 
                            LEFT JOIN catmenu cm ON cm.ecodMenu= rumspc.ecodMenu 
                            LEFT JOIN catsubmenu cs ON cs.ecodSubmenu = rumspc.ecodSubmenu
                            LEFT JOIN catpermisos ctp on ctp.ecodPermisos = rumspc.ecodPermisos
                            LEFT JOIN cotcontroller cct on cct.ecodControler = rumspc.ecodController
                            WHERE rumspc.ecodUsuario=".$ecodUsuario;
                        $sqlMenu = DB::select(DB::raw($selectMenu)); 
                        foreach ($sqlMenu as $key => $v){
                            $arrsqlmenu[]=array(
                                'Menu' => $v->Menu,
                                'submenu'=>$v->submenuNombre,
                                'urlSubMenu'=>$v->urlSubMenu,
                                'Permisos'=>$v->Permisos,
                                'PermisosCorto'=>$v->PermisosCorto,
                                'Controller' => $v->nombreController,
                                'urlController'=>$v->urlController,
                                'Iconos'=>$v->Iconos
                            );
                        }         
                        $exito = 0;
                    }
                    else {
                        return response()->json([
                            'mensaje'=>"Esta cuenta no se encuentra activa",
                        ],202);
                    }
                }
                else {
                    return response()->json([
                        'mensaje'=>"Usuario o contraseña imbalida",
                    ],401);
                }
                if ($exito == 0) {
                    DB::rollback();
                } 
                else {
                    DB::commit();
                }
            }
            catch (Exception $e) {
                DB::rollback();
                $exito = $e->getMessage();
            }
            return response()->json(['token' => $token, 'Menu'=>(isset($arrsqlmenu) ? $arrsqlmenu : ""), 'ecodCorreo'=>(isset($resultadosEcodCorreo[0]['ecodCorreo']) ? $resultadosEcodCorreo[0]['ecodCorreo'] : ""),  'TipoUsuario'=>(isset($resultadosact[0]['TipoUsuario']) ? $resultadosact[0]['TipoUsuario'] : "")]);
        }
        return response()->json(['mensaje' => "No cuenta con los permisos"]);

    }    


    function postValidadContrasena(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $contrasena    = (isset($jsonX['contrasena']) && $jsonX['contrasena'] != "" ? "'" . (trim($jsonX['contrasena'])) . "'" : "");           
        $ecodCorreo    = (isset($jsonX['ecodCorreo']) && $jsonX['ecodCorreo'] != "" ? "'" . (trim($jsonX['ecodCorreo'])) . "'" : "");           
        $selectcontra="SELECT count(*) AS dl FROM bitcorreo bc WHERE bc.ecodCorreo = ".$jsonX['ecodCorreo']."  AND bc.tpassword =".$contrasena;
        $sqlcontra = DB::select(DB::raw($selectcontra));
        return response()->json(['valContra'=>$sqlcontra[0]]);
    }
}
