<?php 

use LaszloKorte\Schema\Schema;
use LaszloKorte\Schema\DatabaseId;
use LaszloKorte\Schema\SchemaBuilder;
use LaszloKorte\Schema\ColumnType;

use LaszloKorte\Mapper\DataSource;
use LaszloKorte\Mapper\Query\Operator;
use LaszloKorte\Mapper\MapperDefinition;
use LaszloKorte\Mapper\Mapper;
use LaszloKorte\Mapper\Identifier;

require __DIR__ . '/vendor/autoload.php';

$builder = new SchemaBuilder();


$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	]);

$schema = $builder->buildSchemaFor($connection, 'ishl');

$mapperDef = new MapperDefinition();
$sessionDef = $mapperDef->defineType(new Identifier('session'));
$sessionDef->defineField(new Identifier('id'));
$sessionDef->defineField(new Identifier('title'));
$sessionDef->defineField(new Identifier('description'));
$sessionDef->definePrimaryKey([new Identifier('id')]);


$timeslotDef = $mapperDef->defineType(new Identifier('timeslot'));
$timeslotDef->defineField(new Identifier('id'));
$timeslotDef->defineField(new Identifier('start_time'));
$timeslotDef->defineField(new Identifier('end_time'));
$timeslotDef->definePrimaryKey([new Identifier('id')]);

$sessionTimeslotRel = $sessionDef->defineParentRelationship(new Identifier('timeslot'), new Identifier('timeslot'));
$timeslotSessionRel = $timeslotDef->defineChildRelationship(new Identifier('sessions'), new Identifier('session'));

$sessionTimeslotRel->setInverse(new Identifier('sessions'));
$timeslotSessionRel->setInverse(new Identifier('timeslot'));


$dataSource = new DataSource($connection);
$mapper = new Mapper($mapperDef, $dataSource);

$timeslot = $mapper->timeslot;
$startTime = $timeslot->start_time;
$endTime = $timeslot->end_time;

$result = $timeslot->find()
	->filter($startTime->eq('100')->or($endTime->eq('100')))
	->expand($startTime->eq(42))
	->orderBy($startTime->asc(), $endTime->desc())
	->take(20)
	->skip(10);