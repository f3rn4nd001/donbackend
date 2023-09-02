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
}
