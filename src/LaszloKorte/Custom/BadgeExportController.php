<?php

namespace LaszloKorte\Custom;

use Silex\Application as SilexApp;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/../../../pdf/fpdf.php';
require_once __DIR__.'/../../../pdf/qr.php';

use PDF_Polygon;
use QRCode;
use DateTime;

class BadgeExportController implements ControllerProviderInterface
{
	private function createDocument() {
		return new PDF_Polygon('P','mm','A4');
	}

	private function printBadge($pdf, $registration) {
		$pdf->setMargins(0,0,0,0);
		$pdf->AddPage();
		$pdf->SetFont('Arial','',20);
		$pdf->setY(200);
		$pdf->SetAutoPageBreak(false);
		$pdf->Cell(105,30, sprintf('%s %s', $registration->first_name, $registration->last_name), 0, 0, 'C');
		$pdf->Cell(105,30, sprintf('%s %s', $registration->first_name, $registration->last_name), 0, 0, 'C');


		$qr = QRCode::getMinimumQRCode($registration->id, QR_ERROR_CORRECT_LEVEL_L);
		
		$this->printQR($pdf, $qr, 52.5, 250, 30);
		$this->printQR($pdf, $qr, 157.5, 250, 30);
	}

	private function printQR($pdf, $qr, $cx, $cy, $size) {
		$qrSize = $qr->getModuleCount();
		$cellSize = $size/$qrSize;
		for ($r = 0; $r < $qrSize; $r++) {
		    for ($c = 0; $c < $qrSize; $c++) {
		        if($qr->isDark($r, $c)) {
		            $pdf->SetFillColor(0, 0, 0); 
		        } else {
		            $pdf->SetFillColor(255, 255, 255); 
		        }
		        $pdf->Rect(
		        	$cx - $size/2 + $cellSize*$r, 
		        	$cy - $size/2 + $cellSize*$c, 
		        	$cellSize, $cellSize, 
		        	'F'
		        );
		    }
		}
	}

    public function connect(SilexApp $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('single/{id}', function(SilexApp $silex, $id) {
            $stmt = $silex['db.connection']->prepare('SELECT conference.name as conf_name, registration.id, person.first_name, person.last_name FROM registration, person, conference, ticket_offer WHERE registration.person_id = person.id AND registration.ticket_offer_id
             = ticket_offer.id AND ticket_offer.conference_id = conference.id AND registration.id = :id');
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if(!($result = $stmt->fetch())) {
                throw new \Exception("Not foudnd");
            }

            $pdf = $this->createDocument();

            $this->printBadge($pdf, $result);

            $pdf->SetTitle(sprintf('Badge-%s-%s-%s', $result->conf_name, $result->first_name, $result->last_name));

            return new Response(
                $pdf->Output('S', 'Badge', true),
                200,
                ['Content-Type' => 'application/pdf']
            );
        })
        ->bind('export_single_badge');

        $controllers->get('all/{conference}', function(SilexApp $silex, $conference) {
            $stmt = $silex['db.connection']->prepare('SELECT name FROM conference WHERE conference.id = :conference');
            $stmt->execute([
                ':conference' => $conference
            ]);
            $conf = $stmt->fetch();

            $stmt = $silex['db.connection']->prepare('SELECT registration.id as id, person.first_name AS first_name, person.last_name AS last_name FROM person, registration, ticket_offer, conference WHERE person.id = registration.person_id AND registration.ticket_offer_id = ticket_offer.id AND conference.id = ticket_offer.conference_id AND conference.id = :conference');
            $stmt->execute([
                ':conference' => $conference
            ]);

            $pdf = $this->createDocument();

            $pdf->AddPage();
            $pdf->SetFont('Arial','',20);
            $pdf->setY(100);
            $pdf->Cell(0,20, sprintf('%s Badges', $conf->name), 0, 2, 'C');
            $pdf->SetFont('Arial','',16);
            $pdf->Cell(0,0, sprintf('%s', (new DateTime())->format('d.m.Y H:i')), 0, 2, 'C');
            
            while($result = $stmt->fetch()) {
            	$this->printBadge($pdf, $result);
            }

            $pdf->SetTitle(sprintf('All-Badges-%s', $conf->name));

            return new Response(
                $pdf->Output('S', 'Badge', true),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('inline; filename=All-Badges-%s.pdf', $conf->name),
                ]
            );
        })
        ->bind('export_all_badges');

        return $controllers;
    }
}