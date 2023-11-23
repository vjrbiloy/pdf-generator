<?php

namespace App\MessageHandler;

use App\Message\GeneratePdf;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Dompdf\Dompdf;

#[AsMessageHandler]
class GeneratePdfHandler
{
    public function __invoke(GeneratePdf $generatePdf)
    {
		$dompdf = new Dompdf();
		$dompdf->loadHtml($generatePdf->getContent());
        $dompdf->render();

		$imageSrc = $generatePdf->getImageSource();
		$output = $dompdf->output();
		file_put_contents($imageSrc, $output);
    }
}