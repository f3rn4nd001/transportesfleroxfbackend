<?php

namespace App\Http\Controllers\Catalogo\Autotransportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class operador extends Controller
{
    public function getDetallesLicencia(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."": Null);
        $selectoperador="SELECT cot.ecodOperador, cot.ecodUsuario, cot.nLicencia, cot.ecodEdicion, cot.ecodCreacion, cot.fhExpedicion, cot.fhVencimiento, cot.ecodEstatus, cot.tTipo, cot.tClase, concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,
        concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cot.fhCreacion, cot.fhEdicion, ce.tNombre AS Estatus 
        FROM catoperadortransportes cot 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cot.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cot.ecodEdicion
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cot.ecodEstatus
        WHERE cot.ecodUsuario = ?";
        $sqlOperador = DB::select($selectoperador,[$json]);

        $selectusuario="SELECT concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombreUsuario,cu.tRFC,cu.tCRUP from catusuarios cu 
        WHERE cu.ecodUsuario = ?";   
        $sqlusuario = DB::select($selectusuario,[$json]);

        $data = [
            'sqlLicencia'=>(isset($sqlOperador[0]) ? $sqlOperador[0] : ""),
            'sqlUsuario'=>(isset($sqlusuario[0]) ? $sqlusuario[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistroLicencia(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Licencia) ? $jsonX->Licencia : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));

        $nLicencia = (isset($json->nLicencia)&&$json->nLicencia!=""        ? "".(trim($json->nLicencia))."":   Null);
        $fhExpedicion = (isset($json->fhExpedicion)&&$json->fhExpedicion!=""        ? "".(trim($json->fhExpedicion))."":   Null);
        $fhVencimiento = (isset($json->fhVencimiento)&&$json->fhVencimiento!=""        ? "".(trim($json->fhVencimiento))."":   Null);
        $tTipo = (isset($json->tTipo)&&$json->tTipo!=""        ? "".(trim($json->tTipo))."":   Null);
        $tClase = (isset($json->tClase)&&$json->tClase!=""        ? "".(trim($json->tClase))."":   Null);
        $ecodUsuario = (isset($json->ecodUsuario)&&$json->ecodUsuario!=""        ? "".(trim($json->ecodUsuario))."":   Null);
        $ecodOperador = (isset($json->ecodOperador)&&$json->ecodOperador!=""        ? "".(trim($json->ecodOperador))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");       
       
        DB::beginTransaction();
        try {
            if ($ecodOperador == Null) { 
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":  Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatOperadorTransportes`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$uuid2, $nLicencia, $fhExpedicion, $fhVencimiento, $tTipo, $tClase, $ecodUsuario, $ecodEstatus, $InsertecodUsuario ]);
            }
            else{ 
                $inser=" CALL `stpInsertarCatOperadorTransportes`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $response =  DB::select($inser, [$ecodOperador, $nLicencia, $fhExpedicion, $fhVencimiento, $tTipo, $tClase, $ecodUsuario, $ecodEstatus, $InsertecodUsuario ]);
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
}
