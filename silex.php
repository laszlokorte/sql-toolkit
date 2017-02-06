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
use LaszloKorte\Resource\TableConverter;
use LaszloKorte\Resource\ParameterBag;
use LaszloKorte\Resource\Query\EntityQueryBuilder;

use LaszloKorte\Configurator\ConfigurationBuilder;
use LaszloKorte\Presenter\ApplicationBuilder;
use LaszloKorte\Presenter\Application;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Inflector\Inflector;

use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader,'loadClass']);

$jwtKey = "c303c6c7125d5e365ed7323f6143fb58";
$builder = new SchemaBuilder();


$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
		PDO::ATTR_TIMEOUT => 2,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	]);

$schemaCache = __DIR__ . '/cache/schema.txt';
if(true || !file_exists($schemaCache)) {
	$schemaDef = $builder->buildSchemaFor($connection, 'ishl')->getDef();
	file_put_contents($schemaCache, serialize($schemaDef));
}

$schemaDef = unserialize(file_get_contents($schemaCache));

$schema = new Schema($schemaDef);

$inflector = new Inflector();

$confBuilder = new ConfigurationBuilder();

$schemaConf = $confBuilder->buildConfigurationFor($schema);

$appBuilder = new ApplicationBuilder();

$appDef = $appBuilder->buildApplication($schemaConf);

$app = new Application($appDef);

$silex = new SilexApp();
$silex['debug'] = true;

$silex['helper.inflector'] = function() {
	return new Inflector();
};
$silex['converter.id'] = function() {
	return new IdConverter();
};
$silex['converter.table'] = function($silex) {
	return new TableConverter($silex['schema']);
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
$silex['schema'] = function($silex) {
	return $silex['builder.schema']->buildSchemaFor($silex['db.connection'], $silex['db.name']);
};

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

$silex->get('/table/{table}.{format}', function (SilexApp $silex, Request $request, Table $table, $format) {
	var_dump($format);
	$q = (new ParameterBag($_GET))
		->replace('table', 'users');
    return new Response('Hello' . $q);
})
->value('format', 'html')
->assert('format', '[a-z]+')
->convert('table', 'converter.table:convert')
->bind('table_list');

$silex->get('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
	var_dump($id);
    return 'Hello ';
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail');

$silex->get('/table/{table}/{id}/{child}.{format}', function (SilexApp $silex, Request $request, $table, $id, $child, $format) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail_export');

$silex->get('/table/{table}/new', function (SilexApp $silex, Request $request, $table) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->bind('table_new');

$silex->post('/table/{table}', function (SilexApp $silex, Request $request, $table) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->bind('table_create');

$silex->get('/table/{table}/{id}/edit', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_edit');

$silex->put('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_update');

$silex->get('/table/{table}/{id}/delete', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_delete');

$silex->delete('/table/{table}/{id}', function (SilexApp $silex, Request $request, $table, $id) {
    return 'Hello '.$silex->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_destroy');

$silex->get('/login', function (SilexApp $silex, Request $request) {
    return 'Hello';
});

$silex->post('/login', function (SilexApp $silex, Request $request) {
    return 'Hello';
});

$silex->get('/logout', function (SilexApp $silex, Request $request) {
    return 'Hello';
});

$silex->get('/', function (SilexApp $silex, Request $request) {
    return $silex['twig']->render('index.html.twig', array(
        'tables' => $silex['schema']->tables(),
    ));
});

$silex->error(function (\Exception $e, Request $request, $code) {
    return new Response('We are sorry, but something went terribly wrong.');
});

$silex->run();
