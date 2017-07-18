<?php 
date_default_timezone_set("Europe/Berlin");

ob_start();

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
use LaszloKorte\Resource\Scene\CollectionScene;
use LaszloKorte\Resource\Controllers\CollectionController;
use LaszloKorte\Resource\Controllers\FormController;
use LaszloKorte\Resource\Controllers\DeletionController;
use LaszloKorte\Resource\Controllers\DetailController;
use LaszloKorte\Resource\Controllers\UserProvider;
use LaszloKorte\Resource\Renderer\HtmlRenderer;
use LaszloKorte\Resource\Renderer\TextRenderer;
use LaszloKorte\Resource\Navigation\NavigationController;


use LaszloKorte\Configurator\ConfigurationBuilder;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphBuilder;
use LaszloKorte\Graph\Graph;
use LaszloKorte\Graph\Entity;

use LaszloKorte\Custom\BadgeExportController;
use LaszloKorte\Custom\InvoiceExportController;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Inflector\Inflector;

use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRImage;
use chillerlan\QRCode\Output\QRImageOptions;

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader,'loadClass']);

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

$silex['graphDefinition.cached.file'] = __DIR__ . '/cache/graph.txt';
$silex['graphDefinition.cached'] = function($silex) {
    $cacheFile = $silex['graphDefinition.cached.file'];
    if(file_exists($cacheFile)) {
        $def = unserialize(file_get_contents($cacheFile));
    } else {
        $def = $silex['graphDefinition.fresh'];
        file_put_contents($silex['graphDefinition.cached.file'], serialize($def));
    }
    return $def;
};

$silex['graphDefinition.fresh'] = function($silex) {
    return $silex['graphBuilder']->buildGraph($silex['schemaConf'], $silex['schema']);
};

$silex['graph'] = function($silex) {
    return new Graph($silex['graphDefinition.cached'] ?? $silex['graphDefinition.fresh']);
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

$silex->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
));

$silex->extend('twig', function($twig, $silex) {
    $twig->addFilter(new \Twig_SimpleFilter('pluralize', [
    	$silex['helper.inflector'],
    	'pluralize',
    ]));

    $twig->addFilter(new \Twig_SimpleFilter('titlelize', function($s) {
        return ucwords(str_replace('_', ' ', $s));
    }));

    $twig->addFilter(new \Twig_SimpleFilter('qrcode', function($data) {
        $outputOptions = new QRImageOptions;
        $outputOptions->type = QRCode::OUTPUT_IMAGE_PNG;
        $outputOptions->pixelSize = 10;
        $outputInterface = new QRImage($outputOptions);

        // invoke a fresh QRCode instance
        $qrcode = new QRCode($data, $outputInterface);

        // and dump the output
        return $qrcode->getRawData();
    }));

    return $twig;
});

$silex->get('/table/{entity}', function (SilexApp $silex, Request $request, $entity) {

    $scene = new CollectionScene($silex['db.connection'], $silex['graph'], new HtmlRenderer(), new IdConverter());

    return new Response(
        $silex['twig']->render(
            'collection.html.twig', 
            $scene->load(new Identifier($entity), new ParameterBag($_GET))
        ),
    200);
})
->assert('entity', '[a-z\_]+')
->bind('table_list');

$silex->get('/table/{entity}.{format}', function (SilexApp $silex, Request $request, Entity $entity, $format) {

    $contentTypes = [
        'csv' => 'text/plain',
        'xml' => 'text/xml',
        'json' => 'application/json',
    ];

    if(!array_key_exists($format, $contentTypes)) {
        throw new \Exception("Unexpected format");
    }

    return new Response($silex['twig']->render('collection.'.$format.'.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new CollectionController($silex['db.connection'], $entity, new ParameterBag($_GET), true),
        'templateRenderer' => new TextRenderer(),
    ]),
    200,
    [
        'Content-Type' => $contentTypes[$format],
        'Content-Disposition' => sprintf('attachment; filename="%s-export_%s.%s"', $entity->title(true), (new \DateTime())->format('Y-m-d_H_i'), $format),
    ]);
})
->assert('format', '[a-z]+')
->convert('entity', 'converter.entity:convert')
->bind('table_export');



$silex->get('/table/{entity}/new', function (SilexApp $silex, Request $request, $entity) {
    $navigationController = new NavigationController($silex['graph']);
    $navigation = $navigationController->getNavigation($entity->id(), null, null);

    return $silex['twig']->render('form.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new FormController(),
        'navigation' => $navigation,
    ]);
})
->convert('entity', 'converter.entity:convert')
->bind('table_new');

$silex->get('/table/{entity}/{id}.{format}', function (SilexApp $silex, Request $request, $entity, $id) {
	$navigationController = new NavigationController($silex['graph']);
    $navigation = $navigationController->getNavigation($entity->id(), null, null);

    return $silex['twig']->render('detail.html.twig', [
        'graph' => $silex['graph'],
        'id' => $id,
        'entity' => $entity,
        'navigation' => $navigation,
        'controller' => new DetailController($silex['db.connection'], $entity, $id, new ParameterBag($_GET)),
        'templateRenderer' => new HtmlRenderer(),
    ]);
})
->value('format', 'html')
->assert('format', '[a-z]+')
->convert('entity', 'converter.entity:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail');

$silex->get('/table/{table}/{id}/{child}.{format}', function (SilexApp $silex, Request $request, $table, $id, $child, $format) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail_export');

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

$silex->get('/table/{entity}/batch/{id}/edit', function (SilexApp $silex, Request $request, $entity, $id) {
    return $silex['twig']->render('batch_form.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'controller' => new BatchFormController(),
    ]);
})
->convert('entity', 'converter.entity:convert')
->convert('id', 'converter.id:convert')
->bind('table_batch_edit');

$silex->put('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_update');

$silex->get('/table/{entity}/{id}/delete', function (SilexApp $silex, Request $request, $entity, $id) {
    $navigationController = new NavigationController($silex['graph']);
    $navigation = $navigationController->getNavigation($entity->id(), null, null);

    return $silex['twig']->render('deletion.html.twig', [
        'graph' => $silex['graph'],
        'entity' => $entity,
        'navigation' => $navigation,
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

$silex->get('/password/{password}', function(SilexApp $silex, $password) {
    $token = $silex['security.token_storage']->getToken();
    $user = $token->getUser();
    $encoder = $silex['security.encoder_factory']->getEncoder($user);

    // compute the encoded password for foo
    $password = $encoder->encodePassword($password, $user->getSalt());

    return $password;
});

$silex->get('/', function (SilexApp $silex, Request $request) {
    $navigationController = new NavigationController($silex['graph']);
    $navigation = $navigationController->getNavigation(null, null, null);

    return $silex['twig']->render('index.html.twig', [
        'graph' => $silex['graph'],
        'params' => new ParameterBag($_GET),
        'navigation' => $navigation,
    ]);
})
->bind('root')
;

$silex->mount('/badges', new BadgeExportController());
$silex->mount('/invoice', new InvoiceExportController());

// $silex->error(function (\Exception $e, Request $request, $code) {
//     return new Response('We are sorry, but something went terribly wrong.');
// });

$silex->run();
