<?php 
	date_default_timezone_set("Europe/Berlin");

	ob_start();
	//
	//
	//
	// !!!!!!!!!!!
	// CAUTION: This file is just a playground 
	// for testing the Inspector API
	//
	// It contains 
	// CROSS-SITE-SCRIPTING VULNERABILITIES
	//
	// DO NOT USE IN PRODUCTION
	// !!!!!!!!!!!
	//
	//
	// TODO: Extract all the features prototyped in this
	// file to their own api
	//
	//

	use \Firebase\JWT\JWT;

	use LaszloKorte\Schema\Schema;
	use LaszloKorte\Schema\Table;
	use LaszloKorte\Schema\DatabaseId;
	use LaszloKorte\Schema\SchemaBuilder;
	use LaszloKorte\Schema\ColumnType;
	use LaszloKorte\Schema\ForeignKey;

	use LaszloKorte\Resource\IdConverter;
	use LaszloKorte\Resource\TableConverter;
	use LaszloKorte\Query\ParameterBag;

	use LaszloKorte\Configurator\ConfigurationBuilder;
	use LaszloKorte\Presenter\ApplicationBuilder;

	use Doctrine\Common\Annotations\AnnotationRegistry;
	use Doctrine\Common\Inflector\Inflector;

	use Silex\Application;
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
if(!file_exists($schemaCache)) {
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

$app = new Application();

$app['helper.inflector'] = function() {
	return new Inflector();
};
$app['converter.id'] = function() {
	return new IdConverter();
};
$app['converter.table'] = function($app) {
	return new TableConverter($app['schema']);
};
$app['builder.schema'] = function() {
	return new SchemaBuilder();
};
$app['db.name'] = 'ishl';
$app['db.connection'] = function() {
	return new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
			PDO::ATTR_TIMEOUT => 2,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		]);
};
$app['schema'] = function($app) {
	return $app['builder.schema']->buildSchemaFor($app['db.connection'], $app['db.name']);
};

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->extend('twig', function($twig, $app) {
    $twig->addFilter(new \Twig_SimpleFilter('pluralize', [
    	$app['helper.inflector'],
    	'pluralize',
    ]));

    $twig->addFilter(new \Twig_SimpleFilter('titlelize', function($s) {
    	return ucwords(str_replace('_', ' ', $s));
    }));

    return $twig;
});

$app->get('/table/{table}.{format}', function (Application $app, Request $request, Table $table, $format) {
	var_dump($format);
	$q = (new ParameterBag($_GET))
		->replace('table', 'users');
    return new Response('Hello' . $q);
})
->value('format', 'html')
->assert('format', '[a-z]+')
->convert('table', 'converter.table:convert')
->bind('table_list');

