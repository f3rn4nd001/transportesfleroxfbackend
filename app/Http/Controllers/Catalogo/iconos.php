<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class iconos extends Controller
{
   
 
    public function getRegistro(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos            = isset($jsonX->metodos) ? $jsonX->metodos : [];
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
        
        $selectEstustus="SELECT ci.tIcono, ci.ecodIcono , ci.tNombre FROM caticono AS ci ". " WHERE 1=1 ".
        (isset($ecodIcono )       ? " AND ci.ecodIcono  LIKE ('%".$ecodIcono ."%')"        : '').
        (isset($tNombre)        ? " AND ci.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json( $returResponse);  
    }

    public function getComprementos(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos            = isset($jsonX->metodos) ? $jsonX->metodos : [];
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
       
        $select="SELECT * FROM caticono WHERE 1=1 ".  
        (isset($ecodIcono)       ? " AND ecodIcono LIKE ('%".$ecodIcono."%')"        : '').
        (isset($tNombre)        ? " AND tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json( $returResponse); 
    }
}
