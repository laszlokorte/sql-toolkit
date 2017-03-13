<?php

namespace LaszloKorte\Custom;

use Silex\Application as SilexApp;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use DateTime;

class BadgeExportController implements ControllerProviderInterface
{

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

            $options = new Options();
            $options->setDefaultMediaType('print');
            $options->setDefaultFont('sans-serif');
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                $silex['twig']->render('custom/badge.html.twig', [
                    'registration' => $result,
                ])
            );

            $pdf->render();

            return new Response(
                $pdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('inline; filename=Badge-%s-%s-%s.pdf', $result->conf_name, $result->first_name, $result->last_name),

                ]
            );
        })
        ->bind('export_single_badge');

        $controllers->get('all/{conference}', function(SilexApp $silex, $conference) {
            $stmt = $silex['db.connection']->prepare('SELECT name FROM conference WHERE conference.id = :conference');
            $stmt->execute([
                ':conference' => $conference
            ]);
            $conf = $stmt->fetch();

            $stmt = $silex['db.connection']->prepare('SELECT registration.id as id, person.first_name AS first_name, person.last_name AS last_name FROM person, registration, ticket_offer, conference WHERE person.id = registration.person_id AND registration.ticket_offer_id = ticket_offer.id AND conference.id = ticket_offer.conference_id AND conference.id = :conference ORDER BY person.first_name ASC, person.last_name ASC');
            $stmt->execute([
                ':conference' => $conference
            ]);

            $options = new Options();
            $options->setDefaultMediaType('print');
            $options->setDefaultFont('sans-serif');
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                $silex['twig']->render('custom/badges.html.twig', [
                    'registrations' => $stmt->fetchAll(),
                    'conference' => $conf,
                    'date' => new DateTime(),
                ])
            );

            $pdf->render();

            return new Response(
                $pdf->output(),
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