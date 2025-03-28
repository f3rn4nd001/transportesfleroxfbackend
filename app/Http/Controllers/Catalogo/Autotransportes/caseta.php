<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class caseta extends Controller
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
        $selec="SELECT cc.ecodCaseta,cc.tNombre, cc.tUbicacion, cmo.tNombre AS origen, cmd.tNombre AS destino, ce.tNombre AS estatus,  cc.ecodEstatus FROM catcaseta cc
        LEFT JOIN catmunicipios cmo ON cmo.ecodMunicipio = cc.ecodOrigen
        LEFT JOIN catmunicipios cmd ON cmd.ecodMunicipio = cc.ecodDestino
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cc.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodCaseta)    ? " AND cc.ecodCaseta  LIKE ('%".$ecodCaseta ."%')"   : '').
        (isset($tNombre)          ? " AND cc.tNombre LIKE ('%".$tNombre."%')"                 : '').
        (isset($tUbicacion)          ? " AND cc.tUbicacion LIKE ('%".$tUbicacion."%')"                 : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"             : '').
        (isset($destino['ecodMunicipio'])  ? " AND cc.ecodDestino LIKE ('%".$destino['ecodMunicipio']."%')"        : '').
        (isset($origen['ecodMunicipio'])  ? " AND cc.ecodOrigen LIKE ('%".$origen['ecodMunicipio']."%')"        : '').
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
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."": Null);
        $selectCaseta="SELECT cc.ecodCaseta,cc.tNombre,cc.ecodOrigen,cc.ecodDestino, cc.tUbicacion, cmo.tNombre AS origen, cmd.tNombre AS destino, ce.tNombre AS estatus, concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,cc.fhEdicion, cc.ecodEstatus,cc.ecodEdicion,cc.fhCreacion FROM catcaseta cc
        LEFT JOIN catmunicipios cmo ON cmo.ecodMunicipio = cc.ecodOrigen
        LEFT JOIN catmunicipios cmd ON cmd.ecodMunicipio = cc.ecodDestino
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cc.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cc.ecodEdicion
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cc.ecodEstatus
        WHERE cc.ecodCaseta = ?";
        $sqlCaseta = DB::select($selectCaseta,[$json]);     
        $selecCostos="SELECT bc.ecodBitCaseta,bc.nEjes,bc.nCosto FROM bitcaseta bc
        WHERE bc.ecodCaseta = ? ORDER BY bc.nEjes ASC";
        $sqlCostos = DB::select($selecCostos,[$json]); 
        $data = [
            'sqlCaseta'=>(isset($sqlCaseta[0]) ? $sqlCaseta[0] : ""),
            'sqlCostos'=>(isset($sqlCostos) ? $sqlCostos : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Caseta) ? $jsonX->Caseta : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $jsonarrCostos  = isset($json->arrCostos) ? $json->arrCostos : [];
        $jsonarrCostosEliminar  = isset($json->ecodEliminacion) ? $json->ecodEliminacion : [];
        $tNombre    = (isset($json->tNombre)&&$json->tNombre!=""        ? "'".(trim($json->tNombre))."'":   "NULL");
        $ecodCaseta    = (isset($json->ecodCaseta)&&$json->ecodCaseta!=""        ? "'".(trim($json->ecodCaseta))."'":   "NULL");
        $ecodOrigen    = (isset($json->ecodOrigen)&&$json->ecodOrigen!=""        ? "'".(trim($json->ecodOrigen))."'":   "NULL");
        $ecodDestino    = (isset($json->ecodDestino)&&$json->ecodDestino!=""        ? "'".(trim($json->ecodDestino))."'":   "NULL");
        $tUbicacion    = (isset($json->tUbicacion)&&$json->tUbicacion!=""        ? "'".(trim($json->tUbicacion))."'":   "NULL");
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "'" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "'" : "");  
        if (count($jsonarrCostosEliminar) > 0) {
            foreach ($jsonarrCostosEliminar as $key => $value) {
                $ecodEliminar  = (isset($value)&&$value!=""            ? "'".(trim($value))."'":   "NULL");  
                DB::select("DELETE FROM bitcaseta WHERE ecodBitCaseta =".$ecodEliminar); 
            }
        }
        if ($ecodCaseta == 'NULL') {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "'".(trim($uui))."'":   "NULL");
            $ecodEstatus = "'2660376e-dbf8-44c1-b69f-b2554e3e5d4c'";
            $inserCaseta=" CALL `stpInsertarCatCasetas`(".$uuid2.",".$tNombre.",".$ecodOrigen.",".$ecodDestino.",".$tUbicacion.",".$ecodEstatus.",".$InsertecodUsuario.")";
            $responseCaseta = DB::select($inserCaseta); 
            if (count($jsonarrCostos) > 0) {
                foreach ($jsonarrCostos as $key => $value) {
                    $uuiecodCostos = Uuid::uuid4();   
                    $uuid2uuiecodCostos = (isset($uuiecodCostos)&&$uuiecodCostos!="" ? "'".(trim($uuiecodCostos))."'":   "NULL");
                    $nCosto  = (isset($value->nCosto)&&$value->nCosto!=""            ? "".(trim($value->nCosto))."":   "NULL");
                    $ecodBitCaseta  = (isset($value->ecodBitCaseta)&&$value->ecodBitCaseta!=""            ? "'".(trim($value->ecodBitCaseta))."'":   "NULL");  
                    $nEjes  = (isset($value->nEjes)&&$value->nEjes!=""            ? "'".(trim($value->nEjes))."'":   "NULL");  
                    if ($ecodBitCaseta == 'NULL') {
                        $uuiecodBitCaseta = Uuid::uuid4();   
                        $uuid2uuiecodBitCaseta = (isset($uuiecodBitCaseta)&&$uuiecodBitCaseta!="" ? "'".(trim($uuiecodBitCaseta))."'":   "NULL");
                        $inserBitCaseta=" CALL `stpInsertarBitCatCasetas`(".$uuid2uuiecodBitCaseta.",".$uuid2.",".$nEjes.",".$nCosto.")";
                        $responseBitCaseta = DB::select($inserBitCaseta);
                    }
                    else{
                        $inserBitCaseta=" CALL `stpInsertarBitCatCasetas`(".$ecodBitCaseta.",".$uuid2.",".$nEjes.",".$nCosto.")";
                        $responseBitCaseta = DB::select($inserBitCaseta);
                    }
                }
            }
        }
        else {
            $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""        ? "'".(trim($json->ecodEstatus))."'":   "NULL");
            $inserCaseta=" CALL `stpInsertarCatCasetas`(".$ecodCaseta.",".$tNombre.",".$ecodOrigen.",".$ecodDestino.",".$tUbicacion.",".$ecodEstatus.",".$InsertecodUsuario.")";
            $responseCaseta = DB::select($inserCaseta); 
            if (count($jsonarrCostos) > 0) {
                foreach ($jsonarrCostos as $key => $value) {
                    $uuiecodCostos = Uuid::uuid4();   
                    $uuid2uuiecodCostos = (isset($uuiecodCostos)&&$uuiecodCostos!="" ? "'".(trim($uuiecodCostos))."'":   "NULL");
                    $nCosto  = (isset($value->nCosto)&&$value->nCosto!=""            ? "".(trim($value->nCosto))."":   "NULL");
                    $ecodBitCaseta  = (isset($value->ecodBitCaseta)&&$value->ecodBitCaseta!=""            ? "'".(trim($value->ecodBitCaseta))."'":   "NULL");  
                    $nEjes  = (isset($value->nEjes)&&$value->nEjes!=""            ? "'".(trim($value->nEjes))."'":   "NULL");  
                    if ($ecodBitCaseta == 'NULL') {
                        $uuiecodBitCaseta = Uuid::uuid4();   
                        $uuid2uuiecodBitCaseta = (isset($uuiecodBitCaseta)&&$uuiecodBitCaseta!="" ? "'".(trim($uuiecodBitCaseta))."'":   "NULL");
                        $inserBitCaseta=" CALL `stpInsertarBitCatCasetas`(".$uuid2uuiecodBitCaseta.",".$ecodCaseta.",".$nEjes.",".$nCosto.")";
                        $responseBitCaseta = DB::select($inserBitCaseta);
                    }
                    else{
                        $inserBitCaseta=" CALL `stpInsertarBitCatCasetas`(".$ecodBitCaseta.",".$ecodCaseta.",".$nEjes.",".$nCosto.")";
                        $responseBitCaseta = DB::select($inserBitCaseta);
                    }
                }
            }
        }
        $jsonData = json_encode($responseCaseta);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);  
    }

    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $ecod = (isset($jsonX->ecod)&&$jsonX->ecod!="" ? "'".(trim($jsonX->ecod))."'":   "NULL");
        $inserLog=" CALL `stpInsertarLogCatCasetaEliminar`(".$ecod.")";
        $responseLog = DB::select($inserLog);
        $jsonData = json_encode($responseLog[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,202);
   
    }  
        
}