$app->get('/table/{table}/{id}', function (Application $app, Request $request, $table, $id) {
	var_dump($id);
    return 'Hello ';
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail');

$app->get('/table/{table}/{id}/{child}.{format}', function (Application $app, Request $request, $table, $id, $child, $format) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_detail_export');

$app->get('/table/{table}/new', function (Application $app, Request $request, $table) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->bind('table_new');

$app->post('/table/{table}', function (Application $app, Request $request, $table) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->bind('table_create');

$app->get('/table/{table}/{id}/edit', function (Application $app, Request $request, $table, $id) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_edit');

$app->put('/table/{table}/{id}', function (Application $app, Request $request, $table, $id) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_update');

$app->get('/table/{table}/{id}/delete', function (Application $app, Request $request, $table, $id) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_delete');

$app->delete('/table/{table}/{id}', function (Application $app, Request $request, $table, $id) {
    return 'Hello '.$app->escape($table);
})
->convert('table', 'converter.table:convert')
->convert('id', 'converter.id:convert')
->bind('table_destroy');

$app->get('/login', function (Application $app, Request $request) {
    return 'Hello';
});

$app->post('/login', function (Application $app, Request $request) {
    return 'Hello';
});

$app->get('/logout', function (Application $app, Request $request) {
    return 'Hello';
});


$app->get('/', function (Application $app, Request $request) {
    return $app['twig']->render('index.html.twig', array(
        'tables' => $app['schema']->tables(),
    ));
});

$app->error(function (\Exception $e, Request $request, $code) {
    return new Response('We are sorry, but something went terribly wrong.');
});

// $app->run();
// exit(0);

$queryBag = new ParameterBag($_GET);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>DB Manager</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<?php 

if(array_key_exists('logout', $_GET)) {
	$isLoggedIn = true;
	unset($_COOKIE['auth']); 
	setcookie('auth', NULL, -1); 
	ob_clean();
	header("Location: /?login");
	exit(0);
}

try {
	$token = isset($_COOKIE['auth']) ? JWT::decode($_COOKIE['auth'], $jwtKey, ['HS256']) : NULL;
	$isLoggedIn = !empty($token);
} catch (Exception $e) {
	var_dump($e);
	exit(0);
	$isLoggedIn = false;
}

if(array_key_exists('login', $_GET) && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = $_POST['login']['email'];
	$password = $_POST['login']['password'];
	$stmt = $connection->prepare('SELECT password FROM account WHERE email = :email');
	$stmt->execute([
		':email' => $email,
	]);
	$actualPassword = $stmt->fetchColumn(0);
	if(password_verify($password, $actualPassword)) {
		$isLoggedIn = true;
		setcookie('auth', JWT::encode([
			'user' => 'laszlo',
		], $jwtKey));
		ob_clean();
		header("Location: /");
		exit(0);
	} else {
		$loginError = 'Invalid Login';
	}
}

if(!$isLoggedIn || array_key_exists('login', $_GET)) {
	echo "<div class='login-panel-outer'>";
	echo "<div class=login-panel>";
		echo "<h1 class=login-title>Login</h1>";
		echo "<form action='?login' method=post>";

		if(isset($loginError)) {
			echo "<div class=login-error>";
			echo $loginError;
			echo "</div>";
		}

		echo "<dl class='prop-list'>";
		echo "<dt>E-Mail</dt>";
		$emailValue = isset($email) ? $email : '';
		echo "<dd><input name=login[email] type=text value='$emailValue' /></dd>";
		echo "<dt>Password</dt>";
		echo "<dd><input name=login[password] type=password /></dd>";
		echo "</dl>";

		echo "<button>Sign in</button>";
		if($isLoggedIn) {
			echo "<a href=?>Back</a>";
		}

		echo "</form>";
	echo "</div>";
	echo "</div>";
} else {

	echo "<div class='menu'>";

	echo "<h1 class=app-title><a href='/'>DB Manager</a></h1>";
	echo "<a href=?logout rel=nofollow>Logout</a> | ";
	echo "<label><input type=checkbox onChange=\"document.body.classList.toggle('debug', this.checked)\"/> Debug</label>";
	echo "<hr>";
	$tableCategories = [];
	$tableCategoryPriorities = [];
	$tablePriorities = [];
	$uncategorized = [];
	foreach ($schema->tables() as $table) {
		// if(isJoinTable($table)) {
		// 	continue;
		// }
		$comment = $table->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('NavGroup', $attributes)) {
			$groupName = $attributes['NavGroup'];
			if(!isset($tableCategories[$groupName])) {
				$tableCategories[$groupName] = [];
			}
			$tableCategories[$groupName][] = $table;

			if(!isset($tableCategoryPriorities[$groupName])) {
				$tableCategoryPriorities[$groupName] = 0;
			}
			if(array_key_exists('Priority', $attributes)) {
				$tableCategoryPriorities[$groupName] += (int)$attributes['Priority'];
				$tablePriorities[(string)$table->getName()] = (int)$attributes['Priority'];
			} else {
				$tablePriorities[(string)$table->getName()] = 0;
			}
		} else {
			$uncategorized []= $table;
		}
	}
	uksort($tableCategories, function($a,$b) use ($tableCategoryPriorities) {
		if($tableCategoryPriorities[$a] == $tableCategoryPriorities[$b]) {
			return strcasecmp($a, $b);
		}

		return $tableCategoryPriorities[$a] < $tableCategoryPriorities[$b] ? 1 : -1;
	});

	foreach ($tableCategories as $cat => $tables) {
		usort($tables, function($a,$b) use ($tablePriorities) {
			$aName = (string)$a->getName();
			$bName = (string)$b->getName();
			$aJ = isJoinTable($a);
			$bJ = isJoinTable($b);

			if($aJ == $bJ) {
				return strcasecmp($aName, $bName);
			}

			return $aJ ? 1 : -1;
		});

		$catTitle = ucwords(str_replace('_', ' ', $cat));

		echo "<h2 class=nav-group-title>$catTitle</h2>";
		echo "<ul class=nav-list>";
		foreach ($tables as $t) {
			$tableTitle = ucwords(str_replace('_', ' ', $t->getName()));

			$currentTable = $t->getName() == ($_GET['table']??null) ? 'state-active' : '';
			$jtPrefix = isJoinTable($t) ? '_': '';
			echo "<li><a class='nav-table $currentTable' href='?table={$t->getName()}'>{$jtPrefix}{$inflector->pluralize($tableTitle)}</a></li>";
		}
		echo "</ul>";
	}
	if(count($tableCategories) > 0 && count($uncategorized) > 0) {
		echo "<h2 class=nav-group-title>Others</h2>";
	}
	echo "<ul class=nav-list>";
	foreach ($uncategorized as $t) {
		$tableTitle = ucwords(str_replace('_', ' ', $t->getName()));
		$currentTable = $t->getName() == $_GET['table'] ? 'state-active' : '';
		echo "<li><a class='nav-table $currentTable' href='?table={$t->getName()}'>{$inflector->pluralize($tableTitle)}</a></li>";
	}
	echo "</ul>";

	echo "</div>";

	echo "<div class='content'>";
	if (!isset($_GET['table'])) {
		echo "Select some data set on the left.";
	} elseif (!isset($_GET['id'])) {
		$tableName = $_GET['table'];
		$tableTitle = ucwords(str_replace('_', ' ', $tableName));
		$table = $schema->table($tableName);

		if(!isset($_GET['action'])) {
			
			echo "<h2>{$inflector->pluralize($tableTitle)}</h2>";

			echo "<div class='debug'>";

			echo "<pre>";

			echo "<h3>Comment</h3>";

			echo "<p>";

			echo $table->getComment();

			echo "</p>";

			echo "<h3>Primary Key</h3>";

			if($table->hasPrimaryKeys()) {
				echo "<ul>";
				foreach ($table->primaryKeys() as $index) {
					echo "<li>{$index}</li>";
				}
				echo "</ul>";
			} else {
				echo "<p>None</p>";
			}

			echo "<h3>Indices</h3>";
			echo "<ul>";
			foreach ($table->indices() as $index) {
				echo "<li>{$index}</li>";
			}
			echo "</ul>";
			echo "<h3>ForeignKeys</h3>";
			echo "<ul>";
			foreach ($table->foreignKeys() as $assoc) {
				echo "<li>{$assoc}</li>";
			}
			echo "</ul>";
			echo "<ul>";
			foreach ($table->reverseForeignKeys() as $assoc) {
				echo "<li>{$assoc}</li>";
			}
			echo "</ul>";

			$columns = $table->columns();

			echo "<h3>Columns</h3>";
			echo "<ul>";
			foreach ($columns as $column) {
				echo "<li>{$column} [{$column->getType()}] 
				 ".($column->isNullable() ? '' : 'NOT NULL')." // {$column->getComment()}</li>";
			}
			echo "</ul>";
			echo "</div>";
			$orderCol = $queryBag[(string)$table->getName()]['order']['col'] ?? null;
			$orderCount = $queryBag[(string)$table->getName()]['order']['count'] ?? null;
			$orderRef = $queryBag[(string)$table->getName()]['order']['ref'] ?? null;
			$orderDir = $queryBag[(string)$table->getName()]['order']['dir'] ?? null;
			$sql = buildTableQuery($table, false, true, $orderCol ?? $orderCount ?? $orderRef, $orderDir);
			$stmt = $connection->prepare($sql);

			$limit = isset($_GET['export']) ? 1000000 : 20;
			$page = (int)(($_GET[(string)$tableName]['page']) ?? 0);
			$stmt->bindValue(':offset', $page * $limit, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
			echo "<div class='sql debug'>$sql</div>";
			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_OBJ);

			renderTable($table, $data, $page, $queryBag, null);
		} elseif($_GET['action'] === 'add') {
			$copy = isset($_GET['template']);
			if($copy) {
				echo "<h2>New $tableTitle by duplicating XXX</h2>";
			} else {
				echo "<h2>New $tableTitle</h2>";
			}
			echo "<form method='post'>";
			
			renderForm($table, $connection, [$tableName]);

			echo "<button>Create $tableTitle</button>";
			echo " | <a href='?table=$tableName'>Cancel</a>";
			echo "</form>";

		}
	} else {
		$tableName = $_GET['table'];
		$tableTitle = ucwords(str_replace('_', ' ',$tableName));
		$table = $schema->table($tableName);

		$id = getIdFromQuery($table, $_GET['id']);
		echo "<a href='?table=$tableName'>{$inflector->pluralize($tableTitle)}</a>";

		$sql = buildTableQuery($table, true);
		$stmt = $connection->prepare($sql);

		foreach($table->primaryKeys() AS $pk) {
			$stmt->bindValue(':'.$pk->getName(), $id[(string)$pk->getName()]);
		}
		$stmt->bindValue(':limit', 1, PDO::PARAM_INT);
		$stmt->bindValue(':offset', 0, PDO::PARAM_INT);
		$stmt->execute();
		echo "<div class='sql debug'>$sql</div>";

		$data = $stmt->fetch(PDO::FETCH_OBJ);

		if(!isset($_GET['action'])) {
			

			echo "<h2>";
			echo $tableTitle;
			echo ": ";
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('Link', $attributes)) {
					$anyLink = TRUE;
					echo $data->{$tableName.'_'.$col->getName()};
					echo " ";
				}
			}
			if(!$anyLink) {
				echo implode(',', array_map(function($c) use ($tableName, $data) {
					$key = $tableName . '_' . $c->getName();
					return $data->$key;
				}, iterator_to_array($table->primaryKeys())));
			}
			$idQ = buildIdQuery($table, $data);
			$idQTemplate = buildIdQuery($table, $data, NULL, 'template');
			echo "[ ";
			echo "<a href='?{$idQ->replace('action','edit')}'>&#x270E;</a>";
			echo " ";
			echo "<a href='?{$idQ->replace('action','delete')}'>&#x2715;</a>";
			echo " ";
			echo "<a href='?{$idQTemplate->replace('action','add')}'>&#x2398;</a>";
			echo " ]";
			echo "</h2>";



			echo "<dl class='prop-list'>";
			foreach ($table->columns(false) as $col) {
				echo "<dt>";
				$colTitle = ucwords(str_replace('_', ' ', $col->getName()));
				echo $colTitle;
				if($col->belongsToPrimaryKey()) {
					echo "*";
				}
				if($col->isSerialColumn()) {
					echo "[S]";
				}
				echo "</dt>";
				echo "<dd>";
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				$val = $data->{$tableName . '_'. $col->getName()};
				if(array_key_exists('Secret', $attributes)) {
					$val = '******';
				}
				if(array_key_exists('Hyper', $attributes)) {
					if($attributes['Hyper'] === 'email') {
						echo " <a class='hyper' href='mailto:$val'>💬</a> ";
					} elseif($attributes['Hyper'] === 'country') {
						$lower = strtolower($val);
							echo "<img width='50' src='https://lipis.github.io/flag-icon-css/flags/4x3/$lower.svg' alt='$val' /> ";
					}
				}
				switch($attributes['Display'] ?? null) {
					case 'boolean':
						echo $val === '1' ? '<input type="checkbox" checked disabled>' : '<input type="checkbox" disabled>';
						break;
					case 'url':
						echo is_null($val) ? '<span class="empty">-</span>' : "<a href='$val'>$val</a>";
						break;
						
					case 'currency", unit="euro':
						echo (is_null($val) || 0) ? '<span class="empty">-</span>' : "$val,00 €";
						break;
					case 'pre':
						echo "<div class='whitespaced'>$val</div>";
						break;
					case 'json':
						$parsed = json_decode($val);
						$pretty = json_encode($parsed, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
						echo "<div class='json'>$pretty</div>";
						break;

					case 'color':
						echo "<span style='background-color: $val;border:5px solid $val;' class=color>$val</span>";
						break;
					default:
						echo is_null($val) ? '<span class="empty">-</span>' : $val;
				}
				echo "</dd>";
			}
			foreach ($table->foreignKeys() as $fk) {
				echo "<dt>";
				$colTitle = ucwords(str_replace('_', ' ', 
					preg_replace('/^fk_.+__/i', '', $fk->getName())
				));
				echo $colTitle;
				echo "</dt>";
				echo "<dd>";
				
				$foreignTable = $fk->getTargetTable();
				$targetName = $fk->getName();

				if ($foreignTable->hasPrimaryKeys() && fkIsset($foreignTable, $data, $targetName)) {
					$idQ = buildIdQuery($foreignTable, $data, $targetName);

					echo "<a href='?$idQ'>";
				
					$tableComment = $foreignTable->getComment();
					$tableAttributes = parseColumnAttributes($tableComment);

					if(array_key_exists('Display', $tableAttributes)) {
						$template = $tableAttributes['Display'];

						echo preg_replace_callback('~\{(?<col>[^\}]+)\}~i', function($m) use ($data, $targetName) {
							$idx = $targetName . '_' . $m['col'];
							return $data->$idx ?: '';
						}, $template);
					} else {
						echo implode(',', array_map(function($c) use ($targetName, $data) {
							$key = $targetName . '_' . $c->getName();
							return $data->$key;
						}, iterator_to_array($foreignTable->primaryKeys())));
					}

					echo "</a>";
				} else {
					echo "<span class='empty'>-</span>";
				}
				
				echo "</dd>";
			}
			echo "</dl>";

			foreach ($table->reverseForeignKeys() as $assoc) {
				$assocId = $inflector->pluralize(preg_replace('/^fk_(.+)__.+$/i', '$1', $assoc->getName()));
				echo "<div class=child-records id='$assocId'>";
				$sourceTable = $assoc->getOwnTable();
				$sourceTitle = ucwords(str_replace('_', ' ', $sourceTable->getName()));
				echo "<h3>{$inflector->pluralize($sourceTitle)}</h3>";

				if(false && isJoinTable($sourceTable)) {
					continue;
				} else {
					$orderCol = $queryBag[(string)$sourceTable->getName()]['order']['col'] ?? null;
					$orderCount = $queryBag[(string)$sourceTable->getName()]['order']['count'] ?? null;
					$orderRef = $queryBag[(string)$sourceTable->getName()]['order']['ref'] ?? null;
					$orderDir = $queryBag[(string)$sourceTable->getName()]['order']['dir'] ?? null;
					$sql = 
						buildTableQuery($sourceTable, $assoc, true, $orderCol ?? $orderCount ?? $orderRef, $orderDir);
					$stmt = $connection->prepare($sql);
				}


				$limit = isset($_GET['export']) ? 1000000 : 20;
				$page =  (int)(($_GET[(string)$sourceTable->getName()]['page']) ?? 0);
				$stmt->bindValue(':offset', $page * $limit, PDO::PARAM_INT);
				$stmt->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
				foreach($assoc->getForeignColumns() AS $cix => $c) {
					$stmt->bindValue(':'.$assoc->getName().'_'.$c->getName(), $data->{$tableName.'_'.$c->getName()});
				}
				echo "<div class='sql debug'>$sql</div>";
				$stmt->execute();

				$childData = $stmt->fetchAll(PDO::FETCH_OBJ);

				renderTable($sourceTable, $childData, $page, $queryBag, $assoc);
				echo "</div>";
			}
		} elseif($_GET['action'] === 'edit') {
			$name = '';
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('Link', $attributes)) {
					$anyLink = TRUE;
					$name .= $data->{$tableName.'_'.$col->getName()};
					$name .= " ";
				}
			}
			if(!$anyLink) {
				$name = implode(',', array_map(function($c) use ($tableName, $data) {
					$key = $tableName . '_' . $c->getName();
					return $data->$key;
				}, iterator_to_array($table->primaryKeys())));
			}
			$idQ = buildIdQuery($table, $data);

			
			echo "<h2>";
			echo "Edit ";
			echo $tableTitle;
			echo ": ";
			echo $name;
			echo "</h2>";

			renderForm($table, $connection, [$tableName]);

			echo "<p>";
			echo "<button>Save</button>";
			echo " | <a href='?$idQ'>Cancel</a>";
			echo "</p>";
		} elseif($_GET['action'] === 'delete') {
			$name = '';
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('Link', $attributes)) {
					$anyLink = TRUE;
					$name .= $data->{$tableName.'_'.$col->getName()};
					$name .= " ";
				}
			}
			if(!$anyLink) {
				$name = implode(',', array_map(function($c) use ($tableName, $data) {
					$key = $tableName . '_' . $c->getName();
					return $data->$key;
				}, $table->primaryKeys()));
			}
			$idQ = buildIdQuery($table, $data);
			
			echo "<h2>";
			echo "Delete ";
			echo $tableTitle;
			echo ": ";
			echo $name;
			echo "</h2>";

			echo "<p>";
			echo "Are your sure you want to delete this $tableTitle?";
			echo "</p>";

			echo "<p>";
			echo "The following associated records will also be deleted:";
			echo "</p>";

			echo "<ul>";
			foreach ($table->reverseForeignKeys() as $assoc) {
				$sourceTable = $assoc->getOwnTable();
				if(isJoinTable($sourceTable)) {
					continue;
				}
				$sourceTitle = ucwords(str_replace('_', ' ', $sourceTable->getName()));
				echo "<li>$sourceTitle</li>";
			}
			echo "</ul>";

			echo "<p>";
			echo "The following references will be unlinked:";
			echo "</p>";

			echo "<ul>";
			foreach ($table->reverseForeignKeys() as $assoc) {
				$sourceTable = $assoc->getOwnTable();
				if(!isJoinTable($sourceTable)) {
					continue;
				}
				$sourceTitle = ucwords(str_replace('_', ' ', $sourceTable->getName()));
				echo "<li>$sourceTitle</li>";
			}
			echo "</ul>";

			
			echo "<p>";
			echo "<button>Confirm</button>";
			echo " | <a href='?$idQ'>Cancel</a>";
			echo "</p>";
		}
	}

	echo '</div>';
}
?>

<?php
function renderTable($table, $data, $page, $baseQuery, $parentFK = NULL) {
	global $inflector;
	$columns = $table->columns(false);
	$foreignKeys = array_filter(iterator_to_array($table->foreignKeys()), function($fk) use ($parentFK) {
		return $fk != $parentFK;
	});
	$exportName = is_null($parentFK) ? null : (string)$parentFK->getName();
	$reverseForeignKeys = $table->reverseForeignKeys();
	$tableName = $table->getName();
	$tableTitle = ucwords(str_replace('_', ' ', $tableName));

	$hash = is_null($parentFK) ? '' : '#' . $inflector->pluralize(preg_replace('/^fk_(.+)__.+$/i', '$1', $parentFK->getName()));

	$addUrl = (new ParameterBag([
		'table' => $tableName,
		'action' => 'add'
	]));

	if($parentFK !== NULL) {
		$addUrl = $addUrl
			->replace('parent', $parentFK->getName())
			->replace('parent_id', $baseQuery['id'])
		;
	}
	echo "<div><a href='?$addUrl'>+ New $tableTitle</a></div>";

	if ((isset($baseQuery['export']['data']) && $baseQuery['export']['data'] === $exportName) || isset($baseQuery['export']) && $parentFK === NULL) {
		ob_clean();

		$parentIdName = is_null($parentFK) ? 'all' : $parentFK->getTargetTable()->getName();
		$fileName = sprintf('export-%s-%s-%s', $parentIdName, $inflector->pluralize((string)$tableName), date('Y-m-d_H-i'));
		switch($baseQuery['export']['format']) {
			case 'xml':
				header('Content-type: text/xml; charset=utf-8');
				$extension = 'xml';
				echo '<?xml version="1.0" ?><data></data>';
				break;
			case 'csv':
				header('Content-type: text/csv');
				$extension = 'csv';
				renderCSV($table, $data);
				break;
			case 'json':
				header('Content-type: application/json');
				$extension = 'json';
				renderJSON($table, $data);
				break;
		}
		header('Content-Disposition: attachment; filename="'.$fileName.'.'.$extension.'"');
		ob_end_flush();
		exit(0);
	} 

	echo "<div>";
	echo "Export: ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'xml')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>XML</a>";
	echo " | ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'json')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>JSON</a>";
	echo " | ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'csv')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>CSV</a>";
	echo "</div>";

	if (!empty($data) && ($page > 0 || count($data) > 20)) {
		if ($page > 0) {
			$prevPage = $page - 1;

			echo "<a href='?{$baseQuery->replace([$tableName,'page'], $prevPage)}{$hash}'>Back</a>";
		} else {
			echo "<span class='disabled'>Back</span>";
		}
		echo " | ";
		if (count($data) > 20) {
			$nextPage = $page + 1;
			echo "<a href='?{$baseQuery->replace([$tableName,'page'], $nextPage)}{$hash}'>Next</a>";
		} else {
			echo "<span class='disabled'>Next</span>";
		}
	}


	echo "<div class=searchbox><form method=get action='?$hash'>";
	$hiddenParams = explode('&', (string)$baseQuery->remove([$exportName, 'search']));
	foreach($hiddenParams AS $hp) {
		$split = explode('=', $hp, 2);
		$n = $split[0];
		$v = $split[1];
		echo "<input type='hidden' name='$n' value='$v' />";
	}
	$currentValue = $baseQuery[$exportName]['search'] ?? '';
	echo "<input placeholder='term...' value='$currentValue' type='search' name='{$exportName}[search]' /><button>Search</button></form></div>";
	

	echo "<table>";
	echo "<thead>";
	echo "<tr>";
	$colCount = 0;
	foreach ($columns as $cidx => $col) {
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('HideInList', $attributes)) {
			continue;
		}
		$colCount++;
		echo "<th>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['col']??null) == (string)$col->getName()) {
			
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','count'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','col'], $col->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])}{$hash}'>";
		} else {
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','count'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','col'], $col->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])}{$hash}'>";
		}
		if($col->belongsToPrimaryKey()) {
			echo "*";
		}
		if($col->isSerialColumn()) {
			echo "[S]";
		}
		echo ucwords(str_replace('_', ' ', $col->getName()));
		echo "</a> ";
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','count'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','col'], $col->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['col']??null) == (string)$col->getName()) {
			echo "▲";
		} else {
			echo "△";
		}
		echo "</a>";
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','count'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','col'], $col->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'desc' && ($_GET[(string)$tableName]['order']['col']??null) == (string)$col->getName()) {
			echo "▼";
		} else {
			echo "▽";
		}
		echo "</a>";
		echo "</th>";
	}
	foreach ($foreignKeys as $fkidx => $fk) {
		$colCount++;
		echo "<th>";
		
		$targetTable = $fk->getTargetTable();

		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['ref']??null) == (string)$fk->getName()) {
			
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','count'])
			->replace([$tableName,'order','ref'], $fk->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])}{$hash}'>";
		} else {
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','count'])
			->replace([$tableName,'order','ref'], $fk->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])}{$hash}'>";
		}

		echo ucwords(str_replace('_', ' ', 
			preg_replace('/^fk_.+__/i', '', $fk->getName())
		));

		echo "</a> ";
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','ref'])
			->remove([$tableName,'order','count'])
			->replace([$tableName,'order','ref'], $fk->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['ref']??null) == (string)$fk->getName()) {
			echo "▲";
		} else {
			echo "△";
		}
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','count'])
			->replace([$tableName,'order','ref'], $fk->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'desc' && ($_GET[(string)$tableName]['order']['ref']??null) == (string)$fk->getName()) {
			echo "▼";
		} else {
			echo "▽";
		}
		echo "</a>";
		
		echo "</th>";
	}
	foreach ($reverseForeignKeys as $rfkidx => $rfk) {
		$colCount++;
		echo "<th>";
		
		$sourceTable = $rfk->getOwnTable();

		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['count']??null) == (string)$rfk->getName()) {
			
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','count'], $rfk->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])}{$hash}'>";
		} else {
			echo " <a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','count'], $rfk->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])}{$hash}'>";
		}

		echo $inflector->pluralize(ucwords(str_replace('_', ' ', 
			$sourceTable->getName()
		)));

		echo "</a> ";
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','count'], $rfk->getName())
			->replace([$tableName,'order','dir'], 'asc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'asc' && ($_GET[(string)$tableName]['order']['count']??null) == (string)$rfk->getName()) {
			echo "▲";
		} else {
			echo "△";
		}
		echo "</a>";
		echo "<a href='?{$baseQuery
			->remove([$tableName,'order','col'])
			->remove([$tableName,'order','ref'])
			->replace([$tableName,'order','count'], $rfk->getName())
			->replace([$tableName,'order','dir'], 'desc')
			->remove([$tableName,'page'])
		}{$hash}' class=sort>";
		if(($_GET[(string)$tableName]['order']['dir']??null) == 'desc' && ($_GET[(string)$tableName]['order']['count']??null) == (string)$rfk->getName()) {
			echo "▼";
		} else {
			echo "▽";
		}
		echo "</a>";
		
		echo "</th>";
	}
	if($table->hasPrimaryKeys()) {
		$colCount += 2;
		echo "<th>Actions</th>";
		echo "<th class=fill-label><label><input type=checkbox data-select='all'></label></th>";
	}
	echo "</tr>";
	echo "</thead>";

	echo "<tfoot>";
	echo "<tr>";
	foreach ($columns as $cidx => $col) {
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('HideInList', $attributes)) {
			continue;
		}
		echo "<td>";
		echo "";
		echo "</td>";
	}

	foreach ($foreignKeys as $fkidx => $fk) {
		echo "<td>";
		echo "</td>";
	}
	foreach ($reverseForeignKeys as $rfkidx => $rfk) {
		echo "<td>";
		echo "</td>";
	}
	if($table->hasPrimaryKeys()) {
		echo "<td></td>";
		echo "<td>";
		echo "<select><option /><option>Delete</option></select>";
		echo "</td>";
	}
	echo "</tr>";
	echo "</tfoot>";

	echo "<tbody>";
	$c = 0;
	if (empty($data)) {
		echo "<tr>";
		echo "<td colspan='{$colCount}'>";
		echo "<center class='no-data'>No Data</center>";
		echo "</td>";
		echo "</tr>";
	}
	foreach ($data as $ridx => $row) {
		if(++$c > 20) {
			break;
		}
		echo "<tr>";
			foreach ($columns as $ciidx => $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('HideInList', $attributes)) {
					continue;
				}
				echo "<td>";
				
				$val = $row->{$table->getName().'_'.$col->getName()};
				$isLink = array_key_exists('Link', $attributes) && !is_null($val) || $col->belongsToPrimaryKey();
				$isPassword = array_key_exists('Secret', $attributes);

				if($isPassword) {
					$val = '*****';
				}

				if(array_key_exists('Hyper', $attributes)) {
					if($attributes['Hyper'] === 'email') {
						echo " <a class='hyper' href='mailto:$val'>💬</a> ";
					} elseif($attributes['Hyper'] === 'country') {
						$lower = strtolower($val);
						echo "<img width='50' src='https://lipis.github.io/flag-icon-css/flags/4x3/$lower.svg' alt='$val' /> ";
					}
				}
				

				if ($isLink) {
					$idQ = buildIdQuery($table, $row);
					echo "<a href='?$idQ'>";
				}

				switch($attributes['Display'] ?? null) {
					case 'boolean':
						echo $val === '1' ? '<input type="checkbox" checked disabled>' : '<input type="checkbox" disabled>';
						break;
					
					case 'url':
						echo is_null($val) ? '<span class="empty">-</span>' : "<a href='$val'>$val</a>";
						break;
					
					case 'currency", unit="euro':
						echo (is_null($val) || 0) ? '<span class="empty">-</span>' : "$val,00 €";
						break;
					case 'color':
						echo "<span style='background-color: $val;border:5px solid $val;' class=color>$val</span>";
						break;
					case 'json':
						$trimmed = substr($val, 0, 50);
						$more = strlen($val) >= 50 ? '...' : '';
						echo "<span class='json-small'>$trimmed$more</span>";
						break;
					case 'flag':
						$lower = strtolower($val);
						echo "<img width='50' src='https://lipis.github.io/flag-icon-css/flags/4x3/$lower.svg' alt='$val' />";
						break;
					case 'sort':
						echo '☰';
						echo $val;
						break;
					default:
						echo is_null($val) ? '<span class="empty">-</span>' : $val;
				}
				
				if ($isLink) {
					echo "</a>";
				}

				echo "</td>";
			}
			foreach ($foreignKeys as $fkidx => $fk) {
				echo "<td>";
				$targetTable = $fk->getTargetTable();
				$targetName = $fk->getName();

				if($targetTable->hasPrimaryKeys() && fkIsset($targetTable, $row, $targetName)) {
					$tableComment = $targetTable->getComment();
					$tableAttributes = parseColumnAttributes($tableComment);

					if(array_key_exists('Display', $tableAttributes)) {
						$template = $tableAttributes['Display'];

						$val = preg_replace_callback('~\{(?<col>[^\}]+)\}~i', function($m) use ($row, $targetName) {
							$idx = $targetName . '_' . $m['col'];
							return $row->$idx ?: '';
						}, $template);
					} else {
						$val = implode(',', array_map(function($c) use ($targetName, $row) {
							$key = $targetName . '_' . $c->getName();
							return $row->$key;
						}, iterator_to_array($targetTable->primaryKeys())));
					}

					$idQC = buildIdQuery($targetTable, $row, $targetName);
					echo "<a href='?$idQC'>";
					echo $val;
					echo "</a>";
				} else {
					echo "<span class='empty'>-</span>";
				}
				echo "</td>";
			}
			foreach ($reverseForeignKeys as $rfkidx => $rfk) {
				$rfkName = $rfk->getName();
				$rfkNameShort = $inflector->pluralize(preg_replace('/^fk_(.+)__.+$/i', '$1', $rfkName));
				echo "<td>";
				echo "<a class=badge href='?$idQ#$rfkNameShort'>";
				echo $row->{$rfkName.'_count'};
				echo "</a>";
				echo "</td>";
			}
			if($table->hasPrimaryKeys()) {
				echo "<td>";
				$idQ = buildIdQuery($table, $row, NULL);
				$idQTemplate = buildIdQuery($table, $row, NULL, 'template');

				echo "<a title=edit href='{$idQ->replace('action','edit')}'>&#x270E;</a> ";
				echo "<a title=delete href='?{$idQ->replace('action','delete')}'>&#x2715;</a> ";
				echo "<a title=copy href='?{$idQTemplate->replace('action','new')}'>&#x2398;</a> ";
				echo "</td>";
				echo "<td class=fill-label><label><input type=checkbox data-select /></label></td>";
			}
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	if (!empty($data) && ($page > 0 || count($data) > 20)) {
		if ($page > 0) {
			$prevPage = $page - 1;
			echo "<a href='?{$baseQuery->replace([$tableName,'page'], $prevPage)}{$hash}'>Back</a>";
		} else {
			echo "<span class='disabled'>Back</span>";
		}
		echo " | ";
		if (count($data) > 20) {
			$nextPage = $page + 1;
			echo "<a href='?{$baseQuery->replace([$tableName,'page'], $nextPage)}{$hash}'>Next</a>";
		} else {
			echo "<span class='disabled'>Next</span>";
		}
	}

	echo "<div>";
	echo "Export: ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'xml')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>XML</a>";
	echo " | ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'json')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>JSON</a>";
	echo " | ";
	echo "<a href='?{$baseQuery
		->replace('export.format', 'csv')
		->remove([$tableName,'page'])
		->replace('export.data', $exportName)
	}'>CSV</a>";
	echo "</div>";

	echo "<div><a href='?$addUrl'>+ New $tableTitle</a></div>";
}

