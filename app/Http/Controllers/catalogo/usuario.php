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
    public function getcompremento(Request $request) {
        $selecatEstatus="SELECT * FROM catestatus";
        $sqlcatEstatus = DB::select(DB::raw($selecatEstatus));
        $selectTipoUsuarios="SELECT * FROM cattipousuario";
        $sqlTipoUsuarios= DB::select(DB::raw($selectTipoUsuarios));
        return response()->json([ 'sqlcatEstatus'=>(isset($sqlcatEstatus) ? $sqlcatEstatus : ""), 'sqlTipoUsuarios'=>(isset($sqlTipoUsuarios) ? $sqlTipoUsuarios : "")]);
    }
    public function getRegistro(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json               = isset($jsonX['filtros']) ? $jsonX['filtros'] : [];
        $metodos            = isset($jsonX['metodos']) ? $jsonX['metodos'] : [];
        foreach ($json as $key => $value) {
            if(array_key_exists($key, $json) ){
				if ($value != ''){
					${$key} =$value ;
				}
			}
        }
       
        $select="SELECT ce.tNombre AS Estatus,cu.ecodUsuario,concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres, cu.tNombre,cu.tApellido,cu.tRFC,cu.tCRUP,ctu.tNombre AS TipoUsuario, cu.fhCreacion  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.ecodEstatus=cu.ecodEstatus
        LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario=cu.ecodTipoUsuario ".
        " WHERE 1=1 ".
        (isset($tRFC)        ? " AND cu.tRFC LIKE ('%".$tRFC."%')"        : '').
        (isset($metodos['orden']) ? 'ORDER BY '.$metodos['tMetodoOrdenamiento']." ".$metodos['orden'] : 'ASC')." ".
        (isset($metodos['eNumeroRegistros']) && (int)$metodos['eNumeroRegistros']>0 ? 'LIMIT '.$metodos['eNumeroRegistros'] : '');
        $sql = DB::select(DB::raw($select));


        return response()->json(($sql));
    }

    public function getDetalles(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json = (isset($jsonX)&&$jsonX!="" ? "'".(trim($jsonX))."'":   "NULL");
        $select="SELECT cu.ecodUsuario, cu.trfc,cu.tCRUP, cu.fhCreacion,cu.ecodEliminacion, ce.tNombre AS estatus, cu.ecodTipoUsuario,cu.EcodEstatus, concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombres,cu.tNombre,cu.tApellido,cu.ecodCreacion,concat_ws('',cue.tNombre,' ',cue.tApellido) AS eliminacion,cu.tMotivoEliminacion,cu.fhEliminacion ,concat_ws('',cued.tNombre,' ',cued.tApellido)AS edicion ,cu.fhEdicion,cu.ecoEdicion  FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cu.EcodEstatus
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cu.ecodEliminacion
        LEFT JOIN catusuarios cued ON cued.ecodUsuario = cu.ecoEdicion
        WHERE cu.ecodUsuario = ".$json;
        $sql = DB::select(DB::raw($select));
        $selectgmail="SELECT bc.ecodCorreo,bc.tCorreo, bc.fhCreacion, concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombreCreacion FROM relusuariocorreo ruc
            LEFT JOIN bitcorreo bc ON bc.ecodCorreo =ruc.ecodCorreo
            LEFT JOIN catusuarios cu ON cu.ecodUsuario = bc.ecodCreacion
            WHERE ruc.ecodUsuario =".$json;
        $sqlgmail = DB::select(DB::raw($selectgmail));
       
         return response()->json([ 'sqlusuario'=>(isset($sql[0]) ? $sql[0] : ""),'sqlgmail'=>(isset($sqlgmail) ? $sqlgmail : "") ]);
    }

    public function postRegistro(Request $request){
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach($request['datos'] as $key => $value){$result[$key] = $this->objeto_a_array($value);}
            $result; 
            if(count($result['arrCorreo']) > 0) {foreach($result['arrCorreo'] as $key => $valuenewarrCorreo){$newarrCorreo[$key] = $this->objeto_a_array($valuenewarrCorreo);}}
        }
        DB::beginTransaction();
        try {
            $exito = 1;
            $tRFC = (isset($result['usuario']['tRFC'])&&$result['usuario']['tRFC']!="" ? "'".(trim($result['usuario']['tRFC']))."'":   "NULL");
            $tNombre = (isset($result['usuario']['tNombre'])&&$result['usuario']['tNombre']!="" ? "'".(trim($result['usuario']['tNombre']))."'":   "NULL");
            $tApellido = (isset($result['usuario']['tApellido'])&&$result['usuario']['tApellido']!="" ? "'".(trim($result['usuario']['tApellido']))."'":   "NULL");
            $tCURP = (isset($result['usuario']['tCURP'])&&$result['usuario']['tCURP']!="" ? "'".(trim($result['usuario']['tCURP']))."'":   "NULL");
            $ecodUsuarios = (isset($result['usuario']['ecodUsuario'])&&$result['usuario']['ecodUsuario']!="" ? "'".(trim($result['usuario']['ecodUsuario']))."'":   "NULL");     
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$request['headers']['ecodCorreo'];
            $motivoEliminacion = (isset($result['usuario']['motivoEliminacion'])&&$result['usuario']['motivoEliminacion']!="" ? "'".(trim($result['usuario']['motivoEliminacion']))."'":   "NULL");
            $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario)); 
            foreach ($sqlEcodUsuario as $key => $v){
                $resultadosecodUsuario[]=array(
                    'ecodUsuario' => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
                );
            }
            $loginEcodUsuarios = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");
            if ($ecodUsuarios == 'NULL') {
                //crear usuario
                $uuid = Uuid::uuid4();
                $uuid2 = (isset($uuid)&&$uuid!="" ? "'".(trim($uuid))."'":   "NULL");
                $EcodEstatus = "'ubsvkbvukabvoeho8veowbve'";
                $ecodTipoUsuario = "'fwajf9fjf2o8fj38o2f8o3f'";
                $insert=" CALL `stpInsertarUsuario`(".$tCURP.",".$tRFC.",".$tNombre.",".$tApellido.",".$EcodEstatus.",".$ecodTipoUsuario.",".$uuid2.",".$loginEcodUsuarios.",".$motivoEliminacion.")";
                $response = DB::select($insert);
                $codigoUsuario = (isset($response[0]->Codigo)&& $response[0]->Codigo>0  ? (int)$response[0]->Codigo : "NULL");
                //crear correo y relusuariocorreo
                if (count($result['arrCorreo']) > 0) {
                    foreach ($newarrCorreo as $key => $s){
                        $tCorreo = (isset($s['correo'])&&$s['correo']!="" ? "'".(trim($s['correo']))."'":  "NULL");
                        $tcontraseña = (isset($s['contraseña'])&&$s['contraseña']!="" ? "'".(trim($s['contraseña']))."'":   "NULL");
                        $uuidcorreo = Uuid::uuid4();
                        $uuidCorreo2 = (isset($uuidcorreo)&&$uuidcorreo!="" ? "'".(trim($uuidcorreo))."'":   "NULL");
                        $insertMails =" CALL `stpInsertarBitCorreo`(".$uuidCorreo2.",".$tCorreo.",".$tcontraseña.",".$loginEcodUsuarios.")";
                        $responseincerMails = DB::select($insertMails);
                        $uuidrelusuariocorreo = Uuid::uuid4();
                        $uuidrelusuariocorreo2 = (isset($uuidrelusuariocorreo)&&$uuidrelusuariocorreo!="" ? "'".(trim($uuidrelusuariocorreo))."'":   "NULL");
                        $inserrelusuariocorreo=" CALL `stpInsertarrelusuariocorreo`(".$uuidrelusuariocorreo2.",".$uuidCorreo2.",".$uuid2.")";
                        $responseincerMails = DB::select($inserrelusuariocorreo); 
                    }
                }
              
                //crear telefono
            }
            else {
                //editar usuario
                $selectlogcatusuario="SELECT * FROM catusuarios cu WHERE cu.ecodUsuario =".$ecodUsuarios;
                $sqllogCatusuario = DB::select(DB::raw($selectlogcatusuario));
                //insercionde logs cat usuarios
                
                    $resultadosconsultalogs=array(
                        'ecodUsuario'       => ($sqllogCatusuario[0]->ecodUsuario         ? $sqllogCatusuario[0]->ecodUsuario       :""),
                        'tNombre'           => ($sqllogCatusuario[0]->tNombre             ? $sqllogCatusuario[0]->tNombre           :""),
                        'tApellido'         => ($sqllogCatusuario[0]->tApellido           ? $sqllogCatusuario[0]->tApellido         :""),
                        'tCRUP'             => ($sqllogCatusuario[0]->tCRUP               ? $sqllogCatusuario[0]->tCRUP             :""),
                        'tRFC'              => ($sqllogCatusuario[0]->tRFC                ? $sqllogCatusuario[0]->tRFC              :""),
                        'ecodEstatus'       => ($sqllogCatusuario[0]->ecodEstatus         ? $sqllogCatusuario[0]->ecodEstatus       :""),
                        'ecodTipoUsuario'   => ($sqllogCatusuario[0]->ecodTipoUsuario     ? $sqllogCatusuario[0]->ecodTipoUsuario   :""),
                        'fhCreacion'        => ($sqllogCatusuario[0]->fhCreacion          ? $sqllogCatusuario[0]->fhCreacion        :""),
                        'ecoEdicion'        => ($sqllogCatusuario[0]->ecoEdicion          ? $sqllogCatusuario[0]->ecoEdicion        :""),
                        'fhEdicion'         => ($sqllogCatusuario[0]->fhEdicion           ? $sqllogCatusuario[0]->fhEdicion         :""),
                        'tMotivoEliminacion'=> ($sqllogCatusuario[0]->tMotivoEliminacion  ? $sqllogCatusuario[0]->tMotivoEliminacion:""),
                        'fhEliminacion'     => ($sqllogCatusuario[0]->fhEliminacion       ? $sqllogCatusuario[0]->fhEliminacion     :""),
                        'ecodEliminacion'   => ($sqllogCatusuario[0]->ecodEliminacion     ? $sqllogCatusuario[0]->ecodEliminacion   :""),
                        'ecodCreacion'      => ($sqllogCatusuario[0]->ecodCreacion        ? $sqllogCatusuario[0]->ecodCreacion      :""),
                    );

                $logecodUsuario = (isset($resultadosconsultalogs['ecodUsuario'])&&$resultadosconsultalogs['ecodUsuario']!="" ? "'".(trim($resultadosconsultalogs['ecodUsuario']))."'":   "NULL");
                $logtNombre = (isset($resultadosconsultalogs['tNombre'])&&$resultadosconsultalogs['tNombre']!="" ? "'".(trim($resultadosconsultalogs['tNombre']))."'":   "NULL");
                $logtApellido = (isset($resultadosconsultalogs['tApellido'])&&$resultadosconsultalogs['tApellido']!="" ? "'".(trim($resultadosconsultalogs['tApellido']))."'":   "NULL");
                $logtCRUP = (isset($resultadosconsultalogs['tCRUP'])&&$resultadosconsultalogs['tCRUP']!="" ? "'".(trim($resultadosconsultalogs['tCRUP']))."'":   "NULL");
                $logtRFC = (isset($resultadosconsultalogs['tRFC'])&&$resultadosconsultalogs['tRFC']!="" ? "'".(trim($resultadosconsultalogs['tRFC']))."'":   "NULL");
                $logecodEstatus = (isset($resultadosconsultalogs['ecodEstatus'])&&$resultadosconsultalogs['ecodEstatus']!="" ? "'".(trim($resultadosconsultalogs['ecodEstatus']))."'":   "NULL");
                $logecodTipoUsuario = (isset($resultadosconsultalogs['ecodTipoUsuario'])&&$resultadosconsultalogs['ecodTipoUsuario']!="" ? "'".(trim($resultadosconsultalogs['ecodTipoUsuario']))."'":   "NULL");
                $logfhCreacion = (isset($resultadosconsultalogs['fhCreacion'])&&$resultadosconsultalogs['fhCreacion']!="" ? "'".(trim($resultadosconsultalogs['fhCreacion']))."'":   "NULL");
                $logecoEdicion = (isset($resultadosconsultalogs['ecoEdicion'])&&$resultadosconsultalogs['ecoEdicion']!="" ? "'".(trim($resultadosconsultalogs['ecoEdicion']))."'":   "NULL");
                $logfhEdicion = (isset($resultadosconsultalogs['fhEdicion'])&&$resultadosconsultalogs['fhEdicion']!="" ? "'".(trim($resultadosconsultalogs['fhEdicion']))."'":   "NULL");
                $logecodCreacion = (isset($resultadosconsultalogs['ecodCreacion'])&&$resultadosconsultalogs['ecodCreacion']!="" ? "'".(trim($resultadosconsultalogs['ecodCreacion']))."'":   "NULL");
                $logtMotivoEliminacion = (isset($resultadosconsultalogs['tMotivoEliminacion'])&&$resultadosconsultalogs['tMotivoEliminacion']!="" ? "'".(trim($resultadosconsultalogs['tMotivoEliminacion']))."'":   "NULL");
                $logfhEliminacion = (isset($resultadosconsultalogs['fhEliminacion'])&&$resultadosconsultalogs['fhEliminacion']!="" ? "'".(trim($resultadosconsultalogs['fhEliminacion']))."'":   "NULL");
                $logecodEliminacion = (isset($resultadosconsultalogs['ecodEliminacion'])&&$resultadosconsultalogs['ecodEliminacion']!="" ? "'".(trim($resultadosconsultalogs['ecodEliminacion']))."'":   "NULL");
                $loguuid = Uuid::uuid4();
                $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "'".(trim($loguuid))."'":   "NULL");
                $insertarLogUsusario=" CALL `stpInsertarLogUsusario`(".$loguuid2.",".$logecodUsuario.",".$logtNombre.",".$logtApellido.",".$logtCRUP.",".$logtRFC.",".$logecodEstatus.",".$logecodTipoUsuario.",".$logfhCreacion.",".$logecoEdicion.",".$logfhEdicion.",".$logecodCreacion.",".$logtMotivoEliminacion.",".$logfhEliminacion.",".$logecodEliminacion.")";
                $responseinsertarLogUsusario = DB::select($insertarLogUsusario);
                $ecodTipoUsuario = (isset($result['usuario']['ecodTipoUsuario'])&&$result['usuario']['ecodTipoUsuario']!="" ? "'".(trim($result['usuario']['ecodTipoUsuario']))."'":   "NULL");
                $EcodEstatus = (isset($result['usuario']['EcodEstatus'])&&$result['usuario']['EcodEstatus']!="" ? "'".(trim($result['usuario']['EcodEstatus']))."'":   "NULL");
                $insert=" CALL `stpInsertarUsuario`(".$tCURP.",".$tRFC.",".$tNombre.",".$tApellido.",".$EcodEstatus.",".$ecodTipoUsuario.",".$ecodUsuarios.",".$loginEcodUsuarios.",".$motivoEliminacion.")";
                $response = DB::select($insert);
                if (count($result['arrCorreo']) > 0) {
                    $tCorreo = (isset($s['correo'])&&$s['correo']!="" ? "'".(trim($s['correo']))."'":  "NULL");
                    $ecodCorreo = (isset($s['ecodCorreo'])&&$s['ecodCorreo']!="" ? "'".(trim($s['ecodCorreo']))."'":   "NULL");
                }
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
