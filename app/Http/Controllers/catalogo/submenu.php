<?php

namespace App\Http\Controllers\catalogo;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class submenu extends Controller
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
        
        $selectMenu="SELECT * FROM catsubmenu". " WHERE 1=1 ".  
        (isset($ecodSubmenu)    ? " AND ecodSubmenu LIKE ('%".$ecodSubmenu."%')"    : '').
        (isset($tNombre)        ? " AND tNombre LIKE ('%".$tNombre."%')"            : '').
        (isset($tUrl)           ? " AND tUrl LIKE ('%".$tUrl."%')"            : '').
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

        $ecodSubmenu = (isset($result['SubMenu']['ecodSubmenu'])&&$result['SubMenu']['ecodSubmenu']!="" ? "'".(trim($result['SubMenu']['ecodSubmenu']))."'":   "NULL");
        $tUrl = (isset($result['SubMenu']['tUrl'])&&$result['SubMenu']['tUrl']!="" ? "'".(trim($result['SubMenu']['tUrl']))."'":   "NULL");
        $tNombre = (isset($result['SubMenu']['tNombre'])&&$result['SubMenu']['tNombre']!="" ? "'".(trim($result['SubMenu']['tNombre']))."'":   "NULL");
        $ecodMenu = (isset($result['ecodMenu'])&&$result['ecodMenu']!="" ? "'".(trim($result['ecodMenu']))."'":   "NULL");
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$request['headers']['ecodCorreo'];
        $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario)); 
        foreach ($sqlEcodUsuario as $key => $v){
            $resultadosecodUsuario[]=array(
                'ecodUsuario' => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
            );
        }
        $loginEcodUsuarios = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");
        if ($ecodSubmenu == 'NULL') {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "'".(trim($uui))."'":   "NULL");
            $ecodEstatus = "'ubsvkbvukabvoeho8veowbve'";
            $inserSubMenu=" CALL `stpInsertarCatSubMenu`(".$uuid2.",".$tNombre.",".$tUrl.",".$ecodEstatus.",".$loginEcodUsuarios.")";
            $responseSubMenu = DB::select($inserSubMenu);
           
            $ecodController = "NULL";
            $relMenuSubMenuuui = Uuid::uuid4();
            $relMenuSubMenuuuid2 = (isset($relMenuSubMenuuui)&&$relMenuSubMenuuui!="" ? "'".(trim($relMenuSubMenuuui))."'":   "NULL");
            $inserrelMenuSubMenuController=" CALL `stpInsertarrelMenuSubMenuController`(".$relMenuSubMenuuuid2.",".$uuid2.",".$ecodMenu.",".$ecodController.",".$ecodEstatus.",".$loginEcodUsuarios.")";
            $responserelMenuSubMenuController = DB::select($inserrelMenuSubMenuController);
        }
        return response()->json(['responseSubMenu'=>(isset($responseSubMenu[0]) ? $responseSubMenu[0] : ""),'responserelMenuSubMenuController'=>(isset($responserelMenuSubMenuController[0]) ? $responserelMenuSubMenuController[0] : "")]);
    }

    public function getDetalles(Request $request){
        $jsonX = json_decode( $request['datos'] ,true);
        $json = (isset($jsonX)&&$jsonX!="" ? "'".(trim($jsonX))."'":   "NULL");
        $selectMenu="SELECT ce.tNombre AS Estatus, concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,  concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cs.ecodSubmenu,cs.tNombre,cs.tUrl,cs.ecodCreacion,cs.fhCreacion,cs.ecodEstatus,cs.ecodEdicion,cs.fhEdicion FROM catsubmenu cs
        LEFT JOIN catestatus ce ON ce.EcodEstatus=cs.ecodEstatus
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cs.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario=cs.ecodEdicion WHERE cs.ecodSubmenu =".$json;
        $sqlsubMenu = DB::select(DB::raw($selectMenu));
        $selectrelsubmenucontroller="SELECT rmsc.ecodMenu,rmsc.ecodSubmenu,rmsc.ecodController,rmsc.ecodEstatus,cm.tNombre AS Menu FROM relmenusubmenucontroller rmsc 
        LEFT JOIN catmenu cm ON cm.ecodMenu = rmsc.ecodMenu WHERE rmsc.ecodSubmenu =".$json;
        $sqlrelsubmenucontroller = DB::select(DB::raw($selectrelsubmenucontroller));

        return response()->json([ 'sqlsubMenu'=>(isset($sqlsubMenu[0]) ? $sqlsubMenu[0] : ""), 'sqlrelsubmenucontroller'=>(isset($sqlrelsubmenucontroller[0]) ? $sqlrelsubmenucontroller[0] : "") ]);
    }


}