function renderCSV($table, $data) {
	$row = [];
	$columns = $table->columns(false);
	$usedColumns = [];
	foreach ($columns as $col) {
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('HideInList', $attributes)) {
			continue;
		}
		if(array_key_exists('Secret', $attributes)) {
			continue;
		}

		$row[] = (string)$col->getName();
		$usedColumns []= $col;
	}

	echo implode(',', $row);
	echo PHP_EOL;
	$tableName = $table->getName();

	foreach ($data AS $row) {
		echo implode(',', array_map(function($col) use ($row, $tableName) {
			$colName = (string)$col->getName();
			return maybeEncodeCSVField($row->{$tableName.'_'.$colName});
		}, $usedColumns));
		echo PHP_EOL;
	}
}

function renderJSON($table, $data) {
	$row = [];
	$columns = $table->columns(false);
	$usedColumns = [];
	$keys = [];
	foreach ($columns as $col) {
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('HideInList', $attributes)) {
			continue;
		}
		if(array_key_exists('Secret', $attributes)) {
			continue;
		}
		$usedColumns []= $col;
		$keys[] = $col->getName();
	}

	echo '[';
	echo PHP_EOL;
	$tableName = $table->getName();

	foreach ($data AS $i => $row) {
		echo ($i>0) ? ',' : ' ';
		echo json_encode(array_combine($keys, array_map(function($colName) use ($row, $tableName) {
			return $row->{$tableName.'_'.$colName};
		}, $keys)), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		echo PHP_EOL;
	}

	echo ']';
}

