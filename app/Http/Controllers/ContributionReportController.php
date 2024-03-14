<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ContributionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function contribution_report($report_type)
    {
        ini_set('max_execution_time', 36000);
        // aumentar el tamaño de memoria permitido de este script: 
        ini_set('memory_limit', '9600M');
        
        global $rows_headers;
        $rows_headers = array();
        
        switch($report_type)
        { 
            case 1:
                $contributions = DB::select(DB::raw("
                SELECT 
                    padron.padcedulaidentidad AS ci,
                    padron.padmatricula AS matricula,
                    padron.padnombres AS nombres,
                    padron.padpaterno AS paterno,
                    padron.padmaterno AS materno,
                    padron.padapellidocasada AS apellido_casada,
                    prsAportesMes.tasagestion AS gestion,
                    prsAportesMes.mescod AS mes_aporte,
                    prsAportesMes.prsapormestipo AS tipo_aporte,
                    prsAportesMes.prsapormescotiz AS aporte_mes_cotizable,
                    prsAportesMes.prsapormesvalaporte AS subtotal,
                    prsAportesMes.prsapormesvalaporteipc AS ipc,
                    prsAportesMes.prsapormesvalaportetot AS aporte_mes_total,
                    PrsAportes.sectcod AS tipo_afiliado,
                    PrsAportes.prsaporgestion AS aporte_ultimo_gestion,
                    PrsAportes.prsapormes AS aporte_ultimo_mes,
                    PrsAportes.prsaporfecultmov AS fecha_ultimo_aporte,
                    PrsAportes.prsclasifcod AS clasificacion,
                    PrsAportes.prsapormonto AS prsa_por_monto
                FROM PrsAportes
                LEFT JOIN padron ON (PrsAportes.idpadron = padron.idpadron)
                LEFT JOIN prsAportesMes ON (PrsAportes.prscod = prsAportesMes.prscod)
                ORDER BY padron.PadCedulaIdentidad, padron.PadMatricula, prsAportesMes.tasagestion, prsAportesMes.mescod ASC
            "));
    
            array_push($rows_headers, array(
                'CI',
                'Matricula',
                'Nombres',
                'Apellido Paterno',
                'Apellido Materno',
                'Apellido Casada',
                'Gestion',
                'Mes Aporte',
                'Tipo Aporte',
                'Aporte Mes Cotizable',
                'Subtotal',
                'IPC',
                'Aporte Mes Total',
                'Tipo Afiliado',
                'Aporte Último Gestion',
                'Aporte Último Mes',
                'Fecha Último Aporte',
                'Clasificación',
                'PRSA por Monto'
            ));
    
            foreach ($contributions as $contribution) {
                array_push($rows_headers, array(
                    $contribution->ci,
                    $contribution->matricula,
                    $contribution->nombres,
                    $contribution->paterno,
                    $contribution->materno,
                    $contribution->apellido_casada,
                    $contribution->gestion,
                    $contribution->mes_aporte,
                    $contribution->tipo_aporte,
                    $contribution->aporte_mes_cotizable,
                    $contribution->subtotal,
                    $contribution->ipc,
                    $contribution->aporte_mes_total,
                    $contribution->tipo_afiliado,
                    $contribution->aporte_ultimo_gestion,
                    $contribution->aporte_ultimo_mes,
                    $contribution->fecha_ultimo_aporte,
                    $contribution->clasificacion,
                    $contribution->prsa_por_monto
                ));
            }
            break;
        }

        Excel::create('contribuciones', function ($excel) {
            
            $excel->sheet('contribuciones', function ($sheet) {
                global $rows_headers;
                $sheet->fromModel($rows_headers, null, 'A1', false, false);
                $sheet->cells('A1:S1', function ($cells) {
                    $cells->setBackground('#058A37');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                });
            });
        })->download('xls');
    }
}
