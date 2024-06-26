<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use DB;
class OverdueLoansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // aumenta el tiempo máximo de ejecución de este script a 150 min: 
        ini_set ('max_execution_time', 36000); 
        // aumentar el tamaño de memoria permitido de este script: 
        ini_set ('memory_limit', '9600M');

        $excel = request('excel')??'';
        $date = request('date')??'2018-08-31';
        // Log::info($excel);
        $loans = DB::select("SELECT dbo.Prestamos.IdPrestamo,dbo.Prestamos.PresSaldoAct,dbo.Prestamos.PresCuotaMensual,dbo.Prestamos.PresFechaDesembolso,Producto.PrdDsc,dbo.Prestamos.PresNumero,dbo.Padron.IdPadron, DATEDIFF(month, Amortizacion.AmrFecPag, '".$date."') as meses_mora,dbo.Prestamos.prestasaint from dbo.Prestamos
        join dbo.Padron on Prestamos.IdPadron = Padron.IdPadron
        join dbo.Producto on Prestamos.PrdCod = Producto.PrdCod
        join dbo.Amortizacion on (Prestamos.IdPrestamo = Amortizacion.IdPrestamo and Amortizacion.AmrNroPag = (select max(AmrNroPag) from Amortizacion where Amortizacion.IdPrestamo = Prestamos.IdPrestamo AND Amortizacion.AMRSTS <>'X' ))
        where Prestamos.PresEstPtmo = 'V' and dbo.Prestamos.PresSaldoAct > 0 and Amortizacion.AmrFecPag <  cast('".$date."' as datetime)
        order by meses_mora DESC;");
        // $loans = DB::select("SELECT dbo.Prestamos.IdPrestamo,dbo.Prestamos.PresSaldoAct,dbo.Prestamos.PresCuotaMensual,dbo.Prestamos.PresFechaDesembolso,Producto.PrdDsc,dbo.Prestamos.PresNumero,dbo.Padron.IdPadron from dbo.Prestamos
        // join dbo.Padron on Prestamos.IdPadron = Padron.IdPadron
        // join dbo.Producto on Prestamos.PrdCod = Producto.PrdCod
        // join dbo.Amortizacion on (Prestamos.IdPrestamo = Amortizacion.IdPrestamo and Amortizacion.AmrNroPag = (select max(AmrNroPag) from Amortizacion where Amortizacion.IdPrestamo = Prestamos.IdPrestamo AND Amortizacion.AMRSTS <>'X' ))
        // where Prestamos.PresEstPtmo = 'V' and dbo.Prestamos.PresSaldoAct > 0 and Amortizacion.AmrFecPag <  cast('2018-08-31' as datetime) ;");

        
        $prestamos= [];
        global $rows_exacta;
        $rows_exacta = Array();
        array_push($rows_exacta,array('PTMO','Fecha Desembolso','Cuota Mensual','Saldo Actual','Tipo','Producto','Matricula','C.I.',' Ap. Paterno','Ap.Materno','Nombre','Segundo Nombre','Nro Celular','Días Mora','matricula','ci','ext','nom1','nom2','paterno','materno','tipo','Interes'));
        foreach($loans as $loan)
        {
            $padron = DB::table('Padron')->where('IdPadron',$loan->IdPadron)->first();
            $loan->PadTipo = utf8_encode(trim($padron->PadTipo));
            $loan->PadNombres = utf8_encode(trim($padron->PadNombres));
            $loan->PadNombres2do =utf8_encode(trim($padron->PadNombres2do));
            $loan->PadPaterno =utf8_encode(trim($padron->PadPaterno));
            $loan->PadMaterno =utf8_encode(trim($padron->PadMaterno));
            $loan->PadCedulaIdentidad =utf8_encode(trim($padron->PadCedulaIdentidad));
            $loan->PadExpCedula =utf8_encode(trim($padron->PadExpCedula));
            $loan->PadMatricula =utf8_encode(trim($padron->PadMatricula));
            $loan->PadCelular =utf8_encode(trim($padron->PadCelular));
            
            if($excel!='')//reporte excel hdp 
            {
                $row = array($loan->PresNumero,$loan->PresFechaDesembolso,$loan->PresCuotaMensual,$loan->PresSaldoAct,$loan->PadTipo,$loan->PrdDsc,$loan->PadMatricula,$loan->PadCedulaIdentidad,$loan->PadPaterno,$loan->PadMaterno,$loan->PadNombres,$loan->PadNombres2do,$loan->PadCelular,$loan->meses_mora,$loan->prestasaint);
                
                $garantes = DB::table('PrestamosLevel1')->where('IdPrestamo','=',$loan->IdPrestamo)->get();
                if(sizeof($garantes)>0)
                {
                    foreach($garantes as $garante)
                    {
                        $padron_gar = DB::table('Padron')->where('Padron.IdPadron','=',$garante->IdPadronGar)->first();
                        array_push($row,utf8_encode(trim($padron_gar->PadMatricula)),utf8_encode(trim($padron_gar->PadCedulaIdentidad)),utf8_encode(trim($padron_gar->PadExpCedula)),utf8_encode(trim($padron_gar->PadNombres)),utf8_encode(trim($padron_gar->PadNombres2do)),utf8_encode(trim($padron_gar->PadPaterno)),utf8_encode(trim($padron_gar->PadMaterno)),utf8_encode(trim($padron_gar->PadTipo)),'*');

                    }
                }
                array_push($rows_exacta,$row);    
            }else{
                array_push($prestamos,$loan);
            }
        }
        if($excel!='')//reporte excel hdp 
        {
            Excel::create('prestamos',function($excel)
            {
                global $rows_exacta;
                
                        $excel->sheet('mora_parcial',function($sheet) {
                                global $rows_exacta;
                
                                $sheet->fromModel($rows_exacta,null, 'A1', false, false);
                                $sheet->cells('A1:V1', function($cells) {
                                // manipulate the range of cells
                                $cells->setBackground('#058A37');
                                $cells->setFontColor('#ffffff');  
                                $cells->setFontWeight('bold');
                                });
                            });
    
                      
            })->download('xls');
        }else{

            return json_encode($prestamos);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
