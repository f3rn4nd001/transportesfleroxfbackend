<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;
use App\Models\catMarca;

class marca extends Controller
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
        $selec="SELECT cm.ecodMarca, cm.tNombre, cm.tPaisOrigen, ce.tNombre AS estatus FROM catmarca AS cm 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =cm.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodMarca)    ? " AND cm.ecodMarca  LIKE ('%".$ecodMarca ."%')"   : '').
        (isset($tNombre)          ? " AND cm.tNombre LIKE ('%".$tNombre."%')"                 : '').
        (isset($tPaisOrigen)          ? " AND cm.tPaisOrigen LIKE ('%".$tPaisOrigen."%')"                 : '').
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
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."": Null);
        $select="SELECT cm.ecodMarca, cm.tNombre,concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,cm.ecodEdicion, cm.tPaisOrigen, cm.ecodEstatus, ce.tNombre as estatus, cm.fhCreacion,cm.fhEdicion FROM catmarca cm 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cm.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cm.ecodEdicion
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cm.ecodEstatus
        WHERE cm.ecodMarca = ?";
        $sql = DB::select($select,[$json]); 
        
        $selecRelMarcamodelo="SELECT rmm.ecodModelo, cm.tNombre, ce.tNombre as estatus FROM relmarcamodelo rmm
        LEFT JOIN catmodelo cm ON cm.ecodModelo = rmm.ecodModelo
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cm.ecodEstatus
        WHERE rmm.ecodMarca = ?";
        $sqlrelMarcaModelo = DB::select($selecRelMarcamodelo,[$json]); 

        $data = [
            'sqlMarca'=>(isset($sql[0]) ? $sql[0] : ""),
            'sqlrelMarcaModelo'=>(isset($sqlrelMarcaModelo) ? $sqlrelMarcaModelo : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Marca) ? $jsonX->Marca : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $tNombre        = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $tPaisOrigen   = (isset($json->tPaisOrigen)&&$json->tPaisOrigen!=""        ? "".(trim($json->tPaisOrigen))."":   Null);
        $ecodMarca      = (isset($json->ecodMarca)&&$json->ecodMarca!=""        ? "".(trim($json->ecodMarca))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
        if ($ecodMarca == Null) {
            DB::beginTransaction();
            try {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."": Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatMarca`(?, ?, ?, ?, ?)";
                $response = DB::select($inser,[$uuid2,$tNombre,$tPaisOrigen,$ecodEstatus,$ecodUsuario]); 
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
                $this->logsMarca($ecodMarca);
                $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""        ? "".(trim($json->ecodEstatus))."":   Null);
                $inser=" CALL `stpInsertarCatMarca`(?, ?, ?, ?, ?)";
                $response = DB::select($inser,[$ecodMarca,$tNombre,$tPaisOrigen,$ecodEstatus,$ecodUsuario]); 
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
    public function logsMarca($data) {
        $selectlog="SELECT * FROM catmarca cm WHERE cm.ecodMarca = ?";
        $sqllog = DB::select($selectlog,[$data]);
        $logtNombre = (isset($sqllog[0]->tNombre) && $sqllog[0]->tNombre != "" ? "" . (trim($sqllog[0]->tNombre)) . "" : "");             
        $logtPaisOrigen = (isset($sqllog[0]->tPaisOrigen) && $sqllog[0]->tPaisOrigen != "" ? "" . (trim($sqllog[0]->tPaisOrigen)) . "" : "");             
        $logecodCreacion = (isset($sqllog[0]->ecodCreacion) && $sqllog[0]->ecodCreacion != "" ? "" . (trim($sqllog[0]->ecodCreacion)) . "" : "");             
        $logfhCreacion = (isset($sqllog[0]->fhCreacion) && $sqllog[0]->fhCreacion != "" ? "" . (trim($sqllog[0]->fhCreacion)) . "" : "");             
        $logfhEdicion = (isset($sqllog[0]->fhEdicion) && $sqllog[0]->fhEdicion != "" ? "" . (trim($sqllog[0]->fhEdicion)) . "" : Null);             
        $logecodEdicion = (isset($sqllog[0]->ecodEdicion) && $sqllog[0]->ecodEdicion != "" ? "" . (trim($sqllog[0]->ecodEdicion)) . "" : Null);             
        $logecodEstatus = (isset($sqllog[0]->ecodEstatus) && $sqllog[0]->ecodEstatus != "" ? "" . (trim($sqllog[0]->ecodEstatus)) . "" : "");             
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
        
        $insertarLog=" CALL `stpInsertarLogMarca`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $responseinsertarLog = DB::select($insertarLog,[$loguuid2,$data,$logtNombre,$logtPaisOrigen,$logecodCreacion,$logfhCreacion,$logecodEdicion,$logfhEdicion,$logecodEstatus]);
        return($sqllog);
    }
    
    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
        $ecod = (isset($jsonX->ecod)&&$jsonX->ecod!="" ? "".(trim($jsonX->ecod))."":   Null);
        $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "".(trim($json->mEliminacion))."":   Null);
        $this->logsMarca($ecod);

        $selectlog="SELECT * FROM catmarca cm WHERE cm.ecodMarca = ?";
        $sqllog = DB::select($selectlog,[$ecod]);
        $logtNombre = (isset($sqllog[0]->tNombre) && $sqllog[0]->tNombre != "" ? "" . (trim($sqllog[0]->tNombre)) . "" : "");             
        $logtPaisOrigen = (isset($sqllog[0]->tPaisOrigen) && $sqllog[0]->tPaisOrigen != "" ? "" . (trim($sqllog[0]->tPaisOrigen)) . "" : "");             
        $logecodCreacion = (isset($sqllog[0]->ecodCreacion) && $sqllog[0]->ecodCreacion != "" ? "" . (trim($sqllog[0]->ecodCreacion)) . "" : "");             
        $logfhCreacion = (isset($sqllog[0]->fhCreacion) && $sqllog[0]->fhCreacion != "" ? "" . (trim($sqllog[0]->fhCreacion)) . "" : "");             
        $logfhEdicion = (isset($sqllog[0]->fhEdicion) && $sqllog[0]->fhEdicion != "" ? "" . (trim($sqllog[0]->fhEdicion)) . "" : Null);             
        $logecodEdicion = (isset($sqllog[0]->ecodEdicion) && $sqllog[0]->ecodEdicion != "" ? "" . (trim($sqllog[0]->ecodEdicion)) . "" : Null);             
        $logecodEstatus = (isset($sqllog[0]->ecodEstatus) && $sqllog[0]->ecodEstatus != "" ? "" . (trim($sqllog[0]->ecodEstatus)) . "" : "");             
        
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
        
        $inserLog=" CALL `stpInsertarLogCatMarcaEliminar`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $responseLog = DB::select($inserLog,[$loguuid2,$ecod,$logtNombre,$logtPaisOrigen,$logecodEstatus,$logecodCreacion,$logfhCreacion,$mEliminacion,$InsertecodUsuario]);  
        
        $jsonData = json_encode($responseLog[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,202);
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
        $selectEstustus="SELECT cm.ecodMarca, cm.tNombre FROM catmarca cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cm.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 5' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }

    public function postRegistroRelMarcaModelo(Request $request) {
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $jsonecodMarca = isset($jsonX->ecodMarca) ? $jsonX->ecodMarca : [];
        $jsonarrModelos = isset($jsonX->arrModelos) ? $jsonX->arrModelos : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        
        $ecodMarca = (isset($jsonecodMarca->ecodMarca)&&$jsonecodMarca->ecodMarca!=""        ? "".(trim($jsonecodMarca->ecodMarca))."":   Null);
        
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");
       
        $deleteRelMarcaModelo="DELETE FROM relmarcamodelo WHERE ecodMarca = ?";
        $responsdeleteRelMarcaModelo = DB::select($deleteRelMarcaModelo,[$ecodMarca]); 
        
        foreach ($jsonarrModelos as $key => $value) {
            $uuie = Uuid::uuid4();
            $uuid2 = (isset($uuie)&&$uuie!="" ? "".(trim($uuie))."":   Null);   
            $ecodModelo = (isset($value->ecodModelo)&&$value->ecodModelo!="" ? "".(trim($value->ecodModelo))."":   Null);
            $inserRelMarcaModelo=" CALL `stpInsertarRelMarcaModelo`(?, ?, ?)";
            $responseRelMArcaModelo = DB::select($inserRelMarcaModelo,[$uuid2,$ecodMarca,$ecodModelo]);    
        }
        $data = [
            'Codigo' => $ecodMarca,
        ];

        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse,200);
    }
}
