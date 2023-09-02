<?php

namespace App\Http\Controllers\catalogo;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class menu extends Controller
{
    public function objeto_a_array($data){
        if (is_array($data) || is_object($data)){
            $result = array();
            foreach ($data as $key => $value){$result[$key] = $this->objeto_a_array($value);}
            return $result;
        }
        return $data;
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
        
        $selectMenu="SELECT * FROM catmenu". " WHERE 1=1 ".  
        (isset($ecodMenu)       ? " AND ecodMenu LIKE ('%".$ecodMenu."%')"        : '').
        (isset($tNombre)        ? " AND tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($metodos['orden']) ? 'ORDER BY '.$metodos['tMetodoOrdenamiento']." ".$metodos['orden'] : 'ASC')." ".
        (isset($metodos['eNumeroRegistros']) && (int)$metodos['eNumeroRegistros']>0 ? 'LIMIT '.$metodos['eNumeroRegistros'] : '');
        $sql = DB::select(DB::raw($selectMenu));
        return response()->json(($sql));
    }

    public function postRegistro(Request $request){
        if (is_array($request['datos']) || is_object($request['datos'])){
            $result = array();
            foreach($request['datos'] as $key => $value){$result[$key] = $this->objeto_a_array($value);}
            $result;
        }
        
        $Iicons = (isset($result['Menu']['Iicons'])&&$result['Menu']['Iicons']!="" ? "'".(trim($result['Menu']['Iicons']))."'":   "NULL");
        $tNombre = (isset($result['Menu']['tNombre'])&&$result['Menu']['tNombre']!="" ? "'".(trim($result['Menu']['tNombre']))."'":   "NULL");
        $ecodMenu = (isset($result['Menu']['ecodMenu'])&&$result['Menu']['ecodMenu']!="" ? "'".(trim($result['Menu']['ecodMenu']))."'":   "NULL");
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$request['headers']['ecodCorreo'];
        $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario)); 
        foreach ($sqlEcodUsuario as $key => $v){
            $resultadosecodUsuario[]=array(
                'ecodUsuario' => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
            );
        }
        $loginEcodUsuarios = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");
         if ($ecodMenu == 'NULL') {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "'".(trim($uui))."'":   "NULL");
            $ecodEstatus = "'ubsvkbvukabvoeho8veowbve'";
            $inserMenu=" CALL `stpInsertarCatMenu`(".$uuid2.",".$tNombre.",".$Iicons.",".$ecodEstatus.",".$loginEcodUsuarios.")";
            $responseMenu = DB::select($inserMenu); 
        }
        else{
            $selectlogcatMenu="SELECT * FROM catmenu cu WHERE cu.ecodMenu =".$ecodMenu;
            $sqllogCatMenu = DB::select(DB::raw($selectlogcatMenu));
            $resultadosconsultalogs=array(
                'ecodMenu'      => ($sqllogCatMenu[0]->ecodMenu       ? $sqllogCatMenu[0]->ecodMenu     :""),
                'tNombre'       => ($sqllogCatMenu[0]->tNombre        ? $sqllogCatMenu[0]->tNombre      :""),
                'Iconos'       => ($sqllogCatMenu[0]->Iconos        ? $sqllogCatMenu[0]->Iconos      :""),
                'ecodCreacion' => ($sqllogCatMenu[0]->ecodCreacion  ? $sqllogCatMenu[0]->ecodCreacion      :""),
                'fhCreacion' => ($sqllogCatMenu[0]->fhCreacion  ? $sqllogCatMenu[0]->fhCreacion      :""),
                'ecodEdicion' => ($sqllogCatMenu[0]->ecodEdicion  ? $sqllogCatMenu[0]->ecodEdicion      :""),
                'fhEdicion' => ($sqllogCatMenu[0]->fhEdicion  ? $sqllogCatMenu[0]->fhEdicion      :""),
                'ecodEstatus' => ($sqllogCatMenu[0]->ecodEstatus  ? $sqllogCatMenu[0]->ecodEstatus      :""),
            );
            $logecodMenu = (isset($resultadosconsultalogs['ecodMenu'])&&$resultadosconsultalogs['ecodMenu']!="" ? "'".(trim($resultadosconsultalogs['ecodMenu']))."'":   "NULL");
            $logtNombre = (isset($resultadosconsultalogs['tNombre'])&&$resultadosconsultalogs['tNombre']!="" ? "'".(trim($resultadosconsultalogs['tNombre']))."'":   "NULL");
            $logIicons = (isset($resultadosconsultalogs['Iconos'])&&$resultadosconsultalogs['Iconos']!="" ? "'".(trim($resultadosconsultalogs['Iconos']))."'":   "NULL");
            $logIecodCreacion = (isset($resultadosconsultalogs['ecodCreacion'])&&$resultadosconsultalogs['ecodCreacion']!="" ? "'".(trim($resultadosconsultalogs['ecodCreacion']))."'":   "NULL");
            $logIfhCreacion= (isset($resultadosconsultalogs['fhCreacion'])&&$resultadosconsultalogs['fhCreacion']!="" ? "'".(trim($resultadosconsultalogs['fhCreacion']))."'":   "NULL");
            $logIecodEdicion= (isset($resultadosconsultalogs['ecodEdicion'])&&$resultadosconsultalogs['ecodEdicion']!="" ? "'".(trim($resultadosconsultalogs['ecodEdicion']))."'":   "NULL");
            $logIfhEdicion= (isset($resultadosconsultalogs['fhEdicion'])&&$resultadosconsultalogs['fhEdicion']!="" ? "'".(trim($resultadosconsultalogs['fhEdicion']))."'":   "NULL");
            $logIecodEstatus= (isset($resultadosconsultalogs['ecodEstatus'])&&$resultadosconsultalogs['ecodEstatus']!="" ? "'".(trim($resultadosconsultalogs['ecodEstatus']))."'":   "NULL");

            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "'".(trim($loguuid))."'":   "NULL");
            $insertarLogMenu=" CALL `stpInsertarLogMenu`(".$loguuid2.",".$logecodMenu.",".$logtNombre.",".$logIicons.",".$logIecodCreacion.",".$logIfhCreacion.",".$logIecodEdicion.",".$logIfhEdicion.",".$logIecodEstatus.")";
            $responseinsertarLogMenu = DB::select($insertarLogMenu);

            $EcodEstatus = (isset($result['Menu']['EcodEstatus'])&&$result['Menu']['EcodEstatus']!="" ? "'".(trim($result['Menu']['EcodEstatus']))."'":   "NULL");
            $inserMenu=" CALL `stpInsertarCatMenu`(".$ecodMenu.",".$tNombre.",".$Iicons.",".$EcodEstatus.",".$loginEcodUsuarios.")";
            $responseMenu = DB::select($inserMenu); 
        }

        return response()->json([$responseMenu[0]]);
    }
    public function getDetalles(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json = (isset($jsonX)&&$jsonX!="" ? "'".(trim($jsonX))."'":   "NULL");
        $selectMenu="SELECT concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cm.ecodMenu, cm.tNombre, cm.Iconos, cm.ecodCreacion, cm.fhCreacion,cm.ecodEdicion,cm.fhEdicion,cm.ecodEstatus, ce.tNombre as Estatus FROM catmenu cm 
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cm.ecodEstatus 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cm.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cm.ecodEdicion WHERE cm.ecodMenu =".$json;
        $sqlMenu = DB::select(DB::raw($selectMenu));
        return response()->json([ 'sqlMenu'=>(isset($sqlMenu[0]) ? $sqlMenu[0] : "") ]);
    }

}
