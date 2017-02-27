<?php 
date_default_timezone_set("Europe/Berlin");

ob_start();

use \Firebase\JWT\JWT;

use LaszloKorte\Schema\Schema;
use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\DatabaseId;
use LaszloKorte\Schema\SchemaBuilder;
use LaszloKorte\Schema\ColumnType;
use LaszloKorte\Schema\ForeignKey;

use LaszloKorte\Resource\IdConverter;
use LaszloKorte\Resource\EntityConverter;
use LaszloKorte\Resource\TableConverter;
use LaszloKorte\Resource\ParameterBag;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Controllers\CollectionController;
use LaszloKorte\Resource\Controllers\FormController;
use LaszloKorte\Resource\Controllers\DeletionController;
use LaszloKorte\Resource\Controllers\DetailController;
use LaszloKorte\Resource\Controllers\UserProvider;

use LaszloKorte\Configurator\ConfigurationBuilder;
use LaszloKorte\Graph\GraphBuilder;
use LaszloKorte\Graph\Graph;
use LaszloKorte\Graph\Entity;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Inflector\Inflector;

use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require('pdf/fpdf.php');
require('pdf/qr.php');

$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader,'loadClass']);

$jwtKey = "c303c6c7125d5e365ed7323f6143fb58";

$silex = new SilexApp();
$silex['debug'] = true;

$silex['confBuilder'] = function() {
	return new ConfigurationBuilder();
};
$silex['graphBuilder.config'] = [

];
$silex['graphBuilder'] = function($silex) {
	return new GraphBuilder($silex['graphBuilder.config']);
};

$silex['schemaConf'] = function($silex) {
	return $silex['confBuilder']->buildConfigurationFor($silex['schema']);
};

$silex['graphDefinition'] = function($silex) {
	return $silex['graphBuilder']->buildGraph($silex['schemaConf']);
};

$silex['graph'] = function($silex) {
	return new Graph($silex['graphDefinition']);
};

$silex['helper.inflector'] = function() {
	return new Inflector();
};
$silex['converter.id'] = function() {
	return new IdConverter();
};
$silex['converter.table'] = function($silex) {
	return new TableConverter($silex['schema']);
};
$silex['converter.entity'] = function($silex) {
	return new EntityConverter($silex['graph']);
};
$silex['builder.schema'] = function() {
	return new SchemaBuilder();
};
$silex['db.name'] = 'ishl';
$silex['db.connection'] = function() {
	return new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
			PDO::ATTR_TIMEOUT => 2,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		]);
};

$silex['schemaDef.cached.file'] = __DIR__ . '/cache/schema.txt';
$silex['schemaDef.cached'] = function($silex) {
	if(file_exists($silex['schemaDef.cached.file'])) {
		$schemaDef = unserialize(file_get_contents($silex['schemaDef.cached.file']));
	} else {
		$schemaDef = $silex['schemaDef.fresh'];
		file_put_contents($silex['schemaDef.cached.file'], serialize($schemaDef));
	}
	return $schemaDef;
};

$silex['schemaDef.fresh'] = function($silex) {
	return $silex['builder.schema']->buildSchemaFor($silex['db.connection'], $silex['db.name'])->getDef();
};

$silex['schema'] = function($silex) {
	return new Schema($silex['schemaDef.cached'] ?? $silex['schemaDef.fresh']);
};

$silex->register(new Silex\Provider\SessionServiceProvider());
$silex->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'login' => [
            'pattern' => '^/login$',
        ],
        'secured' => [
            'pattern' => '^.*$',
            'form' => [
                'login_path' => '/login', 
                'check_path' => '/login_check',
            ],
            'logout' => [
                'logout_path' => '/logout'
            ],
            'users' => function () use ($silex) {
                return new UserProvider($silex['db.connection'], $silex['graph']);
            }
        ],
    ]
]);

use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

