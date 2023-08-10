<?php

namespace App\Console\Commands;
use Twilio\Rest\Client;
use Illuminate\Console\Command;
use DB;
class mensajesW extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hello:worl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $select="SELECT bc.tmonitoreo,bc.ecodCliente,bc.ecodViaje,bc.treferencia, bc.tpedido,bc.tDestino,bc.tOrigen,concat_ws('',cu.tNombre,' ', cu.tApellido) AS operador,concat_ws(' ',cuc.tNombre,' ', cuc.tApellido) AS cliente,ce.tNombre AS Estatus, bc.fhSalida, bc.fhLlegada FROM bitviajes bc
        LEFT JOIN catestatus ce ON ce.EcodEstatus = bc.EcodEstatus
        LEFT JOIN catusuarios cu ON cu.ecodUsuarios = bc.ecodOperados
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuarios=bc.ecodCliente
        WHERE  bc.EcodEstatus <> 4 AND bc.tmonitoreo = 1";
        $sql = DB::select(DB::raw($select));
      
        foreach ($sql as $key => $v){
			$resViajes[]=array(
				'ecodCliente' => $v->ecodCliente,
                'ecodViaje' => $v->ecodViaje,
				'treferencia' => $v->treferencia,
                'tpedido' => $v->tpedido,
                'tDestino' => $v->tDestino,
                'tOrigen' => $v->tOrigen,
                'operador' => $v->operador,
                'cliente' => $v->cliente,
                'Estatus' => $v->Estatus,
                'fhSalida' => $v->fhSalida,
                'fhLlegada' => $v->fhLlegada,
                'tmonitoreo'=> $v->tmonitoreo
            );
        }
        info($sql);
      $sid    = "AC734689817fa8c10fa5a3d1a19fde276b"; 
        $token  = "5651dc516edc944ea65b3f753e7ebdf2"; 
        $exito = 1;
    
        $twilio = new Client($sid, $token); 
        $message = $twilio->messages 
                        ->create("whatsapp:+5213141970232", // to 
                                array( 
                                    "from" => "whatsapp:+14155238886",       
                                    "body" => "El viaje con el numero: $sql  
Referencia: 
No. Pedido: 
Con destino a: 
Fecha de llegada:
Operador: 
Ubicacion actual 
"));  
        return 0;
    }
}
