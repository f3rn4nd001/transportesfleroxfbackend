<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Models\catEmpresa;
use App\Http\Controllers\seg\objetArray;

class empresa extends Controller
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
        $select="SELECT cem.ecodEmpresas, cem.tNombre, cem.tRazonSocial, ctem.tNombre AS tipoEmpresa, cet.tNombre AS estado, cm.tNombre AS municipio, cem.nCP,
        ce.tNombre AS estatus FROM catempresas cem
        LEFT JOIN cattipoempresa ctem ON ctem.ecodTipoEmpresa = cem.ecodTipoEmpresa
        LEFT JOIN catestados cet ON cet.ecodEstado = cem.ecodEstado
        LEFT JOIN catmunicipios cm ON cm.ecodMunicipio = cem.ecodMunicipio
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cem.ecodEstatus WHERE 1=1 ".  
        (isset($ecodEmpresas)       ? " AND cem.ecodEmpresas LIKE ('%".$ecodEmpresas."%')"        : '').
        (isset($tNombre)        ? " AND cem.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($tRazonSocial)        ? " AND cem.tRazonSocial LIKE ('%".$tRazonSocial."%')"        : '').
        (isset($tipoEmpresa['ecodTipoEmpresa'])  ? " AND cem.ecodTipoEmpresa LIKE ('%".$tipoEmpresa['ecodTipoEmpresa']."%')"        : '').
        (isset($nCP)        ? " AND cem.nCP LIKE ('%".$nCP."%')"        : '').
        (isset($estado['ecodEstado'])  ? " AND cem.ecodEstado LIKE ('%".$estado['ecodEstado']."%')"        : '').
        (isset($municipio['ecodMunicipio'])  ? " AND cem.ecodMunicipio LIKE ('%".$municipio['ecodMunicipio']."%')"        : '').
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
        $select="SELECT cem.ecodEmpresas, cem.tNombre, cem.tRazonSocial, ctem.tNombre AS tipoEmpresa, cet.tNombre AS estado, cm.tNombre AS municipio, cem.nCP,
        ce.tNombre AS estatus,cem.ecodTipoEmpresa,cem.ecodEstado,cem.ecodMunicipio,cem.ecodEstatus,cem.tColonia,cem.tCalle,cem.nNumero,cem.tComplementos,cem.ecodCreacion,cem.fhCreacion,cem.ecodEdicion,
        cem.fhEdicion,concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor FROM catempresas cem
        LEFT JOIN cattipoempresa ctem ON ctem.ecodTipoEmpresa = cem.ecodTipoEmpresa
        LEFT JOIN catestados cet ON cet.ecodEstado = cem.ecodEstado
        LEFT JOIN catmunicipios cm ON cm.ecodMunicipio = cem.ecodMunicipio
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cem.ecodEstatus
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cem.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cem.ecodEdicion
        WHERE cem.ecodEmpresas = ?";
        $sql = DB::select($select,[$json]);   
        $data = [
            'sqlEmpresa'=>(isset($sql[0]) ? $sql[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Empresa) ? $jsonX->Empresa : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $tNombre = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $tRazonSocial = (isset($json->tRazonSocial)&&$json->tRazonSocial!=""        ? "".(trim($json->tRazonSocial))."":   Null);
        $tColonia = (isset($json->tColonia)&&$json->tColonia!=""        ? "".(trim($json->tColonia))."":   Null);
        $tCalle = (isset($json->tCalle)&&$json->tCalle!=""        ? "".(trim($json->tCalle))."":   Null);
        $tComplementos = (isset($json->tComplementos)&&$json->tComplementos!=""        ? "".(trim($json->tComplementos))."":   Null);
        $ecodEmpresas = (isset($json->ecodEmpresas)&&$json->ecodEmpresas!=""        ? "".(trim($json->ecodEmpresas))."":   Null);
        $ecodTipoEmpresa = (isset($json->ecodTipoEmpresa)&&$json->ecodTipoEmpresa!=""        ? "".(trim($json->ecodTipoEmpresa))."":   Null);
        $nCP = (isset($json->nCP)&&$json->nCP!=""        ? "".(trim($json->nCP))."":   Null);
        $ecodEstado = (isset($json->ecodEstado)&&$json->ecodEstado!=""        ? "".(trim($json->ecodEstado))."":   Null);
        $ecodMunicipio = (isset($json->ecodMunicipio)&&$json->ecodMunicipio!=""        ? "".(trim($json->ecodMunicipio))."":   Null);
        $nNumero = (isset($json->nNumero)&&$json->nNumero!=""        ? "".(trim($json->nNumero))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");       
        DB::beginTransaction();
        try {
            if ($ecodEmpresas == Null) {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":  Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatEmpresa`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$uuid2, $tNombre, $tRazonSocial, $tColonia, $tCalle, $ecodTipoEmpresa, $tComplementos, $nCP, $ecodEstado, $ecodMunicipio, $nNumero, $ecodEstatus, $InsertecodUsuario ]);
            }
            else{
                $this->logsEmpresa($ecodEmpresas);
                $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""  ? "".(trim($json->ecodEstatus))."":  Null);
                $ecodEmpresas = (isset($json->ecodEmpresas)&&$json->ecodEmpresas!=""  ? "".(trim($json->ecodEmpresas))."":  Null);
                $inser=" CALL `stpInsertarCatEmpresa`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$ecodEmpresas, $tNombre, $tRazonSocial, $tColonia, $tCalle, $ecodTipoEmpresa, $tComplementos, $nCP, $ecodEstado, $ecodMunicipio, $nNumero, $ecodEstatus, $InsertecodUsuario ]);
            }
            if ($response[0]->Codigo) {
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
        return response()->json($returResponse2);
    }

    public function logsEmpresa($data) {
        $sqllog=catEmpresa::where("ecodEmpresas",$data)->first();
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
        $logecodEmpresas  = (isset($sqllog->ecodEmpresas) && $sqllog->ecodEmpresas != ""          ? "" . (trim($sqllog->ecodEmpresas)) . "" : Null);             
        $logtNombre  = (isset($sqllog->tNombre) && $sqllog->tNombre != ""          ? "" . (trim($sqllog->tNombre)) . "" : Null);             
        $logtRazonSocial  = (isset($sqllog->tRazonSocial) && $sqllog->tRazonSocial != ""          ? "" . (trim($sqllog->tRazonSocial)) . "" : Null);             
        $logecodTipoEmpresa  = (isset($sqllog->ecodTipoEmpresa) && $sqllog->ecodTipoEmpresa != ""          ? "" . (trim($sqllog->ecodTipoEmpresa)) . "" : Null);             
        $logecodEstado  = (isset($sqllog->ecodEstado) && $sqllog->ecodEstado != ""          ? "" . (trim($sqllog->ecodEstado)) . "" : Null);             
        $logecodMunicipio  = (isset($sqllog->ecodMunicipio) && $sqllog->ecodMunicipio != ""          ? "" . (trim($sqllog->ecodMunicipio)) . "" : Null);             
        $lognCP  = (isset($sqllog->nCP) && $sqllog->nCP != ""          ? "" . (trim($sqllog->nCP)) . "" : Null);             
        $logtColonia  = (isset($sqllog->tColonia) && $sqllog->tColonia != ""          ? "" . (trim($sqllog->tColonia)) . "" : Null);             
        $logtCalle  = (isset($sqllog->tCalle) && $sqllog->tCalle != ""          ? "" . (trim($sqllog->tCalle)) . "" : Null);             
        $lognNumero  = (isset($sqllog->nNumero) && $sqllog->nNumero != ""          ? "" . (trim($sqllog->nNumero)) . "" : Null);             
        $logtComplementos  = (isset($sqllog->tComplementos) && $sqllog->tComplementos != ""          ? "" . (trim($sqllog->tComplementos)) . "" : Null);             
        $logecodEstatus  = (isset($sqllog->ecodEstatus) && $sqllog->ecodEstatus != ""          ? "" . (trim($sqllog->ecodEstatus)) . "" : Null);             
        $logfhCreacion  = (isset($sqllog->fhCreacion) && $sqllog->fhCreacion != ""          ? "" . (trim($sqllog->fhCreacion)) . "" : Null);             
        $logecodCreacion  = (isset($sqllog->ecodCreacion) && $sqllog->ecodCreacion != ""          ? "" . (trim($sqllog->ecodCreacion)) . "" : Null);             
        $logecodEdicion  = (isset($sqllog->ecodEdicion) && $sqllog->ecodEdicion != ""          ? "" . (trim($sqllog->ecodEdicion)) . "" : Null);             
        $logfhEdicion  = (isset($sqllog->fhEdicion) && $sqllog->fhEdicion != ""          ? "" . (trim($sqllog->fhEdicion)) . "" : Null);             
        $responseinsertarLog = "  CALL `stpInsertarLogEmpresa`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $responseLog =  DB::select($responseinsertarLog, [$loguuid2, $logecodEmpresas, $logtNombre, $logtRazonSocial, $logecodTipoEmpresa, $logecodEstado, $logecodMunicipio, $lognCP, $logtColonia, $logtCalle, $lognNumero, $logtComplementos, $logecodEstatus, $logecodCreacion, $logfhCreacion, $logecodEdicion, $logfhEdicion]); 
        return($sqllog);
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
        $select="SELECT cem.ecodEmpresas, cem.tNombre FROM catempresas cem
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cem.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cem.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }

    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        DB::beginTransaction();
        try {
            $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "".(trim($json->mEliminacion))."":   Null);
            $ecodEmpresas = (isset($jsonX->ecod)&&$jsonX->ecod!=""        ? "".(trim($jsonX->ecod))."":   Null);
            $sqllog = $this->logsEmpresa($ecodEmpresas);
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
            $sqlEcodUsuario = DB::select($selectEcodUsuario);
            $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");       
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);            
            $logecodEmpresas  = (isset($sqllog->ecodEmpresas) && $sqllog->ecodEmpresas != ""          ? "" . (trim($sqllog->ecodEmpresas)) . "" : Null);             
            $logtNombre  = (isset($sqllog->tNombre) && $sqllog->tNombre != ""          ? "" . (trim($sqllog->tNombre)) . "" : Null);             
            $logtRazonSocial  = (isset($sqllog->tRazonSocial) && $sqllog->tRazonSocial != ""          ? "" . (trim($sqllog->tRazonSocial)) . "" : Null);             
            $logecodTipoEmpresa  = (isset($sqllog->ecodTipoEmpresa) && $sqllog->ecodTipoEmpresa != ""          ? "" . (trim($sqllog->ecodTipoEmpresa)) . "" : Null);             
            $logecodEstado  = (isset($sqllog->ecodEstado) && $sqllog->ecodEstado != ""          ? "" . (trim($sqllog->ecodEstado)) . "" : Null);             
            $logecodMunicipio  = (isset($sqllog->ecodMunicipio) && $sqllog->ecodMunicipio != ""          ? "" . (trim($sqllog->ecodMunicipio)) . "" : Null);             
            $lognCP  = (isset($sqllog->nCP) && $sqllog->nCP != ""          ? "" . (trim($sqllog->nCP)) . "" : Null);             
            $logtColonia  = (isset($sqllog->tColonia) && $sqllog->tColonia != ""          ? "" . (trim($sqllog->tColonia)) . "" : Null);             
            $logtCalle  = (isset($sqllog->tCalle) && $sqllog->tCalle != ""          ? "" . (trim($sqllog->tCalle)) . "" : Null);             
            $lognNumero  = (isset($sqllog->nNumero) && $sqllog->nNumero != ""          ? "" . (trim($sqllog->nNumero)) . "" : Null);             
            $logtComplementos  = (isset($sqllog->tComplementos) && $sqllog->tComplementos != ""          ? "" . (trim($sqllog->tComplementos)) . "" : Null);             
            $logfhCreacion  = (isset($sqllog->fhCreacion) && $sqllog->fhCreacion != ""          ? "" . (trim($sqllog->fhCreacion)) . "" : Null);             
            $logecodCreacion  = (isset($sqllog->ecodCreacion) && $sqllog->ecodCreacion != ""          ? "" . (trim($sqllog->ecodCreacion)) . "" : Null);             
            $logecodEdicion  = (isset($sqllog->ecodEdicion) && $sqllog->ecodEdicion != ""          ? "" . (trim($sqllog->ecodEdicion)) . "" : Null);             
            $logfhEdicion  = (isset($sqllog->fhEdicion) && $sqllog->fhEdicion != ""          ? "" . (trim($sqllog->fhEdicion)) . "" : Null);             
            $logecodEstatus = "fa6cc9a2-f221-4e27-b575-1fac2698d27a";
            $responseEliminarEmpresa = "  CALL `stpEliminarEmpresa`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $response = DB::select($responseEliminarEmpresa, [$loguuid2, $logecodEmpresas, $logtNombre, $logtRazonSocial, $logecodTipoEmpresa, $logecodEstado, $logecodMunicipio, $lognCP, $logtColonia, $logtCalle, $lognNumero, $logtComplementos, $logecodEstatus, $logecodCreacion, $logfhCreacion, $InsertecodUsuario, $mEliminacion]); 
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
}
