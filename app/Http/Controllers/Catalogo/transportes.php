<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;

class transportes extends Controller
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
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos            = isset($jsonX->metodos) ? $jsonX->metodos : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
            $result; 
        }

        foreach ($result as $key => $value) {
            if(array_key_exists($key, $result) ){
				if ($value != ''){
					${$key} =$value ;
				}
			}
        }
        $selectEstustus="SELECT cu.ecodUsuario, concat_ws('',cu.tNombre,' ', cu.tApellido) as tNombre ,cu.tApellido,cu.tRFC,cu.tCRUP, ce.tNombre AS estatus FROM catusuarios AS cu 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =cu.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodUsuario)    ? " AND cu.ecodUsuario  LIKE ('%".$ecodUsuario ."%')"   : '').
        (isset($tNombre)        ? " AND  concat_ws('',cu.tNombre,' ', cu.tApellido) LIKE ('%".$tNombre."%')"             : '').
        (isset($tRFC)           ? " AND cu.tRFC LIKE ('%".$tRFC."%')"                   : '').
        (isset($tCRUP)          ? " AND cu.tCRUP LIKE ('%".$tCRUP."%')"                 : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"             : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
      
        return response()->json( $returResponse);  
    }

    public function getComprementos(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
            }
            $result; 
        }
        foreach ($result as $key => $value) {
            if(array_key_exists($key, $result) ){
				if ($value != ''){
					${$key} =$value ;
				}
			}
        }
        $selectEstustus="SELECT ct.ecotTipoTransporte,ct.tNombre FROM cattipotransporte ct
        LEFT JOIN catestatus ce ON ce.ecodEstatus = ct.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND ct.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
