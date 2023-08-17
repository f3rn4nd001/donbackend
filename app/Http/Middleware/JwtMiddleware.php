<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use DB;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      

       $selectEcodCorreo = "SELECT * FROM bitcorreo bc WHERE bc.ecodCorreo = ".$request['headers']['ecodCorreo']."
       AND bc.tToken = ".$request['headers']['token'];
       $sqlEcodCorreo = DB::select(DB::raw($selectEcodCorreo)); 
       if($sqlEcodCorreo){
            foreach ($sqlEcodCorreo as $key => $v){
                $resultadosEcodCorreo[]=array(
                    'ecodCorreo'  => ($v->ecodCorreo   ? $v->ecodCorreo    : ""),
                );
            }
            
            $ecodCorreo   = (isset($resultadosEcodCorreo[0]['ecodCorreo']) && $resultadosEcodCorreo[0]['ecodCorreo'] != "" ? "'" . (trim($resultadosEcodCorreo[0]['ecodCorreo'])) . "'" : "");             
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$ecodCorreo;
            $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario));          
            foreach ($sqlEcodUsuario as $key => $v){
                $resultadosecodUsuario[]=array(
                    'ecodUsuario'  => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
                );
            }
            $ecodUsuario = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");             
            $selectact="SELECT ce.tNombre as Estatus FROM catusuarios cu 
            LEFT JOIN catestatus ce ON ce.ecodEstatus = cu.ecodEstatus
            WHERE cu.ecodUsuario=".$ecodUsuario;
            $sqlact = DB::select(DB::raw($selectact));          
            foreach ($sqlact as $key => $v){
                $resultadosact[]=array(
                    'Estatus'  => ($v->Estatus   ? $v->Estatus    : ""),
                );
            }
            $Estatus   = (isset($resultadosact[0]['Estatus']) && $resultadosact[0]['Estatus'] != "" ? "'" . (trim($resultadosact[0]['Estatus'])) . "'" : "");             
            if ($Estatus == "'Activo'") {
                return $next($request);
            }
        }
        return response()->json([
            'mensaje'=>"Token invalido, Inicie sesion nuevamente",
        ],401);    }
}
