<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class operadorpsp extends Controller
{
    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."": Null);
        $selectoperador="SELECT cop.ecodOperadorPSP, cop.ecodUsuario, ctp.tNombre AS TipoPago, cop.tCorreo, cop.ecodBanco, cb.tNombre AS Banco, cop.ecodMoneda, cm.tNombre AS Moneda, cop.ecodtipopago, cop.tCuentaBancaria, cop.nTelefono, cop.ecodEstatus, 
        concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador,
        concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cop.fhCreacion, cop.fhEdicion, ce.tNombre AS Estatus
        FROM catoperadorpsp cop
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cop.ecodCreacion
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cop.ecodEdicion
        LEFT JOIN catestatus ce ON ce.ecodEstatus= cop.ecodEstatus
        LEFT JOIN cattipopago ctp ON ctp.ecodtipopago=cop.ecodtipopago
        LEFT JOIN catmoneda cm on cm.ecodMoneda =cop.ecodMoneda
        LEFT JOIN catbanco cb ON cb.ecodBanco = cop.ecodBanco
        WHERE cop.ecodUsuario = ?";
        $sqlOperador = DB::select($selectoperador,[$json]);

        $selectusuario="SELECT concat_ws('',cu.tNombre,' ',cu.tApellido) AS nombreUsuario,cu.tRFC,cu.tCRUP from catusuarios cu 
        WHERE cu.ecodUsuario = ?";   
        $sqlusuario = DB::select($selectusuario,[$json]);

        $data = [
            'sqlOperadpPSP'=>(isset($sqlOperador[0]) ? $sqlOperador[0] : ""),
            'sqlUsuario'=>(isset($sqlusuario[0]) ? $sqlusuario[0] : ""),
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->OperadorPSP) ? $jsonX->OperadorPSP : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));     
        $nTelefono = (isset($json->nTelefono)&&$json->nTelefono!=""        ? "".(trim($json->nTelefono))."":   Null);
        $tCorreo = (isset($json->tCorreo)&&$json->tCorreo!=""        ? "".(trim($json->tCorreo))."":   Null);
        $ecodBanco = (isset($json->ecodBanco)&&$json->ecodBanco!=""        ? "".(trim($json->ecodBanco))."":   Null);
        $ecodtipopago = (isset($json->ecodtipopago)&&$json->ecodtipopago!=""        ? "".(trim($json->ecodtipopago))."":   Null);
        $ecodOperador = (isset($json->ecodOperador)&&$json->ecodOperador!=""        ? "".(trim($json->ecodOperador))."":   Null);
        $tRFC = (isset($json->tRFC)&&$json->tRFC!=""        ? "".(trim($json->tRFC))."":   Null);
        $tCRUP = (isset($json->tCRUP)&&$json->tCRUP!=""        ? "".(trim($json->tCRUP))."":   Null);
        $ecodMoneda = (isset($json->ecodMoneda)&&$json->ecodMoneda!=""        ? "".(trim($json->ecodMoneda))."":   Null);
        $tCuentaBancaria = (isset($json->tCuentaBancaria)&&$json->tCuentaBancaria!=""        ? "".(trim($json->tCuentaBancaria))."":   Null);
        $ecodUsuario = (isset($json->ecodUsuario)&&$json->ecodUsuario!=""        ? "".(trim($json->ecodUsuario))."":   Null);

        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
      
        DB::beginTransaction();
        try {
            if ($ecodOperador == Null) {
                $uui = Uuid::uuid4();
                $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":  Null);
                $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
                $inser=" CALL `stpInsertarCatOperadorPSP`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$uuid2, $ecodUsuario, $ecodtipopago, $ecodMoneda, $ecodBanco, $tCuentaBancaria, $tCorreo, $nTelefono, $tRFC, $tCRUP, $ecodEstatus, $InsertecodUsuario ]);
            }
            else {                
                $ecodEstatus = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""  ? "".(trim($json->ecodEstatus))."":  Null);
                $inser=" CALL `stpInsertarCatOperadorPSP`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $response =  DB::select($inser, [$ecodOperador, $ecodUsuario, $ecodtipopago, $ecodMoneda, $ecodBanco, $tCuentaBancaria, $tCorreo, $nTelefono, $tRFC, $tCRUP, $ecodEstatus, $InsertecodUsuario ]);
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
