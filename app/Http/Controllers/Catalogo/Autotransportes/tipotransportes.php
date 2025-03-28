<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\seg\objetArray;

class tipotransportes extends Controller
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
    
        $select="SELECT ctt.ecotTipoTransporte, ctt.tNombre ,ctt.ecodEstatus,ce.tNombre AS estatus FROM cattipotransporte ctt
        LEFT JOIN catestatus ce ON ce.ecodEstatus = ctt.ecodEstatus WHERE 1=1 ". 
        (isset($ecotTipoTransporte)       ? " AND ctt.ecotTipoTransporte LIKE ('%".$ecotTipoTransporte."%')"        : '').
        (isset($tNombre)        ? " AND ctt.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);  
    }

    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."": Null);
        $select="SELECT ctt.ecotTipoTransporte, ctt.tNombre ,ctt.ecodEstatus,ce.tNombre AS Estatus, ctt.fhCreacion,ctt.fhEdicion,
        concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor
        FROM cattipotransporte ctt
        LEFT JOIN catestatus ce ON ce.ecodEstatus = ctt.ecodEstatus 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = ctt.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = ctt.ecodEdicion
        WHERE ctt.ecotTipoTransporte = ?";
        $sql = DB::select($select,[$json]);   
        $data = [
            'sqlTipoEmpresa'=>(isset($sql[0]) ? $sql[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    } 

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
