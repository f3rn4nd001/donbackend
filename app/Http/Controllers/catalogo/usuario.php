<?php

namespace App\Http\Controllers\catalogo;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class usuario extends Controller
{
    public function objeto_a_array($data){
        if (is_array($data) || is_object($data)){
            $result = array();
            foreach ($data as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
            return $result;
        }
        return $data;
    }
    public function postRegistro(Request $request){
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach ($request['datos'] as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
             $result;
            if (count($result['Celulares']) > 0) {
                foreach ($result['Celulares'] as $key => $valuenewCel){
                    $newCel[$key] = $this->objeto_a_array($valuenewCel);
                }
            }
            if (count($result['Mails']) > 0) {
                foreach ($result['Mails'] as $key => $valuenewMails){
                    $newMails[$key] = $this->objeto_a_array($valuenewMails);
                }
            }
        }
        DB::beginTransaction();
        try {
            $ecodUsuarios = (isset($result['ecodUsuarios'])&&$result['ecodUsuarios']!="" ? "'".(trim($result['ecodUsuarios']))."'":   "NULL");     
            $exito = 1;
            $uuid = Uuid::uuid4();
            $uuid2 = (isset($uuid)&&$uuid!="" ? "'".(trim($uuid))."'":   "NULL");
            $tRFC = (isset($result['tRFC'])&&$result['tRFC']!="" ? "'".(trim($result['tRFC']))."'":   "NULL");
            $tNombre = (isset($result['tNombre'])&&$result['tNombre']!="" ? "'".(trim($result['tNombre']))."'":   "NULL");
            $tApellido = (isset($result['tApellido'])&&$result['tApellido']!="" ? "'".(trim($result['tApellido']))."'":   "NULL");
            $EcodEstatus = 1;
            $loginEcodUsuarios = (isset($result['loginEcodUsuarios'])&&$result['loginEcodUsuarios']!="" ? "'".(trim($result['loginEcodUsuarios']))."'":   "NULL");
            if ($ecodUsuarios == 'NULL') {
                $insert=" CALL `stpInsertarUsuario`(".$tRFC.",".$tNombre.",".$tApellido.",".$EcodEstatus.",".$uuid2.",".$loginEcodUsuarios.")";
                $response = DB::select($insert);
            }
            else {
                $insert=" CALL `stpInsertarUsuario`(".$tRFC.",".$tNombre.",".$tApellido.",".$EcodEstatus.",".$ecodUsuarios.",".$loginEcodUsuarios.")";
                $response = DB::select($insert);
            }
            $codigope = (isset($response[0]->Codigo)&&$response[0]->Codigo!="" ? "'".(trim($response[0]->Codigo))."'":   "NULL");
            $codigope2 = (isset($response[0]->Codigo)&&$response[0]->Codigo!="" ? "".(trim($response[0]->Codigo))."":   "NULL");
            if (count($result['Celulares']) > 0) {
                foreach ($newCel as $key => $s){
                    $telefono = (isset($s['telefono'])&& $s['telefono']>0  ? (int)$s['telefono']  : "NULL");
                    $incercel =" CALL `stpInsertarBitCelular`(".$telefono.")";
                    $responseincercel = DB::select($incercel);
                    $codigoCel  = (isset($responseincercel[0]->Codigo)&& $responseincercel[0]->Codigo>0  ? (int)$responseincercel[0]->Codigo : "NULL");
                    $incerrelucercel =" CALL `stpInsertarRelusUarioCelular`(".$codigope.",".$codigoCel.")";
                    $responsincerrelucercel = DB::select($incerrelucercel);
                    $codigoRelCel  = (isset($responsincerrelucercel[0]->Codigo)&& $responsincerrelucercel[0]->Codigo>0  ? (int)$responsincerrelucercel[0]->Codigo : "NULL"); 
                    if (!$codigoRelCel) {
                        $exito = 0;
                    }
                }
            }

        if (count($result['Mails']) > 0) {
            foreach ($newMails as $key => $s){
                $tCorreo = (isset($s['gmail'])&&$s['gmail']!="" ? "'".(trim($s['gmail']))."'":   "NULL");
                $Codigomail  = (isset($s['Codigomail'])&& $s['Codigomail']>0  ? (int)$s['Codigomail']  : "NULL"); 
                $tpasswordv = (isset($s['tpasswordv'])&&$s['tpasswordv']!="" ? "'".(trim($s['tpasswordv']))."'":   "NULL");
                $insertMails =" CALL `stpInsertarBitCorreo`(".$tCorreo.",".$Codigomail.")";
                $responseincerMails = DB::select($insertMails);
                $codigoCoreeo = (isset($responseincerMails[0]->Codigo)&& $responseincerMails[0]->Codigo>0  ? (int)$responseincerMails[0]->Codigo : "NULL");
                if ($codigope != "'0'") {
                    $incerrelucerCorreo=" CALL `stpInsertarRelusUarioCorreo`(".$codigope.",".$codigoCoreeo.")";
                    $responsincerrelucerCorreo = DB::select($incerrelucerCorreo);
                    $codigoRelCoreeo = (isset($responsincerrelucerCorreo[0]->Codigo)&& $responsincerrelucerCorreo[0]->Codigo>0  ? (int)$responsincerrelucerCorreo[0]->Codigo : "NULL"); 
                    if (!$codigoRelCoreeo) {
                        $exito = 0;
                    }
                }
                
            }
        }
        
        if (!$codigope ) {
            $exito = 0;
        }
        if ($exito == 0) {
            DB::rollback();
        } else {
            DB::commit();
        }
    } catch (Exception $e) {
        DB::rollback();
        $exito = $e->getMessage();
    }
     return response()->json(['codigo' => $codigope2, 'exito' => $exito]);
    }

    public function getRegistro(Request $request){
        $select="SELECT cu.ecodUsuarios,cu.trfc,cu.fCreacion, ce.tNombre AS estatus, concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cu.EcodEstatus ";

        $sql = DB::select(DB::raw($select));
        return response()->json(($sql));
    }
    
    public function getCompremento(Request $request){
        $select="SELECT concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres ,cu.ecodUsuarios FROM catusuarios cu
        WHERE cu.EcodEstatus=1";
             $sql = DB::select(DB::raw($select));
             return response()->json([$sql]); 
    }

    
    public function getRFC(Request $request){
        $json = json_decode( $request['datos'] ,true);
        $trfc    = (isset($json) && $json != "" ? "'" . (trim($json)) . "'" : "");
        $selectrfc="SELECT count(*) AS dl  FROM catusuarios  WHERE trfc =".$trfc;
        $sql = DB::select(DB::raw($selectrfc));
      return response()->json(['sql'=>$sql[0]]);
    }
    public function getDetalles(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json = (isset($jsonX)&&$jsonX!="" ? "'".(trim($jsonX))."'":   "NULL");
                
        $select="SELECT cu.ecodUsuarios,cu.trfc,cu.fCreacion, ce.tNombre AS estatus, concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres,cu.tNombre,cu.tApellido,cu.ecodCreacion,concat_ws('',cue.tNombre,' ',cue.tApellido) AS eliminacion,cu.tMotivoEliminacion,cu.fhEliminacion ,concat_ws('',cued.tNombre,' ',cued.tApellido)AS edicion ,cu.fhEdicion  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cu.EcodEstatus
        LEFT JOIN catusuarios cue ON cue.ecodUsuarios = cu.ecodEliminacion
        LEFT JOIN catusuarios cued ON cued.ecodUsuarios = cu.ecoEdicion
        WHERE cu.ecodUsuarios = ".$json;
        $sql = DB::select(DB::raw($select));

        $selectRelCel="SELECT bc.*
        FROM relusuariocelular reluc
        LEFT JOIN bitcelular bc On bc.ecodCelular = reluc.ecodCelular            
        WHERE reluc.ecodUsuario = ".$json;
          $relcelres = DB::select(DB::raw($selectRelCel));
        
        $selectRelMails="SELECT bc.tcorreo,bc.ecodCorreo
          FROM relusuariocorreo reluc
          LEFT JOIN bitcorreos bc On bc.ecodCorreo = reluc.ecodCorreo            
          WHERE reluc.ecodUsuario =".$json;
            $relMailres = DB::select(DB::raw($selectRelMails));
       
        return response()->json(['sql'=>$sql[0],'relcelres'=>(isset($relcelres) ? $relcelres : ""),'relMailres'=>(isset($relMailres)? $relMailres:"")]);
    }

    public function postEliminar(Request $request){
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach ($request['datos'] as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
             $result;      
        }
        $ecodUsuarios = (isset($result['ecodUsuarios'])&&$result['ecodUsuarios']!="" ? "'".(trim($result['ecodUsuarios']))."'":   "NULL");
        $loginEcodUsuarios = (isset($result['loginEcodUsuarios'])&&$result['loginEcodUsuarios']!="" ? "'".(trim($result['loginEcodUsuarios']))."'":   "NULL");
        $tMotivo = (isset($result['tMotivo'])&&$result['tMotivo']!="" ? "'".(trim($result['tMotivo']))."'":   "NULL");       
        DB::beginTransaction();
        try {
            $exito = 1;
            $EliminarUsuaros =" CALL `stpEliminatUsuario`(".$ecodUsuarios.",".$loginEcodUsuarios.",".$tMotivo.")";
            $sqlEliminarUsuaros = DB::select($EliminarUsuaros);    
            $codigope = (isset($sqlEliminarUsuaros[0]->Codigo)&&$sqlEliminarUsuaros[0]->Codigo!="" ? "'".(trim($sqlEliminarUsuaros[0]->Codigo))."'":   "NULL");
          
            if (!$codigope ) {
                $exito = 0;
            }
            if ($exito == 0) {
                DB::rollback();
            } else {
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollback();
            $exito = $e->getMessage();
        }
       
        return response()->json(['codigo' => $sqlEliminarUsuaros, 'exito' => $exito]);
    }
}