$silex->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$silex->extend('twig', function($twig, $silex) {
    $twig->addFilter(new \Twig_SimpleFilter('pluralize', [
    	$silex['helper.inflector'],
    	'pluralize',
    ]));

    $twig->addFilter(new \Twig_SimpleFilter('titlelize', function($s) {
    	return ucwords(str_replace('_', ' ', $s));
    }));

    return $twig;
});

$silex->get('/table/{entity}.{format}', function (SilexApp $silex, Request $request, Entity $entity, $format) {

    $contentTypes = [
        'html' => 'text/html',
        'csv' => 'text/plain',
        'xml' => 'text/xml',
        'json' => 'application/json',
    ];

	return new Response($silex['twig']->render('collection.'.$format.'.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new CollectionController($silex['db.connection'], $entity, new ParameterBag($_GET), null, $format !== 'html'),
    ]),
    200,
    [
        'Content-Type' => $contentTypes[$format]
    ]);
})
->value('format', 'html')
->assert('format', '[a-z]+')
->convert('entity', 'converter.entity:convert')
->bind('table_list');



$silex->get('/table/{entity}/new', function (SilexApp $silex, Request $request, $entity) {
    return $silex['twig']->render('form.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new FormController(),
    ]);
})
->convert('entity', 'converter.entity:convert')
->bind('table_new');

$silex->get('/table/{entity}/{id}', function (SilexApp $silex, Request $request, $entity, $id) {
	return $silex['twig']->render('detail.html.twig', [
        'graph' => $silex['graph'],
        'id' => $id,
        'entity' => $entity,
        'controller' => new DetailController($silex['db.connection'], $entity, $id, new ParameterBag($_GET)),
    ]);
})
->convert('entity', 'converter.entity:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail');

