<?php 

use LaszloKorte\Schema\Schema;
use LaszloKorte\Schema\DatabaseId;
use LaszloKorte\Schema\SchemaBuilder;
use LaszloKorte\Schema\ColumnType;

use LaszloKorte\Mapper\MapperDefinition;
use LaszloKorte\Mapper\Identifier;

require __DIR__ . '/vendor/autoload.php';

$builder = new SchemaBuilder();


$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	]);

$schema = $builder->buildSchemaFor($connection, 'ishl');

$mapper = new MapperDefinition();
$sessionDef = $mapper->defineType(new Identifier('session'));
$sessionDef->defineField(new Identifier('title'));
$sessionDef->defineField(new Identifier('description'));


$timeslotDef = $mapper->defineType(new Identifier('timeslot'));
$timeslotDef->defineField(new Identifier('start_time'));
$timeslotDef->defineField(new Identifier('end_time'));

$sessionDef->defineParentRelationship(new Identifier('timeslot'), new Identifier('timeslot'));
$timeslotDef->defineChildRelationship(new Identifier('sessions'), new Identifier('session'));