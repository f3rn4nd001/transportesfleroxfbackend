<?php

namespace App\Http\Controllers\Catalogo\Finanzas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\seg\objetArray;
use App\Http\Controllers\seg\encriptar;
use Ramsey\Uuid\Uuid;

class moneda extends Controller
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
        $select=" SELECT cm.ecodMoneda, cm.tNombre, cm.tNombreCorto, cm.nValorMexicoA,cm.nValorAMexico, ce.tNombre AS Estatus FROM catmoneda cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cm.ecodEstatus
        WHERE 1=1 ".  
        (isset($ecodMoneda)      ? " AND cm.ecodMoneda LIKE ('%".$ecodMoneda."%')"        : '').
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($tNombreCorto)   ? " AND cm.tNombreCorto LIKE ('%".$tNombreCorto."%')"        : '').
        (isset($nValorMexicoA)   ? " AND cm.nValorMexicoA LIKE ('%".$nValorMexicoA."%')"        : '').
        (isset($nValorAMexico)   ? " AND cm.nValorAMexico LIKE ('%".$nValorAMexico."%')"        : '').
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
        
        $select="SELECT cm.ecodMoneda, cm.tNombre, cm.tNombreCorto, cm.ecodEstatus, cm.nValorMexicoA, cm.nValorAMexico, cm.fhCreacion, cm.fhEdicion, concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,
        concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, ce.tNombre AS Estatus FROM catmoneda cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cm.ecodEstatus
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cm.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cm.ecodEdicion	
        WHERE cm.ecodMoneda = ?";
        $sql = DB::select($select,[$json]);   
        $data = [
            'sqlMoneda'=>(isset($sql[0]) ? $sql[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Moneda) ? $jsonX->Moneda : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        
        $tNombre = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $tNombreCorto = (isset($json->tNombreCorto)&&$json->tNombreCorto!=""        ? "".(trim($json->tNombreCorto))."":   Null);
        $ecodMoneda = (isset($json->ecodMoneda)&&$json->ecodMoneda!=""        ? "".(trim($json->ecodMoneda))."":   Null);
        $nValorMexicoA = (isset($json->nValorMexicoA)&&$json->nValorMexicoA!=""        ? "".(trim($json->nValorMexicoA))."":   Null);
        $nValorAMexico = (isset($json->nValorAMexico)&&$json->nValorAMexico!=""        ? "".(trim($json->nValorAMexico))."":   Null);
        
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");
       
        if ($ecodMoneda == Null) {
            DB::beginTransaction();
            try {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":  Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatMoneda`(?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$uuid2, $tNombre, $tNombreCorto, $nValorMexicoA, $nValorAMexico, $ecodEstatus, $InsertecodUsuario]);
                
                $jsonData = json_encode($response[0]);
                $returResponse2 =$encriptar->shiftText($jsonData, 23);
                if(isset($response[0]->mensaje)) {
                    $responEstatus=202;
                }
                if(isset($response[0]->Codigo)) {
                    $responEstatus=200;
                }  
                if (isset($response[0]->Codigo) || isset($response[0]->mensaje)) {
                    DB::commit();
                } else {
                    DB::rollback();
                }  
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            } 
        }
        else{
            DB::beginTransaction();
            try {
              //  $this->logsMoneda($ecodMoneda);
                $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""  ? "".(trim($json->ecodEstatus))."":  Null);
                $inser=" CALL `stpInsertarCatMoneda`(?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$ecodMoneda, $tNombre, $tNombreCorto, $nValorMexicoA, $nValorAMexico, $ecodEstatus, $InsertecodUsuario]);
                if(isset($response[0]->Codigo)) {
                    $jsonData = json_encode($response[0]);
                    $returResponse2 =$encriptar->shiftText($jsonData, 23);
                    $responEstatus=200;
                } 
                if (isset($response[0]->Codigo) || isset($response[0]->mensaje)) {
                    DB::commit();
                } else {
                    DB::rollback();
                }  
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            } 
        }
        return response()->json($returResponse2,$responEstatus);  
    }

    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        DB::beginTransaction();
        try {
            $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "".(trim($json->mEliminacion))."":   Null);
            $ecodBanco = (isset($jsonX->ecod)&&$jsonX->ecod!=""        ? "".(trim($jsonX->ecod))."":   Null);
            $responseEliminar = "  CALL `stpEliminarMoneda`(?)";
            $response = DB::select($responseEliminar, [$ecodBanco]);         
            if ($response[0]->mensaje) {
                DB::commit();
            } else {
                DB::rollback();
            }  
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        } 
        $jsonData = json_encode($response[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,202);
    }

    public function getcompremento(Request $request) {
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX = json_decode($encriptar->shiftText($request['datos'], -23));
        $json  = isset($jsonX->filtros) ? $jsonX->filtros : [];
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
        $select="SELECT cm.ecodMoneda, cm.tNombre, cm.tNombreCorto, cm.ecodEstatus FROM catmoneda cm
		LEFT JOIN catestatus ce ON ce.ecodEstatus = cm.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo'".  
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        (isset($tNombre)        ? " OR cm.tNombreCorto LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 6' ;
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