$silex->get('/table/{table}/{id}/{child}.{format}', function (SilexApp $silex, Request $request, $table, $id, $child, $format) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail_export');

$silex->get('export/badge/{id}', function(SilexApp $silex, Request $request, $id) {
    $stmt = $silex['db.connection']->prepare('SELECT conference.name as conf_name, registration.id, person.first_name, person.last_name FROM registration, person, conference, ticket_offer WHERE registration.person_id = person.id AND registration.ticket_offer_id
     = ticket_offer.id AND ticket_offer.conference_id = conference.id AND registration.id = :id');
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if(!($result = $stmt->fetch())) {
        throw new \Exception("Not foudnd");
    }

    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','',20);
    $pdf->setY(150);
    $pdf->Cell(95,120, sprintf('%s %s', $result->first_name, $result->last_name), 0, 0, 'C');
    $pdf->Cell(95,120, sprintf('%s %s', $result->first_name, $result->last_name), 0, 0, 'C');

    $qr = QRCode::getMinimumQRCode($result->id, QR_ERROR_CORRECT_LEVEL_L);
    

    $qrSize = $qr->getModuleCount();
    $cellSize = 40/$qrSize;
    for ($r = 0; $r < $qrSize; $r++) {
        for ($c = 0; $c < $qrSize; $c++) {
            if($qr->isDark($r, $c)) {
                $pdf->SetFillColor(0, 0, 0); 
            } else {
                $pdf->SetFillColor(255, 255, 255); 
            }
            $pdf->Rect(95/2-10 + $cellSize*$r, 230+$cellSize*$c, $cellSize, $cellSize, 'F');

            $pdf->Rect(95 + 95/2-10 + $cellSize*$r, 230+$cellSize*$c, $cellSize, $cellSize, 'F');
        }
    }

    $pdf->SetTitle(sprintf('Badge-%s-%s-%s', $result->conf_name, $result->first_name, $result->last_name));

    return new Response(
        $pdf->Output('S', 'Badge', true),
        200,
        ['Content-Type' => 'application/pdf']
    );
})
->bind('export_single_badge');

$silex->get('export/badges/{conference}', function(SilexApp $silex, Request $request, $conference) {
    $stmt = $silex['db.connection']->prepare('SELECT name FROM conference WHERE conference.id = :conference');
    $stmt->execute([
        ':conference' => $conference
    ]);
    $conf = $stmt->fetch();

    $stmt = $silex['db.connection']->prepare('SELECT registration.id as id, person.first_name AS first_name, person.last_name AS last_name FROM person, registration, ticket_offer, conference WHERE person.id = registration.id AND registration.ticket_offer_id = ticket_offer.id AND conference.id = ticket_offer.conference_id AND conference.id = :conference');
    $stmt->execute([
        ':conference' => $conference
    ]);

    $pdf = new FPDF('P','mm','A4');

    $pdf->AddPage();
    $pdf->SetFont('Arial','',20);
    $pdf->setY(100);
    $pdf->Cell(0,20, sprintf('%s Badges', $conf->name), 0, 2, 'C');
    $pdf->SetFont('Arial','',16);
    $pdf->Cell(0,0, sprintf('%s', (new DateTime())->format('d.m.Y H:i')), 0, 2, 'C');
    
    while($result = $stmt->fetch()) {
        $pdf->AddPage();
        $pdf->SetFont('Arial','',20);
        $pdf->setY(150);
        $pdf->Cell(95,120, sprintf('%s %s', $result->first_name, $result->last_name), 0, 0, 'C');
        $pdf->Cell(95,120, sprintf('%s %s', $result->first_name, $result->last_name), 0, 0, 'C');


        $qr = QRCode::getMinimumQRCode($result->id, QR_ERROR_CORRECT_LEVEL_L);
        

        $qrSize = $qr->getModuleCount();
        $cellSize = 40/$qrSize;
        for ($r = 0; $r < $qrSize; $r++) {
            for ($c = 0; $c < $qrSize; $c++) {
                if($qr->isDark($r, $c)) {
                    $pdf->SetFillColor(0, 0, 0); 
                } else {
                    $pdf->SetFillColor(255, 255, 255); 
                }
                $pdf->Rect(95/2-10 + $cellSize*$r, 230+$cellSize*$c, $cellSize, $cellSize, 'F');

                $pdf->Rect(95 + 95/2-10 + $cellSize*$r, 230+$cellSize*$c, $cellSize, $cellSize, 'F');
            }
        }
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

$silex->post('/table/{table}', function (SilexApp $silex, Request $request, $table) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->bind('table_create');

$silex->get('/table/{entity}/{id}/edit', function (SilexApp $silex, Request $request, $entity, $id) {
    return $silex['twig']->render('form.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new FormController(),
    ]);
})
->convert('entity', 'converter.entity:convert')
->convert('id', 'converter.id:convert')
->bind('table_edit');

$silex->put('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_update');

$silex->get('/table/{entity}/{id}/delete', function (SilexApp $silex, Request $request, $entity, $id) {
    return $silex['twig']->render('deletion.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new DeletionController(),
    ]);
})
->convert('entity', 'converter.entity:convert')
->convert('id', 'converter.id:convert')
->bind('table_delete');

$silex->delete('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_destroy');

$silex->get('/login', function (SilexApp $silex, Request $request) {
    return $silex['twig']->render('login.html.twig',[
        'error' => $silex['security.last_error']($request),
        'last_username' => $silex['session']->get('_security.last_username'),
    ]);
});

$silex->post('/login', function (SilexApp $silex, Request $request) {
    return 'Hello';
});

$silex->get('/logout', function (SilexApp $silex, Request $request) {
    return 'Hello';
});

$silex->get('/password/{password}', function(SilexApp $silex, $password) {
    $token = $silex['security.token_storage']->getToken();
    $user = $token->getUser();
    $encoder = $silex['security.encoder_factory']->getEncoder($user);

    // compute the encoded password for foo
    $password = $encoder->encodePassword($password, $user->getSalt());

    return $password;
});

$silex->get('/', function (SilexApp $silex, Request $request) {
    return $silex['twig']->render('index.html.twig', [
        'graph' => $silex['graph'],
    ]);
})
->bind('root')
;

// $silex->error(function (\Exception $e, Request $request, $code) {
//     return new Response('We are sorry, but something went terribly wrong.');
// });

$silex->run();