function maybeEncodeCSVField($string) {
    if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        $string = '"' . str_replace('"', '""', $string) . '"';
    }
    return $string;
}

function renderForm($table, $connection, $scope, $parentFk = NULL) {
	echo "<dl class='prop-list'>";
	$scopeString = scopeToFieldName($scope);
	$tableComment = $table->getComment();
	$tableAttributes = parseColumnAttributes($tableComment);
	

	foreach ($table->foreignKeys() as $fk) {
		if($fk == $parentFk) {
			continue;
		}
		echo "<dt>";
		$fkTitle = ucwords(str_replace('_', ' ', 
			preg_replace('/^fk_.+__/i', '', $fk->getName())
		));
		echo $fkTitle;
		echo "</dt>";
		echo "<dd>";
		
		$optTargetTable = $fk->getTargetTable();
		$optionComment = $optTargetTable->getComment();
		$optionAttributes = parseColumnAttributes($optionComment);
		$optTargetName = $optTargetTable->getName();
		
		$sql = buildTableQuery($optTargetTable);
		$stmt = $connection->prepare($sql);
		$stmt->bindValue(':offset', 0, PDO::PARAM_INT);
		$stmt->bindValue(':limit', 50+1, PDO::PARAM_INT);
		echo "<div class='sql debug'>$sql</div>";
		$stmt->execute();
		$options = $stmt->fetchAll(PDO::FETCH_OBJ);
		

		if(array_key_exists('Display', $optionAttributes)) {
			$optTemplate = $optionAttributes['Display'];
		} else {
			$optTemplate = '{id}';
		}

		preg_match_all('~\{(?<col>[^\}]+)\}~i', $optTemplate, $colRefs);

		$colRefs = $colRefs['col'];
		$uniqIndices = [];

		foreach($optTargetTable->indices() AS $optIndex) {
			if(!$optIndex->isUnique()) {
				continue;
			}
			$uniqIndices []= $optIndex;
		}

		$uniqIndices = array_map(function($i) use ($colRefs) {
			$fks = array_map(function($c) {
				return $c->getForeignKey();
			}, array_filter(iterator_to_array($i->getColumns()), function($c) {
				return $c->belongsToForeignKey();
			}));
			return array_filter($fks, function($c) use ($colRefs) {
				return !in_array($c->getName(), $colRefs);
			});
		}, $uniqIndices);

		$simplestFK = NULL;

		foreach($uniqIndices AS $i) {
			if($simplestFK === NULL || count($simplestFK) >= count($i)) {
				$simplestFK = $i;
			}
		}

		$extraTemplate = $simplestFK ? array_values(array_map(function($f) {
			$fkName = $f->getName();
			$fkTargetName = $f->getTargetTable()->getName();
			$xComment = $f->getTargetTable()->getComment();
			$xAttributes = parseColumnAttributes($xComment);
			if(array_key_exists('Display', $xAttributes)) {
				return preg_replace_callback('~\{((?<fk>[^\}:]+):)?(?<col>[^\}]+)\}~i', function($m) use ($opt, $fkName) {
					$pre = !empty($m['fk']) ? $m['fk'] : $fkName;
					$idx = $pre . ':' . $m['col'];
					return "[{".$idx."}]";
				}, $xAttributes['Display']);
			} else {
				return "[".$fkTargetName." : {".$fkName.":id}]";
			}
		}, $simplestFK))[0] : NULL;

		if($extraTemplate) {
			$optTemplate = $optTemplate . ' '. $extraTemplate;
		}
		

		$fieldName = sprintf('%s[%s]', scopeToFieldName($scope), $fk->getName());

		echo "<select name='$fieldName' data-form-scope='$scopeString'>";
		if(!$fk->isRequired()) {
			echo "<option>---None---</option>";
		}
		if(count($options) > 0) {
			echo "<optgroup label=Existing:>";
			foreach($options AS $i => $opt) {
				if($i===50) {
					break;
				}
				echo "<option value=''>";
				echo preg_replace_callback('~\{((?<fk>[^\}:]+):)?(?<col>[^\}]+)\}~i', function($m) use ($opt, $optTargetName) {
					$pre = !empty($m['fk']) ? $m['fk'] : $optTargetName;
					$idx = $pre . '_' . $m['col'];
					return $opt->$idx ?: '';
				}, $optTemplate);
				echo "</option>";
			}
			echo "</optgroup>";
		}
		if(count($options) > 50) {
			echo "<optgroup label='More...'></optgroup>";
		}
		if(!$parentFk) 
		echo "<option value=__new>New: </option>";
		echo "</select>";
		
		if(!$parentFk) {
			$subScope = scopeToFieldName(array_merge($scope, ['__new', $fk->getName()]));
			echo "<div class=sub-form data-form-scope='$subScope'>";
			$subTitle = ucwords(str_replace('_', ' ', $fk->getTargetTable()->getName()));
			echo "<h2>New $subTitle</h2>";
			renderForm($fk->getTargetTable(), $connection, array_merge($scope, ['__new', $fk->getName()]), $fk);
			echo "</div>";
		}

		echo "</dd>";
	}
	foreach ($table->columns(false) as $col) {
		$dataType = $col->getType();
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if($col->isSerialColumn()) {
			continue;
		}
		echo "<dt>";
		$colTitle = ucwords(str_replace('_', ' ', $col->getName()));
		echo $colTitle;
		echo "</dt>";
		echo "<dd>";
		$fieldName = sprintf('%s[%s]', scopeToFieldName($scope), $col->getName());
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if($dataType instanceof ColumnType\Enumerable) {
			$multi = $dataType->allowMultiple();
			if($multi) {
				foreach($dataType->getOptions() AS $o) {
					echo "<label><input type=checkbox value=$o /> " . $o . "</label>";
				}
			} else {
				foreach($dataType->getOptions() AS $o) {
					echo "<label><input name={$col->getName()} type=radio value=$o /> " . $o . "</label>";
				}
			}
		} elseif($dataType instanceof ColumnType\Date) {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=date />";
		} elseif($dataType instanceof ColumnType\DateTime) {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=date />";
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=time />";
		} elseif($dataType instanceof ColumnType\Time) {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=time />";
		} elseif($dataType instanceof ColumnType\String) {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=text />";
		} elseif($dataType instanceof ColumnType\Blob) {
			echo "<textarea name='$fieldName'></textarea>";
		} elseif($dataType instanceof ColumnType\Integer) {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=number />";
		} else {
			echo "<input name='$fieldName' data-form-scope='$scopeString' type=text />";
		}


		echo "</dd>";
	}
	echo "</dl>";

	global $inflector;

	if(
		!$parentFk && 
		//!array_key_exists('NoChildren', $tableAttributes) && 
		count($table->reverseForeignKeys())
	) {
		echo "<h2>Child Data</h2>";
		foreach ($table->reverseForeignKeys() as $assoc) {
			$sourceTable = $assoc->getOwnTable();
			$relTitle = ucwords(str_replace('_', ' ', 
				preg_replace('/^fk_.+__/i', '', $assoc->getName())
			));
			$sourceTitle = ucwords(str_replace('_', ' ', $sourceTable->getName()));
			echo "<h3>{$inflector->pluralize($sourceTitle)} this is a {$relTitle} of</h3>";
			echo "<div class=child-records>";

			renderForm($sourceTable, $connection, [], $assoc);

			echo "</div>";
		}
	}
}

