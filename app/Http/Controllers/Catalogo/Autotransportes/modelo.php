<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class modelo extends Controller
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
        $selec="SELECT cm.ecodModelo, cm.tNombre, ce.tNombre as estatus FROM catmodelo cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus=cm.ecodEstatus". 
        " WHERE 1=1 ".  
        (isset($ecodModelo)    ? " AND cm.ecodModelo  LIKE ('%".$ecodModelo ."%')"   : '').
        (isset($tNombre)          ? " AND cm.tNombre LIKE ('%".$tNombre."%')"                 : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"             : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selec);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);  
    }

    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":   Null);
        $select="SELECT cm.ecodModelo, cm.tNombre, concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,cm.ecodEdicion, cm.ecodEstatus, ce.tNombre as estatus, cm.fhCreacion,cm.fhEdicion FROM catmodelo cm 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cm.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cm.ecodEdicion
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cm.ecodEstatus
        WHERE cm.ecodModelo = ?";
        $sql = DB::select($select,[$json]);   
        $data = [
            'sqlModelo'=>(isset($sql[0]) ? $sql[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function logsBanco($data) {
            $selectlog="SELECT * FROM catmodelo cm WHERE cm.ecodModelo = ?";
            $sqllog = DB::select($selectlog,[$data]);
            $logtNombre = (isset($sqllog[0]->tNombre) && $sqllog[0]->tNombre != "" ? "" . (trim($sqllog[0]->tNombre)) . "" : "");             
            $logecodCreacion = (isset($sqllog[0]->ecodCreacion) && $sqllog[0]->ecodCreacion != "" ? "" . (trim($sqllog[0]->ecodCreacion)) . "" : "");             
            $logfhCreacion = (isset($sqllog[0]->fhCreacion) && $sqllog[0]->fhCreacion != "" ? "" . (trim($sqllog[0]->fhCreacion)) . "" : "");             
            $logfhEdicion = (isset($sqllog[0]->fhEdicion) && $sqllog[0]->fhEdicion != "" ? "" . (trim($sqllog[0]->fhEdicion)) . "" : Null);             
            $logecodEdicion = (isset($sqllog[0]->ecodEdicion) && $sqllog[0]->ecodEdicion != "" ? "" . (trim($sqllog[0]->ecodEdicion)) . "" : Null);             
            $logecodEstatus = (isset($sqllog[0]->ecodEstatus) && $sqllog[0]->ecodEstatus != "" ? "" . (trim($sqllog[0]->ecodEstatus)) . "" : "");             
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            
            $insertarLog=" CALL `stpInsertarLogModelo`(?, ?, ?, ?, ?, ?, ?, ?)";
            $responseinsertarLog = DB::select($insertarLog,[$loguuid2,$data,$logtNombre,$logecodCreacion,$logfhCreacion,$logecodEdicion,$logfhEdicion,$logecodEstatus]);
            return($sqllog);

    }
    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Modelo) ? $jsonX->Modelo : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        
        $tNombre        = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":  Null);
        $ecodModelo     = (isset($json->ecodModelo)&&$json->ecodModelo!=""  ? "".(trim($json->ecodModelo))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : Null);  
        if ($ecodModelo == Null) {
            DB::beginTransaction();
            try {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."": Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatModelo`(?, ?, ?, ?)";
                $response = DB::select($inser,[$uuid2,$tNombre,$ecodEstatus,$ecodUsuario]);
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
                $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""        ? "".(trim($json->ecodEstatus))."":  Null);
                $this->logsBanco($ecodModelo);
                $inser=" CALL `stpInsertarCatModelo`(?, ?, ?, ?)";
                $response = DB::select($inser,[$ecodModelo,$tNombre,$ecodEstatus,$ecodUsuario]);
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


   /* public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "'" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "'" : "");  
        $ecod = (isset($jsonX->ecod)&&$jsonX->ecod!="" ? "'".(trim($jsonX->ecod))."'":   "NULL");
        $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "'".(trim($json->mEliminacion))."'":   "NULL");
       
        $selectlog="SELECT * FROM catmodelo cm WHERE cm.ecodModelo =".$ecod;
        $sqllog = DB::select($selectlog);
        $logtNombre = (isset($sqllog[0]->tNombre) && $sqllog[0]->tNombre != "" ? "'" . (trim($sqllog[0]->tNombre)) . "'" : "");             
        $logecodMarca = (isset($sqllog[0]->ecodMarca) && $sqllog[0]->ecodMarca != "" ? "'" . (trim($sqllog[0]->ecodMarca)) . "'" : "");             
        $logecodCreacion = (isset($sqllog[0]->ecodCreacion) && $sqllog[0]->ecodCreacion != "" ? "'" . (trim($sqllog[0]->ecodCreacion)) . "'" : "");             
        $logfhCreacion = (isset($sqllog[0]->fhCreacion) && $sqllog[0]->fhCreacion != "" ? "'" . (trim($sqllog[0]->fhCreacion)) . "'" : "");             
        $logfhEdicion = (isset($sqllog[0]->fhEdicion) && $sqllog[0]->fhEdicion != "" ? "'" . (trim($sqllog[0]->fhEdicion)) . "'" : "NULL");             
        $logecodEdicion = (isset($sqllog[0]->ecodEdicion) && $sqllog[0]->ecodEdicion != "" ? "'" . (trim($sqllog[0]->ecodEdicion)) . "'" : "NULL");             
        $logecodEstatus = (isset($sqllog[0]->ecodEstatus) && $sqllog[0]->ecodEstatus != "" ? "'" . (trim($sqllog[0]->ecodEstatus)) . "'" : "");             
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "'".(trim($loguuid))."'":   "NULL");
        $inserLog=" CALL `stpInsertarLogCatModeloEliminar`(".$loguuid2.",".$ecod.",".$logtNombre.",".$logecodMarca.",".$logecodEstatus.",".$logecodCreacion.",".$logfhCreacion.",".$mEliminacion.",".$InsertecodUsuario.")";
        $responseLog = DB::select($inserLog);  
        $jsonData = json_encode($responseLog[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,202);
    }*/

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
        $selectEstustus="SELECT cm.ecodModelo, cm.tNombre FROM catmodelo cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cm.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 5' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}

