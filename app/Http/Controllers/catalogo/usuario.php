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

    public function getRegistro(Request $request){
        $select="SELECT ce.tNombre AS Estatus,cu.ecodUsuario,cu.tNombre,cu.tApellido,cu.tRFC,cu.tCRUP,ctu.tNombre AS TipoUsuario, cu.fhCreacion  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.ecodEstatus=cu.ecodEstatus
        LEFT JOIN cattipousuario ctu ON ctu.ecotTipoEsuario=cu.ecodTipoUsuario ";
        $sql = DB::select(DB::raw($select));
        return response()->json(($sql));
    }

    public function getDetalles(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json = (isset($jsonX)&&$jsonX!="" ? "'".(trim($jsonX))."'":   "NULL");
        $select="SELECT cu.ecodUsuario, cu.trfc,cu.tCRUP, cu.fhCreacion, ce.tNombre AS estatus, concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres,cu.tNombre,cu.tApellido,cu.ecodCreacion,concat_ws('',cue.tNombre,' ',cue.tApellido) AS eliminacion,cu.tMotivoEliminacion,cu.fhEliminacion ,concat_ws('',cued.tNombre,' ',cued.tApellido)AS edicion ,cu.fhEdicion  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cu.EcodEstatus
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cu.ecodEliminacion
        LEFT JOIN catusuarios cued ON cued.ecodUsuario = cu.ecoEdicion
        WHERE cu.ecodUsuario = ".$json;
         $sql = DB::select(DB::raw($select));
         return response()->json([ 'sqlusuario'=>(isset($sql[0]) ? $sql[0] : "") ]);
    }

    public function postRegistro(Request $request){
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach ($request['datos'] as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
            $result; 
            if (count($result['arrCorreo']) > 0) {
                foreach ($result['arrCorreo'] as $key => $valuenewarrCorreo){
                    $newarrCorreo[$key] = $this->objeto_a_array($valuenewarrCorreo);
                }
            }
            if (count($result['arrTelefono']) > 0) {
                foreach ($result['arrTelefono'] as $key => $valuenewarrTelefono){
                    $newarrTelefono[$key] = $this->objeto_a_array($valuenewarrTelefono);
                }
            }
        }
        DB::beginTransaction();
        try {
            $exito = 1;
            $tRFC = (isset($result['usuario']['tRFC'])&&$result['usuario']['tRFC']!="" ? "'".(trim($result['usuario']['tRFC']))."'":   "NULL");
            $tNombre = (isset($result['usuario']['tNombre'])&&$result['usuario']['tNombre']!="" ? "'".(trim($result['usuario']['tNombre']))."'":   "NULL");
            $tApellido = (isset($result['usuario']['tApellido'])&&$result['usuario']['tApellido']!="" ? "'".(trim($result['usuario']['tApellido']))."'":   "NULL");
            $tCURP = (isset($result['usuario']['tCURP'])&&$result['usuario']['tCURP']!="" ? "'".(trim($result['usuario']['tCURP']))."'":   "NULL");
            $ecodUsuarios = (isset($result['usuario']['ecodUsuarios'])&&$result['usuario']['ecodUsuarios']!="" ? "'".(trim($result['usuario']['ecodUsuarios']))."'":   "NULL");     
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$request['headers']['ecodCorreo'];
            $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario)); 
            foreach ($sqlEcodUsuario as $key => $v){
                $resultadosecodUsuario[]=array(
                    'ecodUsuario'  => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
                );
            }
            $loginEcodUsuarios = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");
            if ($ecodUsuarios == 'NULL') {
                $uuid = Uuid::uuid4();
                $uuid2 = (isset($uuid)&&$uuid!="" ? "'".(trim($uuid))."'":   "NULL");
                $EcodEstatus = "'ubsvkbvukabvoeho8veowbve'";
                $ecodTipoUsuario = "'fwajf9fjf2o8fj38o2f8o3f'";
                $insert=" CALL `stpInsertarUsuario`(".$tCURP.",".$tRFC.",".$tNombre.",".$tApellido.",".$EcodEstatus.",".$ecodTipoUsuario.",".$uuid2.",".$loginEcodUsuarios.")";
                $response = DB::select($insert);
            }
            else {
                $ecodTipoUsuario = (isset($result['usuario']['ecodTipoUsuario'])&&$result['usuario']['ecodTipoUsuario']!="" ? "'".(trim($result['usuario']['ecodTipoUsuario']))."'":   "NULL");
                $ecodEstatus = (isset($result['usuario']['EcodEstatus'])&&$result['usuario']['EcodEstatus']!="" ? "'".(trim($result['usuario']['EcodEstatus']))."'":   "NULL");
                $insert=" CALL `stpInsertarUsuario`(".$tCURP.",".$tRFC.",".$tNombre.",".$tApellido.",".$ecodEstatus.",".$ecodTipoUsuario.",".$ecodUsuarios.",".$loginEcodUsuarios.")";
                $response = DB::select($insert);
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
        return response()->json([$response[0], 'exito' => $exito]);
    }
}