function isJoinTable($table) {
	return count($table->columns(FALSE)) <= 1;
}

function parseColumnAttributes($string) {
	$attributes = [];
	if($string && preg_match_all('~@(?<key>[^\(\s@]+)(\(\"?(?<val>[^\)]+?)\"?\))?~i', $string, $matches, PREG_SET_ORDER)) {
		foreach($matches as $conf) {
			if(array_key_exists($conf['key'], $attributes)) {
				throw new \Exception("Duplicate attribute {$conf['key']}");
			}
			$attributes[$conf['key']] = isset($conf['val']) ? $conf['val'] : null;
		}
	}

	return $attributes;
}

function buildTableQuery($table, $single = false, $includeChildCounts = FALSE, $orderBy = NULL, $orderDir = 'ASC') {
	$columns = [];
	$tables = [];
	$conditions = [];
	$joins = [];

	$tables[] = $table->getName();

	if($single === TRUE) {
		foreach($table->primaryKeys() AS $pk) {
			$conditions[] = $table->getName() . '.' . $pk->getName() . ' = :'.$pk->getName();
		}
	}

	$orderByRel = null;
	foreach ($table->foreignKeys() as $fk) {
		if($orderBy == $fk->getName()) {
			$orderByRel = $fk;
		}
		$targetTable = $fk->getTargetTable();

		$sourceCols = $fk->getOwnColumns();
		$targetCols = $fk->getForeignColumns();

		$joinConditions = [];
		for($i=0;$i<count($sourceCols);$i++) {
			$joinConditions []= sprintf('%s.%s = %s.%s',  $fk->getName(), $targetCols[$i], $fk->getOwnTable()->getName(), $sourceCols[$i]);
		}

		$joins []= $targetTable->getName() . ' ' . $fk->getName() . ' ON ('.implode(' AND ', $joinConditions).')';


		foreach($targetTable->columns() AS $targetCol) {
			$columns []= $fk->getName() . '.' . $targetCol->getName()  . ' AS ' . $fk->getName() . '_' . $targetCol->getName();
		}

		if($single && $single instanceof ForeignKey && $single == $fk) {
			foreach($single->getForeignColumns() AS $s) {
				$conditions[] = $single->getName() . '.' . $s->getName() . ' = :' . $single->getName() . '_' . $s->getName();
			}
		}
	}

	foreach ($table->columns() as $col) {
		$columns []= $table->getName() . '.' . $col->getName() . ' AS ' . $table->getName() . '_' . $col->getName();
	}

	$orderByCount = false;
	if($includeChildCounts) {
		foreach($table->reverseForeignKeys() AS $rfk) {
			$sourceCols = $rfk->getOwnColumns();
			$targetCols = $rfk->getForeignColumns();

			$subQueryConditions = [];
			for($i=0;$i<count($sourceCols);$i++) {
				$subQueryConditions []= sprintf('%s.%s = %s.%s',  $rfk->getTargetTable()->getName(), $targetCols[$i], $rfk->getName(), $sourceCols[$i]);
			}

			$countQuery = sprintf("(SELECT \n\t\tCOUNT(*) \n\tFROM \n\t\t%s %s \n\tWHERE \n\t\t%s\n\t)", 
				$rfk->getOwnTable()->getName(), 
				$rfk->getName(),
				implode(" \n\t\tAND \n\t\t", $subQueryConditions));
			$columns []= $countQuery . ' AS '. $rfk->getName() . '_count';

			if($orderBy === (string)$rfk->getName()) {
				$orderByCount = true;
			}
		}
	}

	if($orderBy === NULL) {
		$order = [];
	} elseif($orderByRel) {
		$order = [];
		$orderTarget = $orderByRel->getTargetTable();
		$targetComment = $orderTarget->getComment();
		$targetAttributes = parseColumnAttributes($targetComment);
		
		if(array_key_exists('Display', $targetAttributes)) {
			preg_match_all('~\{(?<col>[^\}]+)\}~i', $targetAttributes['Display'], $cols);

			$colNames = array_merge($cols['col'], array_map(function($c) {
				return $c->getName();
			}, iterator_to_array($orderByRel->getForeignColumns())));

			if(!empty($colNames)) {
				$order = array_map(function($c) use($orderDir, $orderByRel) {
					return sprintf('%s_%s %s', $orderByRel->getName(), $c, ($orderDir == 'desc' ? 'desc' : 'asc'));
				}, $colNames);
			}
		}
	}  elseif($orderByCount) {
		$order = [sprintf('%s_count %s', $orderBy, ($orderDir == 'desc' ? 'desc' : 'asc'))];
	} else {
		$order = [sprintf('%s_%s %s', $table->getName(), $table->column($orderBy)->getName(), ($orderDir == 'desc' ? 'desc' : 'asc'))];
	}

	$order = $table->hasPrimaryKeys() ? implode(', ', array_merge($order, array_map(function($c) use ($table, $orderDir) {
			return $table->getName() . '_' . $c->getName() . ' ' . 'asc';
		}, iterator_to_array($table->primaryKeys())))) : '1';

	return sprintf("SELECT \n\t%s \nFROM\n\t%s\n%s \nWHERE \n\t%s \nORDER BY %s \nLIMIT :limit \nOFFSET :offset",implode(", \n\t", $columns), implode(', ', $tables), empty($joins) ? '' : "LEFT JOIN\n\t" . implode("\nLEFT JOIN\n\t", $joins), implode(' AND ', $conditions) ?: '1', $order);
}

