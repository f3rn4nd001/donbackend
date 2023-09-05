<?php

namespace App\Http\Controllers\catalogo;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class controladores extends Controller
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
        
        $selectMenu="SELECT cc.*, ce.tNombre AS Estatus FROM cotcontroller cc
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cc.ecodEstatus". " WHERE 1=1 ".  
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

        $tNombre = (isset($result['controladores']['tNombre'])&&$result['controladores']['tNombre']!="" ? "'".(trim($result['controladores']['tNombre']))."'":   "NULL");
        $tUrl = (isset($result['controladores']['tUrl'])&&$result['controladores']['tUrl']!="" ? "'".(trim($result['controladores']['tUrl']))."'":   "NULL");
        $ecodSubmenu = (isset($result['ecodSubmenu'])&&$result['ecodSubmenu']!="" ? "'".(trim($result['ecodSubmenu']))."'":   "NULL");
        $ecodControllers = (isset($result['controladores']['ecodControllers'])&&$result['controladores']['ecodControllers']!="" ? "'".(trim($result['controladores']['ecodControllers']))."'":   "NULL");
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$request['headers']['ecodCorreo'];
        $sqlEcodUsuario = DB::select(DB::raw($selectEcodUsuario)); 
        foreach ($sqlEcodUsuario as $key => $v){
            $resultadosecodUsuario[]=array(
                'ecodUsuario' => ($v->ecodUsuario   ? $v->ecodUsuario    : ""),
            );
        }
        $loginEcodUsuarios = (isset($resultadosecodUsuario[0]['ecodUsuario']) && $resultadosecodUsuario[0]['ecodUsuario'] != "" ? "'" . (trim($resultadosecodUsuario[0]['ecodUsuario'])) . "'" : "");
        if ($ecodControllers == 'NULL') {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "'".(trim($uui))."'":   "NULL");
            $ecodEstatus = "'ubsvkbvukabvoeho8veowbve'";
            $inserControllers=" CALL `stpInsertarCatControllers`(".$uuid2.",".$tNombre.",".$tUrl.",".$ecodEstatus.",".$loginEcodUsuarios.")";
            $responseControllers = DB::select($inserControllers);    
        }
        return response()->json([$responseControllers[0]]);
    }
}
