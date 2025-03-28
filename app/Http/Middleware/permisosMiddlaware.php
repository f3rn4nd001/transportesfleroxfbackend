<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
/*use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;*/
use DB;
use App\Http\Controllers\seg\encriptar;

class permisosMiddlaware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    

    public function handle(Request $request, Closure $next)
    {
        $encriptar = new encriptar();

        $reqs=$request->all();
        foreach ($reqs as $key => $value) {
            if(array_key_exists($key, $reqs) ){
				if ($value != ''){
					${$key} =$value ;
                  	}
			}
        }
        

        $urls =json_decode($encriptar->shiftText($request['datos'], -23));
        $descompriheader =json_decode($encriptar->shiftText($request['headers'], -23));
        $Url = (isset($urls->urls) && $urls->urls != "" ? "'/" . (trim($urls->urls)) . "'" : "");
        $selectEcodCorreo = "SELECT * FROM bitcorreo bc WHERE bc.ecodCorreo = ".$descompriheader->ecodCorreo."
        AND bc.tToken = ".$descompriheader->token;
        $sqlEcodCorreo = DB::select(($selectEcodCorreo)); 
        if($sqlEcodCorreo){
            $ecodCorreo = (isset($sqlEcodCorreo[0]->ecodCorreo) && $sqlEcodCorreo[0]->ecodCorreo != "" ? "'" . (trim($sqlEcodCorreo[0]->ecodCorreo)) . "'" : "");             
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$ecodCorreo;
            $sqlEcodUsuario = DB::select(($selectEcodUsuario));  
            $tokencontroll = (isset($request->tokencontroll) && $request->tokencontroll != "" ? "'" . (trim($request->tokencontroll)) . "'" : "");             
           
           
            $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "'" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "'" : "");             
            $seleVAlicacion="SELECT count(*) AS dl FROM relusuariomenusubmenucontroller rumsc 
            LEFT JOIN catsubmenu csm ON csm.ecodSubmenu = rumsc.ecodSubmenu
            LEFT JOIN catcontroller cct on cct.ecodControler =rumsc.ecodController
            WHERE rumsc.ecodUsuario =".$ecodUsuario. 
            " AND csm.tUrl = ".$Url. 
            " AND rumsc.tToken =".$tokencontroll.
            " OR cct.turl =".$Url.
            " AND rumsc.tToken =".$tokencontroll.
            " AND rumsc.ecodUsuario =".$ecodUsuario;
            $sqlact = DB::select($seleVAlicacion);
            if ($sqlact[0]->dl >= 1) {
                return $next($request);
            }  
            else {
                $data = [
                    'mensaje'=>"Usuario invalido, No cuenta con los permisos",
                ];
                $jsonData = json_encode($data);
                $returResponse =$encriptar->shiftText($jsonData, 23);
                return response()->json($returResponse,401); 
            }     
        }
        else{
            $data = [
                'mensaje'=>"Token invalido, Inicie sesion nuevamente",
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
            return response()->json($returResponse,401); 
        }
    }
}