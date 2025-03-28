<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class usuario extends Controller
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
        $selectEstustus="SELECT cu.ecodUsuario, concat_ws('',cu.tNombre,' ', cu.tApellido) as tNombre,cu.ecodTipoUsuario ,cu.tApellido,cu.tRFC,cu.tCRUP, ce.tNombre AS estatus FROM catusuarios AS cu 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =cu.ecodEstatus". 
        " LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario = cu.ecodTipoUsuario".
        " WHERE 1=1 ".  
        (isset($ecodUsuario)    ? " AND cu.ecodUsuario  LIKE ('%".$ecodUsuario ."%')"   : '').
        (isset($tNombre)        ? " AND  concat_ws('',cu.tNombre,' ', cu.tApellido) LIKE ('%".$tNombre."%')"             : '').
        (isset($tRFC)           ? " AND cu.tRFC LIKE ('%".$tRFC."%')"                   : '').
        (isset($tCRUP)          ? " AND cu.tCRUP LIKE ('%".$tCRUP."%')"                 : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"             : '').
        (isset($TipoUsuario)? " AND ctu.tNombre LIKE ('%".$TipoUsuario."%')" : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json( $returResponse);  
    }
    
    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":   Null);
        $selectUsuario="SELECT concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,cu.tNotas,cu.ecodEdicion,cu.iUsuario, cu.ecodEstatus,concat_ws('',cu.tNombre,' ',cu.tApellido) as Nombre,
        cu.tCRUP,cu.tRFC,ce.tNombre as Estatus, cu.ecodUsuario,cu.fhCreacion,cu.tNombre, cu.tApellido,cu.nEdad,cu.nTelefono,cu.tSexo,
        cu.ecodCreacion,cu.fhEdicion, concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cu.fhNacimiento, cu.ecodTipoUsuario,ctu.tNombre AS TipoUsuario FROM catusuarios cu
        LEFT JOIN catusuarios cuc ON cuc.ecodCreacion = cu.ecodCreacion
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cu.ecodEstatus 
        LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario = cu.ecodTipoUsuario 
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cu.ecodEdicion
        WHERE cu.ecodUsuario = ?";
                           
        $sqlUsusario = DB::select($selectUsuario,[$json]);   
        $selectCorreo="SELECT bc.ecodCorreo,bc.tCorreo,bc.tToken FROM relusuariocorreo ruc
        LEFT JOIN bitcorreo bc ON bc.ecodCorreo=ruc.ecodCorreo
        WHERE ruc.ecodUsuario = ?";
        $sqlCorreo = DB::select($selectCorreo,[$json]);
        $data = [
            'sqlUsusario'=>(isset($sqlUsusario[0]) ? $sqlUsusario[0] : ""),
            'sqlCorreo'=>(isset($sqlCorreo) ? $sqlCorreo : "")
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Usuario) ? $jsonX->Usuario : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $jsonarrCorreo  = isset($json->arrCorreo) ? $json->arrCorreo : [];
        $tNombre    = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $tApellido  = (isset($json->tApellido)&&$json->tApellido!=""    ? "".(trim($json->tApellido))."":   Null);
        $tCRUP      = (isset($json->tCRUP)&&$json->tCRUP!=""            ? "".(trim($json->tCRUP))."":   Null);
        $tRFC       = (isset($json->tRFC)&&$json->tRFC!=""              ? "".(trim($json->tRFC))."":   Null);
        $tSexo      = (isset($json->tSexo)&&$json->tSexo!=""            ? "".(trim($json->tSexo))."":   Null);
        $nEdad      = (isset($json->nEdad)&&$json->nEdad!=""            ? "".(trim($json->nEdad))."":   Null);
        $nTelefono  = (isset($json->nTelefono)&&$json->nTelefono!=""    ? "".(trim($json->nTelefono))."":   Null);
        $fhNacimiento = (isset($json->fhNacimiento)&&$json->fhNacimiento!="" ? "".(trim($json->fhNacimiento))."":   Null);
        $iUsuario   = (isset($json->iUsuario)&&$json->iUsuario!=""      ? "".(trim($json->iUsuario))."":   Null);
        $ecodUsuario = (isset($json->ecodUsuario)&&$json->ecodUsuario!="" ? "".(trim($json->ecodUsuario))."":   Null);
        $tNotas = (isset($json->tNotas)&&$json->tNotas!="" ? "".(trim($json->tNotas))."":   Null);
        $ecodTipoUsuario = (isset($json->ecodTipoUsuario)&&$json->ecodTipoUsuario!="" ? "".(trim($json->ecodTipoUsuario))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
        if ($ecodUsuario == Null) {
            $uuiecodUsuario = Uuid::uuid4();
            $uuid2ecodUsuario = (isset($uuiecodUsuario)&&$uuiecodUsuario!="" ? "".(trim($uuiecodUsuario))."":   Null);
            $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
            $inserUsuario=" CALL `stpInsertarCatUsuario`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $responseUsuario = DB::select($inserUsuario, [$uuid2ecodUsuario, $tNombre, $tApellido, $tCRUP, $tRFC, $tSexo, $nTelefono, $tNotas, $nEdad, $ecodEstatus, $ecodTipoUsuario, $InsertecodUsuario, $fhNacimiento, $iUsuario]); 
            if (count($jsonarrCorreo) > 0) {
                foreach ($jsonarrCorreo as $key => $value) {
                    $uuiecodCorreo = Uuid::uuid4();   
                    $uuid2uuiecodCorreo = (isset($uuiecodCorreo)&&$uuiecodCorreo!="" ? "".(trim($uuiecodCorreo))."":   Null);
                    $tCorreo  = (isset($value->tCorreo)&&$value->tCorreo!=""            ? "".(trim($value->tCorreo))."":   Null);
                    $tContrasena  = (isset($value->tContrasena)&&$value->tContrasena!=""            ? "".(trim($value->tContrasena))."":   Null);  
                    $inserCorreo=" CALL `stpInsertarBitCorreo`(?,?,?)";
                    $responseCorreo = DB::select($inserCorreo,[$uuid2uuiecodCorreo,$tCorreo,$tContrasena]); 
                    $uuiecodRelUsuarioCorreo = Uuid::uuid4();
                    $uuid2uuiecodRelUsuarioCorreo = (isset($uuiecodRelUsuarioCorreo)&&$uuiecodRelUsuarioCorreo!="" ? "".(trim($uuiecodRelUsuarioCorreo))."":   Null);
                    $inserRelUsuarioCorreo=" CALL `stpInsertarRelUsuarioCorreo`(?,?,?)";
                    $responseRelUsuarioCorreo = DB::select($inserRelUsuarioCorreo,[$uuid2uuiecodRelUsuarioCorreo,$uuid2uuiecodCorreo,$uuid2ecodUsuario]); 
                }
            } 
        }
        else{
            $selectlogcatUsuario="SELECT * FROM catusuarios cu WHERE cu.ecodUsuario = ?";
            $sqllogCatUsuario = DB::select($selectlogcatUsuario,[$ecodUsuario]);
            $logtNombre     = (isset($sqllogCatUsuario[0]->tNombre) && $sqllogCatUsuario[0]->tNombre != ""      ? "" . (trim($sqllogCatUsuario[0]->tNombre)) . "" : "");             
            $logtApellido   = (isset($sqllogCatUsuario[0]->tApellido) && $sqllogCatUsuario[0]->tApellido != ""  ? "" . (trim($sqllogCatUsuario[0]->tApellido)) . "" : "");             
            $logtCRUP       = (isset($sqllogCatUsuario[0]->tCRUP) && $sqllogCatUsuario[0]->tCRUP != ""          ? "" . (trim($sqllogCatUsuario[0]->tCRUP)) . "" : Null);             
            $logtRFC        = (isset($sqllogCatUsuario[0]->tRFC) && $sqllogCatUsuario[0]->tRFC != ""            ? "" . (trim($sqllogCatUsuario[0]->tRFC)) . "" : Null);             
            $logtSexo       = (isset($sqllogCatUsuario[0]->tSexo) && $sqllogCatUsuario[0]->tSexo != ""          ? "" . (trim($sqllogCatUsuario[0]->tSexo)) . "" : Null);             
            $lognEdad       = (isset($sqllogCatUsuario[0]->nEdad) && $sqllogCatUsuario[0]->nEdad != ""          ? "" . (trim($sqllogCatUsuario[0]->nEdad)) . "" : Null);             
            $lognTelefono   = (isset($sqllogCatUsuario[0]->nTelefono) && $sqllogCatUsuario[0]->nTelefono != ""  ? "" . (trim($sqllogCatUsuario[0]->nTelefono)) . "" : Null);             
            $logecodEstatus = (isset($sqllogCatUsuario[0]->ecodEstatus) && $sqllogCatUsuario[0]->ecodEstatus != ""  ? "" . (trim($sqllogCatUsuario[0]->ecodEstatus)) . "" : "");             
            $logecodTipoUsuario = (isset($sqllogCatUsuario[0]->ecodTipoUsuario) && $sqllogCatUsuario[0]->ecodTipoUsuario != ""  ? "" . (trim($sqllogCatUsuario[0]->ecodTipoUsuario)) . "" : Null);             
            $logfhCreacion  = (isset($sqllogCatUsuario[0]->fhCreacion) && $sqllogCatUsuario[0]->fhCreacion != "" ? "" . (trim($sqllogCatUsuario[0]->fhCreacion)) . "" : "");             
            $logecodCreacion = (isset($sqllogCatUsuario[0]->ecodCreacion) && $sqllogCatUsuario[0]->ecodCreacion != "" ? "" . (trim($sqllogCatUsuario[0]->ecodCreacion)) . "" : "");             
            $logecodEdicion  = (isset($sqllogCatUsuario[0]->ecodEdicion) && $sqllogCatUsuario[0]->ecodEdicion != "" ? "" . (trim($sqllogCatUsuario[0]->ecodEdicion)) . "" : Null);             
            $logfhEdicion   = (isset($sqllogCatUsuario[0]->fhEdicion) && $sqllogCatUsuario[0]->fhEdicion != ""   ? "" . (trim($sqllogCatUsuario[0]->fhEdicion)) . "" : Null);             
            $logfhNacimiento = (isset($sqllogCatUsuario[0]->fhNacimiento) && $sqllogCatUsuario[0]->fhNacimiento != "" ? "" . (trim($sqllogCatUsuario[0]->fhNacimiento)) . "" : Null);             
            $logiUsuario    = (isset($sqllogCatUsuario[0]->iUsuario) && $sqllogCatUsuario[0]->iUsuario != ""     ? "" . (trim($sqllogCatUsuario[0]->iUsuario)) . "" : Null);             
            $logtNotas    = (isset($sqllogCatUsuario[0]->tNotas) && $sqllogCatUsuario[0]->tNotas != ""     ? "" . (trim($sqllogCatUsuario[0]->tNotas)) . "" : Null);             
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            $inserLogUsuario=" CALL `stpInsertarLogCatUsuario`(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $responseLogUsuario = DB::select($inserLogUsuario,[$loguuid2, $ecodUsuario, $logtNombre, $logtApellido, $logtCRUP, $logtRFC, $logtSexo, $lognTelefono, $logtNotas, $lognEdad, $logecodEstatus, $logecodTipoUsuario,$logfhNacimiento, $logecodCreacion, $logfhCreacion, $logecodEdicion, $logfhEdicion, $logiUsuario]); 
            $ecodEstatus = (isset($json->ecodEstatus)&&$json->ecodEstatus!="" ? "".(trim($json->ecodEstatus))."":   Null);
            $inserUsuario=" CALL `stpInsertarCatUsuario`(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $responseUsuario = DB::select($inserUsuario,[$ecodUsuario, $tNombre, $tApellido, $tCRUP, $tRFC, $tSexo, $nTelefono, $tNotas, $nEdad, $ecodEstatus, $ecodTipoUsuario, $InsertecodUsuario, $fhNacimiento, $iUsuario]); 
            if (count($jsonarrCorreo) > 0) {
                foreach ($jsonarrCorreo as $key => $value) {
                    $tCorreo  = (isset($value->tCorreo)&&$value->tCorreo!=""            ? "".(trim($value->tCorreo))."":   Null);
                    $ecodCorreo  = (isset($value->ecodCorreo)&&$value->ecodCorreo!=""   ? "".(trim($value->ecodCorreo))."":   Null);
                    if ($ecodCorreo == Null) {
                        $uuiecodCorreo = Uuid::uuid4();
                        $uuid2uuiecodCorreo = (isset($uuiecodCorreo)&&$uuiecodCorreo!="" ? "".(trim($uuiecodCorreo))."":   Null);
                        $tContrasena  = (isset($value->tContrasena)&&$value->tContrasena!=""  ? "".(trim($value->tContrasena))."":   Null);      
                        $inserCorreo=" CALL `stpInsertarBitCorreo`(?,?,?)";
                        $responseCorreo = DB::select($inserCorreo,[$uuid2uuiecodCorreo,$tCorreo,$tContrasena]); 
                        $uuiecodRelUsuarioCorreo = Uuid::uuid4();
                        $uuid2uuiecodRelUsuarioCorreo = (isset($uuiecodRelUsuarioCorreo)&&$uuiecodRelUsuarioCorreo!="" ? "".(trim($uuiecodRelUsuarioCorreo))."":   Null);
                        $inserRelUsuarioCorreo=" CALL `stpInsertarRelUsuarioCorreo`(?,?,?)";
                        $responseRelUsuarioCorreo = DB::select($inserRelUsuarioCorreo,[$uuid2uuiecodRelUsuarioCorreo,$uuid2uuiecodCorreo,$ecodUsuario]); 
                    }
                    else {
                        $tContrasena  =  Null;      
                        $inserCorreo=" CALL `stpInsertarBitCorreo`(?,?,?)";
                        $responseCorreo = DB::select($inserCorreo,[$ecodCorreo,$tCorreo,$tContrasena]); 
                    }
                }
            }
        }
        $jsonData = json_encode($responseUsuario[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
    
    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Usuario) ? $jsonX->Usuario : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        DB::beginTransaction();
        try {
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
            $sqlEcodUsuario = DB::select($selectEcodUsuario); 
            $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
            $ecodUsuario = (isset($jsonX->ecodUsuario)&&$jsonX->ecodUsuario!="" ? "".(trim($jsonX->ecodUsuario))."":   Null);
            $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "".(trim($json->mEliminacion))."":   Null);
            $selectlogcatUsuario="SELECT * FROM catusuarios cu WHERE cu.ecodUsuario  = ?";
            $sqllogCatUsuario= DB::select($selectlogcatUsuario,[$ecodUsuario]); 
            $logtNombre  = (isset($sqllogCatUsuario[0]->tNombre) && $sqllogCatUsuario[0]->tNombre != ""          ? "" . (trim($sqllogCatUsuario[0]->tNombre)) . "" : Null);             
            $logtApellido  = (isset($sqllogCatUsuario[0]->tApellido) && $sqllogCatUsuario[0]->tApellido != ""          ? "" . (trim($sqllogCatUsuario[0]->tApellido)) . "" : Null);             
            $logtCRUP  = (isset($sqllogCatUsuario[0]->tCRUP) && $sqllogCatUsuario[0]->tCRUP != ""          ? "" . (trim($sqllogCatUsuario[0]->tCRUP)) . "" : Null);             
            $logtRFC  = (isset($sqllogCatUsuario[0]->tRFC) && $sqllogCatUsuario[0]->tRFC != ""          ? "" . (trim($sqllogCatUsuario[0]->tRFC)) . "" : Null);             
            $lognEdad  = (isset($sqllogCatUsuario[0]->nEdad) && $sqllogCatUsuario[0]->nEdad != ""          ? "" . (trim($sqllogCatUsuario[0]->nEdad)) . "" : Null);             
            $logtSexo  = (isset($sqllogCatUsuario[0]->tSexo) && $sqllogCatUsuario[0]->tSexo != ""          ? "" . (trim($sqllogCatUsuario[0]->tSexo)) . "" : Null);             
            $lognTelefono  = (isset($sqllogCatUsuario[0]->nTelefono) && $sqllogCatUsuario[0]->nTelefono != ""          ? "" . (trim($sqllogCatUsuario[0]->nTelefono)) . "" : Null);             
            $logtNotas  = (isset($sqllogCatUsuario[0]->tNotas) && $sqllogCatUsuario[0]->tNotas != ""          ? "" . (trim($sqllogCatUsuario[0]->tNotas)) . "" : Null);             
            $logfhCreacion  = (isset($sqllogCatUsuario[0]->fhCreacion) && $sqllogCatUsuario[0]->fhCreacion != ""          ? "" . (trim($sqllogCatUsuario[0]->fhCreacion)) . "" : Null);             
            $logiUsuario  = (isset($sqllogCatUsuario[0]->iUsuario) && $sqllogCatUsuario[0]->iUsuario != ""          ? "" . (trim($sqllogCatUsuario[0]->iUsuario)) . "" : Null);             
            $logecodCreacion  = (isset($sqllogCatUsuario[0]->ecodCreacion) && $sqllogCatUsuario[0]->ecodCreacion != ""          ? "" . (trim($sqllogCatUsuario[0]->ecodCreacion)) . "" : Null);             
            $logfhNacimiento  = (isset($sqllogCatUsuario[0]->fhNacimiento) && $sqllogCatUsuario[0]->fhNacimiento != ""          ? "" . (trim($sqllogCatUsuario[0]->fhNacimiento)) . "" : Null);             
            $logecodEstatus = "fa6cc9a2-f221-4e27-b575-1fac2698d27a";
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            $inserLogUsuario=" CALL `stpInsertarLogCatUsuarioEliminar`(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $responseLogUsuario = DB::select($inserLogUsuario,[$loguuid2,$ecodUsuario,$logtNombre,$logtApellido,$logtCRUP,$logtRFC,$logtSexo,$lognTelefono,$logtNotas,$lognEdad,$logecodEstatus,$logfhNacimiento,$logecodCreacion,$logfhCreacion,$mEliminacion,$InsertecodUsuario,$logiUsuario]); 
            if ($responseLogUsuario[0]->mensaje) {
                DB::commit();
            } else {
                DB::rollback();
            }  
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        } 
        $jsonData = json_encode($responseLogUsuario[0]);
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
        $selecUsuario="SELECT  concat_ws('',cu.tNombre,' ', cu.tApellido) as tNombre, cu.ecodUsuario FROM catusuarios cu
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cu.ecodEstatus
        LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario = cu.ecodTipoUsuario
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($TipoUsuario)? " AND ctu.tNombre LIKE ('%".$TipoUsuario."%')" : '').
        (isset($tNombre)  ? " AND concat_ws('',cu.tNombre,' ', cu.tApellido) LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selecUsuario);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json( $returResponse2);
    }
}
