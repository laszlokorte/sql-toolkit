<?php

namespace LaszloKorte\Custom;

use Silex\Application as SilexApp;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../../../pdf/fpdf.php';
require_once __DIR__.'/../../../pdf/qr.php';

use FPDF;
use QRCode;
use DateTime;

class InvoiceExportController implements ControllerProviderInterface
{
    const A4_WIDTH = 210;
    const A4_HEIGHT = 297;
    const MARGIN_V = 15;
    const MARGIN_H = 10;

	private function createDocument() {
		return new FPDF('P','mm','A4');
	}

    private function printInvoice($pdf, $data) {
        $pdf->SetMargins(self::A4_WIDTH, self::A4_HEIGHT);
        $pdf->AddPage();
        $pdf->SetFont('Arial','',13);
        $pdf->setXY(self::MARGIN_H, self::MARGIN_V);
        $title = "Invoice ISHL 10";
        $length = $pdf->GetStringWidth( $title );
        $pdf->Cell( $length, 2, $title);

        $someText = (new DateTime($data->date))->format('d.m.Y');
        $length = $pdf->GetStringWidth( $someText );
        $pdf->setXY(self::A4_WIDTH - self::MARGIN_H - $length, self::MARGIN_V);
        $pdf->Cell( $length, 2, $someText, 0, 0, 'R');

        $someText = sprintf("Ticket for %s %s", $data->first_name, $data->last_name);
        $length = $pdf->GetStringWidth( $someText );
        $pdf->setXY(self::MARGIN_H, self::MARGIN_V + 30);
        $pdf->Cell($length, 2, $someText, 0, 0, 'L');

        $someText = sprintf("Price: %.2f €", $data->amount/100);
        $length = $pdf->GetStringWidth($someText);
        $pdf->setXY(self::MARGIN_H, self::MARGIN_V + 36);
        $pdf->Cell($length, 2, $someText, 0, 0, 'L');
    }

    public function connect(SilexApp $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('single/{id}', function(SilexApp $silex, $id) {
            $stmt = $silex['db.connection']->prepare('SELECT ticket_offer.price as amount, registration.registered_at as date, conference.name as conf_name, registration.id, person.first_name, person.last_name FROM registration, person, conference, ticket_offer WHERE registration.person_id = person.id AND registration.ticket_offer_id
             = ticket_offer.id AND ticket_offer.conference_id = conference.id AND registration.id = :id');
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if(!($data = $stmt->fetch())) {
                throw new \Exception("Not foudnd");
            }

            $pdf = $this->createDocument();

            $this->printInvoice($pdf, $data);

            $pdf->SetTitle(sprintf('Invoice-%s-%s-%s', $data->conf_name, $data->first_name, $data->last_name));

            return new Response(
                $pdf->Output('S', 'Invoice', true),
                200,
                ['Content-Type' => 'application/pdf']
            );
        })
        ->bind('export_single_invoice');

        return $controllers;
    }
}