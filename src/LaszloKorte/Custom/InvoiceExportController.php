<?php

namespace LaszloKorte\Custom;

use Silex\Application as SilexApp;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceExportController implements ControllerProviderInterface
{
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

            $options = new Options();
            $options->setDefaultMediaType('print');
            $options->setDefaultFont('sans-serif');
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                $silex['twig']->render('custom/invoice.html.twig', [
                    'invoice' => $data,
                    'basepath' => __DIR__ . '/../../../images/',
                ])
            );

            $pdf->render();

            return new Response(
                $pdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('inline; filename=Invoice.pdf'),

                ]
            );
        })
        ->bind('export_single_invoice');

        return $controllers;
    }
}