<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;

class tipocontenedor extends Controller
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
        $selectEstustus="SELECT ctc.ecodTipoContenedor,ctc.tNombre,ctc.tNombreIng, ce.tNombre AS estatus FROM cattipocontenedor ctc 
        LEFT JOIN catestatus ce ON ce.ecodEstatus=ctc.ecodEstatus
        WHERE 1=1 ".  
        (isset($ecodTipoContenedor)       ? " AND ctc.ecodTipoContenedor LIKE ('%".$ecodTipoContenedor."%')"        : '').
        (isset($tNombre)        ? " AND ctc.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($tNombreIng)        ? " AND ctc.tNombreIng LIKE ('%".$tNombreIng."%')"        : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json( $returResponse);  
    }
    
}
