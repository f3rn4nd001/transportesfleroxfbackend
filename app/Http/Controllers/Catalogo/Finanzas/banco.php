<?php

namespace App\Http\Controllers\Catalogo\Finanzas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\seg\objetArray;
use Ramsey\Uuid\Uuid;
use App\Models\catBanco;

class banco extends Controller
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
        $select=" SELECT cb.ecodBanco, cb.tNombre, cb.tNombreCorto, cb.ecodEstatus,ce.tNombre AS Estatus FROM catbanco cb
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cb.ecodEstatus
        WHERE 1=1 ".  
        (isset($ecodBanco)      ? " AND cb.ecodBanco LIKE ('%".$ecodBanco."%')"        : '').
        (isset($tNombre)        ? " AND cb.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($tNombreCorto)   ? " AND cb.tNombreCorto LIKE ('%".$tNombreCorto."%')"        : '').
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
        
        $select="SELECT cb.ecodBanco, cb.tNombre, cb.tNombreCorto, cb.ecodEstatus, cb.ecodCreacion, cb.ecodEdicion, cb.fhCreacion, cb.fhEdicion,ce.tNombre AS Estatus,concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,
        concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor FROM catbanco cb
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cb.ecodEstatus
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cb.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cb.ecodEdicion
        WHERE cb.ecodBanco = ?";
        $sql = DB::select($select,[$json]);   
        $data = [
            'sqlBanco'=>(isset($sql[0]) ? $sql[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function logsBanco($data) {
        $sqllog=catBanco::where("ecodBanco",$data)->first();
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
        $logecodBanco  = (isset($sqllog->ecodBanco) && $sqllog->ecodBanco != ""          ? "" . (trim($sqllog->ecodBanco)) . "" : Null);             
        $logtNombre  = (isset($sqllog->tNombre) && $sqllog->tNombre != ""          ? "" . (trim($sqllog->tNombre)) . "" : Null);             
        $logtNombreCorto  = (isset($sqllog->tNombreCorto) && $sqllog->tNombreCorto != ""          ? "" . (trim($sqllog->tNombreCorto)) . "" : Null);             
        $logecodEstatus  = (isset($sqllog->ecodEstatus) && $sqllog->ecodEstatus != ""          ? "" . (trim($sqllog->ecodEstatus)) . "" : Null);             
        $logfhCreacion  = (isset($sqllog->fhCreacion) && $sqllog->fhCreacion != ""          ? "" . (trim($sqllog->fhCreacion)) . "" : Null);             
        $logecodCreacion  = (isset($sqllog->ecodCreacion) && $sqllog->ecodCreacion != ""          ? "" . (trim($sqllog->ecodCreacion)) . "" : Null);             
        $logecodEdicion  = (isset($sqllog->ecodEdicion) && $sqllog->ecodEdicion != ""          ? "" . (trim($sqllog->ecodEdicion)) . "" : Null);             
        $logfhEdicion  = (isset($sqllog->fhEdicion) && $sqllog->fhEdicion != ""          ? "" . (trim($sqllog->fhEdicion)) . "" : Null);             
        $responseinsertarLog = "  CALL `stpInsertarLogBanco`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $responseLog =  DB::select($responseinsertarLog, [$loguuid2, $logecodBanco, $logtNombre, $logtNombreCorto,  $logecodEstatus, $logecodCreacion, $logfhCreacion, $logecodEdicion, $logfhEdicion]); 
        return($sqllog);
   }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Bancos) ? $jsonX->Bancos : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $tNombre = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $tNombreCorto = (isset($json->tNombreCorto)&&$json->tNombreCorto!=""        ? "".(trim($json->tNombreCorto))."":   Null);
        $ecodBanco = (isset($json->ecodBanco)&&$json->ecodBanco!=""        ? "".(trim($json->ecodBanco))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");
        if ($ecodBanco == Null) {
            DB::beginTransaction();
            try {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":  Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatBanco`(?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$uuid2, $tNombre, $tNombreCorto, $ecodEstatus, $InsertecodUsuario ]);
                
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
                $this->logsBanco($ecodBanco);
                $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""  ? "".(trim($json->ecodEstatus))."":  Null);
                $inser=" CALL `stpInsertarCatBanco`(?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$ecodBanco, $tNombre, $tNombreCorto, $ecodEstatus, $InsertecodUsuario ]);
                $jsonData = json_encode($response[0]);
                $returResponse2 =$encriptar->shiftText($jsonData, 23);
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
            $sqllog = $this->logsBanco($ecodBanco);
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
            $sqlEcodUsuario = DB::select($selectEcodUsuario);
            $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");       
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);            
            $logecodBanco  = (isset($sqllog->ecodBanco) && $sqllog->ecodBanco != ""          ? "" . (trim($sqllog->ecodBanco)) . "" : Null);             
            $logtNombre  = (isset($sqllog->tNombre) && $sqllog->tNombre != ""          ? "" . (trim($sqllog->tNombre)) . "" : Null);             
            $logtNombreCorto  = (isset($sqllog->tNombreCorto) && $sqllog->tNombreCorto != ""          ? "" . (trim($sqllog->tNombreCorto)) . "" : Null);             
            $logfhCreacion  = (isset($sqllog->fhCreacion) && $sqllog->fhCreacion != ""          ? "" . (trim($sqllog->fhCreacion)) . "" : Null);             
            $logecodCreacion  = (isset($sqllog->ecodCreacion) && $sqllog->ecodCreacion != ""          ? "" . (trim($sqllog->ecodCreacion)) . "" : Null);             
            $logecodEdicion  = (isset($sqllog->ecodEdicion) && $sqllog->ecodEdicion != ""          ? "" . (trim($sqllog->ecodEdicion)) . "" : Null);             
            $logfhEdicion  = (isset($sqllog->fhEdicion) && $sqllog->fhEdicion != ""          ? "" . (trim($sqllog->fhEdicion)) . "" : Null);             
            $logecodEstatus = "fa6cc9a2-f221-4e27-b575-1fac2698d27a";
            $responseEliminar = "  CALL `stpEliminarBanco`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $response = DB::select($responseEliminar, [$loguuid2, $logecodBanco, $logtNombre, $logtNombreCorto, $logecodEstatus, $logecodCreacion, $logfhCreacion, $InsertecodUsuario, $mEliminacion]);         
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
        $select="SELECT cb.ecodBanco, cb.tNombre, cb.tNombreCorto FROM catbanco cb
		LEFT JOIN catestatus ce ON ce.ecodEstatus = cb.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo'".  
        (isset($tNombre)        ? " AND cb.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        (isset($tNombre)        ? " OR cb.tNombreCorto LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 6' ;
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
