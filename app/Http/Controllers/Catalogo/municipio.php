<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class municipio extends Controller
{
    public function getComprementos(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $objetArray->objeto_a_array($value);
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
        $selectEstustus="SELECT cm.ecodMunicipio, cm.tNombre FROM catmunicipios cm WHERE 1=1 ".  
        (isset($tNombre)   ? " AND cm.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
