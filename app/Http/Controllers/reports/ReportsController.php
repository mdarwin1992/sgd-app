<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //
    public function generatePDF()
    {

        $options = new QROptions([
            'version' => 10,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel' => QRCode::ECC_H,
            'scale' => 3,
            'imageBase64' => true,
            'drawCircularModules' => true,
            'circleRadius' => 0.45,
        ]);

        $data = [
            'QR' => (new QRCode($options))->render('/darboar'),
        ];

        $pdf = PDF::loadView('reports.ticket.ticket', $data);

        // Configurar el tamaño del papel
        $pdf->setPaper([0, 0, 320, 260.59], 'portrait');

        // 226.77 es el equivalente a 80mm en puntos (1 pulgada = 72 puntos)
        // 1000 es una altura arbitraria, puedes ajustarla según tus necesidades

        return $pdf->stream('ticket.pdf');
    }
}