function buildIdQuery($table, $data, $fkName = NULL, $prefix = 'id') {
	$pks = $table->primaryKeys();
	$dataPrefix = $fkName !== NULL ? $fkName : $table->getName();

	$bag = new ParameterBag([
		'table' => $table->getName(),
	]);

	if(count($pks) === 1) {
		$key = $dataPrefix . '_' . $pks[0]->getName();
		return $bag->replace($prefix, $data->$key);
	}

	return array_reduce(iterator_to_array($pks), function($bag, $c) use ($dataPrefix, $prefix, $data) {
		$key = $dataPrefix . '_' . $c->getName();
		return $bag->replace([$prefix, $c->getName()], $data->$key);
	}, $bag);
}

function fkIsset($table, $data, $fkName = NULL) {
	$pks = $table->primaryKeys();
	$dataPrefix = $fkName !== NULL ? $fkName : $table->getName();

	return array_reduce(iterator_to_array($pks), function($acc, $c) use ($dataPrefix, $data) {
		return $acc && isset($data->{$dataPrefix . '_' . $c->getName()});
	}, true);
}

function getIdFromQuery($table, $params) {
	$pks = $table->primaryKeys();

	if(count($pks) === 1) {
		return ['id' => $params];
	}

	$result = [];

	foreach($pks AS $pk) {
		$result[(string)$pk->getName()] = $params[(string)$pk->getName()];
	}

	return $result;
}

