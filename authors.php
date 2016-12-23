<?php 

header('Content-Type: text/html; charset:utf-8');

use LaszloKorte\Schema\Schema;
use LaszloKorte\Schema\DatabaseId;
use LaszloKorte\Schema\SchemaBuilder;
use LaszloKorte\Schema\ColumnType;

require __DIR__ . '/vendor/autoload.php';

$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	]);

$stmt = $connection->prepare('SELECT id, authors FROM abstract');
$stmt->execute();

$abstracts = $stmt->fetchAll(PDO::FETCH_OBJ);

// $insert = $connection->prepare('INSERT INTO abstracts_author (abstract_id, first_name, last_name) VALUES (:abstract_id, :first_name, :last_name)');


foreach($abstracts AS $abstract) {
	echo "<h3>$abstract->id</h3>";
	$names = array_map('trim', explode(',',$abstract->authors));
	$analyzedNames = array_map(function($name) {
		$splitted = explode(' ', preg_replace('/\s/u',' ', $name), 2);

		if(!$splitted[1]) {
			echo "ERROR:".$splitted[0];
		}
		return (object)[
			'last' => $splitted[0],
			'rest' => $splitted[1],
		];
	}, $names);
	echo '<pre>';

	// foreach($analyzedNames as $n) {
	// 	echo 'insert';
	// 	if($n->last === '') {
	// 		continue;
	// 	}
	// 	var_dump($n);
	// 	$insert->execute([
	// 		':abstract_id' => $abstract->id,
	// 		':first_name' => $n->rest,
	// 		':last_name' => $n->last,
	// 	]);
	// }

	var_dump($analyzedNames);
}