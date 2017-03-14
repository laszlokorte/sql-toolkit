<?php

namespace LaszloKorte\Custom;

use Silex\Application as SilexApp;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;
use DateTime;

class BadgeExportController implements ControllerProviderInterface
{

    public function connect(SilexApp $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('single/{id}.{format}', function(SilexApp $silex, $id, $foramt) {
            $stmt = $silex['db.connection']->prepare('SELECT conference.name as conf_name, registration.id, person.first_name, person.last_name FROM registration, person, conference, ticket_offer WHERE registration.person_id = person.id AND registration.ticket_offer_id
             = ticket_offer.id AND ticket_offer.conference_id = conference.id AND registration.id = :id');
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if(!($result = $stmt->fetch())) {
                throw new \Exception("Not foudnd");
            }

            if($format === 'html') {
                return $silex['twig']->render('custom/badge.html.twig', [
                    'registration' => $result,
                ]);
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
        ->value('format', 'pdf')
        ->assert('format', '(html|pdf)')
        ->bind('export_single_badge');

        $controllers->get('all/{conference}.{format}', function(SilexApp $silex, $conference, $format) {
            $stmt = $silex['db.connection']->prepare('SELECT name FROM conference WHERE conference.id = :conference');
            $stmt->execute([
                ':conference' => $conference
            ]);
            $conf = $stmt->fetch();

            $stmt = $silex['db.connection']->prepare('SELECT registration.id as id, person.first_name AS first_name, person.last_name AS last_name FROM person, registration, ticket_offer, conference WHERE person.id = registration.person_id AND registration.ticket_offer_id = ticket_offer.id AND conference.id = ticket_offer.conference_id AND conference.id = :conference ORDER BY person.first_name ASC, person.last_name ASC');
            $stmt->execute([
                ':conference' => $conference
            ]);



            if($format === 'html') {
                return $silex['twig']->render('custom/badges.html.twig', [
                    'registrations' => $stmt,
                    'conference' => $conf,
                    'date' => new DateTime(),
                ]);
            }

            $options = new Options();
            $options->setDefaultMediaType('print');
            $options->setDefaultFont('sans-serif');
            $pdf = new Dompdf($options);
            $pdf->loadHtml(
                $silex['twig']->render('custom/badges.html.twig', [
                    'registrations' => $stmt,
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
        ->value('format', 'pdf')
        ->assert('format', '(html|pdf)')
        ->bind('export_all_badges');

        return $controllers;
    }
}