function buildQuery($oldQuery, $newValues) {
	return http_build_query(array_replace_recursive($oldQuery, $newValues));
}

function scopeToFieldName($scope) {
	return array_reduce($scope, function($prev, $field) {
		return is_null($prev) ? $field : sprintf('%s[%s]', $prev, $field);
	});
}
?>

<script>
	document.body.addEventListener('change', function(evt) {
		if(evt.target.tagName === 'INPUT' && evt.target.type === 'checkbox' && evt.target.hasAttribute('data-select')) {
			var sel = evt.target.getAttribute('data-select');
			var row = evt.target.parentNode.parentNode.parentNode;
			var table = row.parentNode.parentNode;
			if(sel === 'all') {
				var isChecked = evt.target.checked;

				Array.prototype.forEach.call(
					table.querySelectorAll('tbody [data-select]'),
					function(c) {
						c.checked = isChecked
						var cRow = c.parentNode.parentNode.parentNode
						cRow.classList.toggle('state-selected', isChecked)
					}
				)
			} else {
				row.classList.toggle('state-selected', isChecked)

				var allChecked = Array.prototype.reduce.call(
					table.querySelectorAll('tbody input[data-select]'),
					function(acc, c) {
						return acc === undefined ? c.checked : (acc === c.checked ? acc : null)
					},
					undefined
				)

				var allCheck = table.querySelector('[data-select=all]')

				allCheck.checked = allChecked === true
				allCheck.indeterminate = allChecked === null
			}
		} else if (evt.target.tagName === 'SELECT' && evt.target.nextSibling && evt.target.nextSibling.classList.contains('sub-form')) {
			var enableSubForm = evt.target.value === '__new';
			evt.target.nextSibling.classList.toggle('state-visible', enableSubForm);
			//TODO: Disabling nesting forms does not stack correctly
			Array.prototype.forEach.call(evt.target.nextSibling.querySelectorAll('[data-form-scope="'+evt.target.nextSibling.getAttribute('data-form-scope')+'"]'), function(el) {
				if(enableSubForm) {
					el.removeAttribute('disabled');
				} else {
					el.setAttribute('disabled', true);
				}
			})
		}
	}, false)
</script>
</body>
</html>