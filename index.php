<?php 
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
	use LaszloKorte\Schema\DatabaseId;
	use LaszloKorte\Schema\SchemaBuilder;
	use LaszloKorte\Schema\ColumnType;
	use LaszloKorte\Schema\ForeignKey;

	require __DIR__ . '/vendor/autoload.php';

	$jwtKey = "c303c6c7125d5e365ed7323f6143fb58";
	$builder = new SchemaBuilder();


	$connection = new PDO('mysql:host=directus.dev;port=3306;dbname=ishl;charset=utf8', 'ishl', 'ishl', [
			PDO::ATTR_TIMEOUT => 2,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	   		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		]);

	$schema = $builder->buildSchemaFor($connection, 'ishl');
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
	echo "<div class=login-panel>";
		echo "<h1>Login</h1>";
		echo "<form action='?login' method=post>";

		if(isset($loginError)) {
			echo $loginError;
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
} else {

	echo "<div class='menu'>";

	echo "<h1>DB Manager</h1>";
	echo "<a href=?logout rel=nofollow>Logout</a> | ";
	echo "<label><input type=checkbox onChange=\"document.body.classList.toggle('debug', this.checked)\"/> Debug</label>";
	echo "<hr>";
	$tableCategories = [];
	$uncategorized = [];
	foreach ($schema->tables() as $table) {
		// if(isJoinTable($table)) {
		// 	continue;
		// }
		$comment = $table->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('group', $attributes)) {
			$groupName = $attributes['group'];
			if(!isset($tableCategories[$groupName])) {
				$tableCategories[$groupName] = [];
			}
			$tableCategories[$groupName][] = $table;
		} else {
			$uncategorized []= $table;
		}
	}
	foreach ($tableCategories as $cat => $tables) {
		$catTitle = ucwords(str_replace('_', ' ', $cat));

		echo "<h2 class=nav-group-title>$catTitle</h2>";
		echo "<ul class=nav-list>";
		foreach ($tables as $t) {
			$tableTitle = ucwords(str_replace('_', ' ', $t->getName())) . 's';

			$currentTable = $t->getName() == $_GET['table'] ? 'state-active' : '';
			echo "<li><a class='$currentTable' href='?table={$t->getName()}'>{$tableTitle}</a></li>";
		}
		echo "</ul>";
	}
	if(count($tableCategories) > 0 && count($uncategorized) > 0) {
		echo "<h2 class=nav-group-title>Others</h2>";
	}
	echo "<ul class=nav-list>";
	foreach ($uncategorized as $t) {
		$tableTitle = ucwords(str_replace('_', ' ', $t->getName())) . 's';
		$currentTable = $t->getName() === $_GET['table'] ? 'state-active' : '';
		echo "<li><a class='$currentTable' href='?table={$t->getName()}'>{$tableTitle}</a></li>";
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
			
			echo "<h2>${tableTitle}s</h2>";

			echo "<div class='debug'>";

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
			$sql = buildTableQuery($table);
			$stmt = $connection->prepare($sql);

			$limit = 20;
			$page = (int)($_GET[$tableName]['page']);
			$stmt->bindValue(':offset', $page * $limit, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
			echo "<div class='sql debug'>$sql</div>";
			$stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_OBJ);

			renderTable($table, $data, $page, "?table=$tableName", null);
		} elseif($_GET['action'] === 'add') {
			echo "<h2>New $tableTitle</h2>";
			echo "<form>";
			echo "<dl class='prop-list'>";

			foreach ($table->foreignKeys() as $fk) {
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
				

				if(array_key_exists('display', $optionAttributes)) {
					$optTemplate = $optionAttributes['display'];
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
					}, array_filter($i->getColumns(), function($c) {
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
					if(array_key_exists('display', $xAttributes)) {
						return preg_replace_callback('~\{((?<fk>[^\}:]+):)?(?<col>[^\}]+)\}~i', function($m) use ($opt, $fkName) {
							$pre = !empty($m['fk']) ? $m['fk'] : $fkName;
							$idx = $pre . ':' . $m['col'];
							return "[{".$idx."}]";
						}, $xAttributes['display']);
					} else {
						return "[".$fkTargetName." : {".$fkName.":id}]";
					}
				}, $simplestFK))[0] : NULL;

				if($extraTemplate) {
					$optTemplate = $optTemplate . ' '. $extraTemplate;
				}
				
				echo "<select>";
				if(!$fk->isRequired()) {
					echo "<option>---None---</option>";
				}
				if(count($options) > 0) {
					echo "<optgroup label=Existing:>";
					foreach($options AS $i => $opt) {
						if($i===50) {
							break;
						}
						echo "<option>";
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
				echo "<option>New: </option>";
				echo "</select>";
				
				echo "</dd>";
			}
			foreach ($table->columns(false) as $col) {
				$dataType = $col->getType();
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($optionComment);
				if($col->isSerialColumn()) {
					continue;
				}
				echo "<dt>";
				$colTitle = ucwords(str_replace('_', ' ', $col->getName()));
				echo $colTitle;
				echo "</dt>";
				echo "<dd>";
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
					echo "<input type=date />";
				} elseif($dataType instanceof ColumnType\DateTime) {
					echo "<input type=date />";
					echo "<input type=time />";
				} elseif($dataType instanceof ColumnType\Time) {
					echo "<input type=time />";
				} elseif($dataType instanceof ColumnType\String) {
					echo "<input type=text />";
				} elseif($dataType instanceof ColumnType\Blob) {
					echo "<textarea></textarea>";
				} elseif($dataType instanceof ColumnType\Integer) {
					echo "<input type=number />";
				} else {
					echo "<input type=text />";
				}


				echo "</dd>";
			}
			echo "</dl>";

			echo "<button>Create $tableTitle</button>";
			echo "</form>";

		}
	} else {
		$tableName = $_GET['table'];
		$tableTitle = ucwords(str_replace('_', ' ',$tableName));
		$table = $schema->table($tableName);

		$id = getIdFromQuery($table, $_GET['id']);
		// echo "<a href='?'>Tables</a> / ";
		echo "<a href='?table=$tableName'>${tableTitle}s</a>";


		// $sql = sprintf('SELECT %s FROM %s WHERE id = :id', implode(', ', array_map(function($id) {
		// 	return "`{$id->getName()}`";
		// }, $table->columns())), $table->getName());
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
				if(array_key_exists('link', $attributes)) {
					$anyLink = TRUE;
					echo $data->{$tableName.'_'.$col->getName()};
					echo " ";
				}
			}
			if(!$anyLink) {
				echo implode(',', array_map(function($c) use ($tableName, $data) {
					$key = $tableName . '_' . $c->getName();
					return $data->$key;
				}, $table->primaryKeys()));
			}
			$idQ = buildIdQuery($table, $data);
			echo "[ ";
			echo "<a href='?table=$tableName&amp;action=edit&amp;$idQ'>&#x270E;</a>";
			echo " ";
			echo "<a href='?table=$tableName&amp;action=delete&amp;$idQ'>&#x2715;</a>";
			echo " ";
			echo "<a href='?table=$tableName&amp;action=copy&amp;$idQ'>&#x2398;</a>";
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
				if(array_key_exists('password', $attributes)) {
					$val = '******';
				}
				if($attributes['hyper'] === 'email') {
					echo " <a class='hyper' href='mailto:$val'>💬</a> ";
				} elseif($attributes['hyper'] === 'country') {
					$lower = strtolower($val);
						echo "<img width='50' src='https://lipis.github.io/flag-icon-css/flags/4x3/$lower.svg' alt='$val' /> ";
				}
				switch($attributes['display']) {
					case 'boolean':
						echo $val === '1' ? '<input type="checkbox" checked disabled>' : '<input type="checkbox" disabled>';
						break;
					case 'url':
						echo is_null($val) ? '<span class="empty">-</span>' : "<a href='$val'>$val</a>";
						break;
						
					case 'currency, euro':
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

				if ($foreignTable->hasPrimaryKeys()) {
					$idQ = buildIdQuery($foreignTable, $data, $targetName);

					echo "<a href='?table={$foreignTable->getName()}&amp;$idQ'>";
				
					$tableComment = $foreignTable->getComment();
					$tableAttributes = parseColumnAttributes($tableComment);

					if(array_key_exists('display', $tableAttributes)) {
						$template = $tableAttributes['display'];

						echo preg_replace_callback('~\{(?<col>[^\}]+)\}~i', function($m) use ($data, $targetName) {
							$idx = $targetName . '_' . $m['col'];
							return $data->$idx ?: '';
						}, $template);
					} else {
						echo implode(',', array_map(function($c) use ($targetName, $data) {
							$key = $targetName . '_' . $c->getName();
							return $data->$key;
						}, $foreignTable->primaryKeys()));
					}

					echo "</a>";
				} else {
					echo "<span class='empty'>-</span>";
				}
				
				echo "</dd>";
			}
			echo "</dl>";

			foreach ($table->reverseForeignKeys() as $assoc) {
				echo "<div class=child-records>";
				$sourceTable = $assoc->getOwnTable();
				$sourceTitle = ucwords(str_replace('_', ' ', $sourceTable->getName()));
				echo "<h3>{$sourceTitle}s</h3>";

				if(false && isJoinTable($sourceTable)) {
					continue;
				} else {
					$sql = 
						buildTableQuery($sourceTable, $assoc);
					$stmt = $connection->prepare($sql);
				}


				$limit = 20;
				$page = (int)($_GET[(string)$sourceTable->getName()]['page']);
				$stmt->bindValue(':offset', $page * $limit, PDO::PARAM_INT);
				$stmt->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
				foreach($assoc->getForeignColumns() AS $cix => $c) {
					$stmt->bindValue(':'.$assoc->getName().'_'.$c->getName(), $data->{$tableName.'_'.$c->getName()});
				}
				echo "<div class='sql debug'>$sql</div>";
				$stmt->execute();

				$childData = $stmt->fetchAll(PDO::FETCH_OBJ);
				$idQ = buildIdQuery($table, $data);

				renderTable($sourceTable, $childData, $page, "?table=$tableName&amp;$idQ", $table);
				echo "</div>";
			}
		} elseif($_GET['action'] === 'edit') {
			$name = '';
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('link', $attributes)) {
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

			// echo " / <a href='?table=$tableName&amp;$idQ'>$name</a>";
			echo "<h2>";
			echo "Edit ";
			echo $tableTitle;
			echo ": ";
			echo $name;
			echo "</h2>";

			echo "<p>";
			echo "<button>Save</button>";
			echo " | <a href='?table=$tableName&amp;$idQ'>Cancel</a>";
			echo "</p>";
		} elseif($_GET['action'] === 'copy') {
			$name = '';
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('link', $attributes)) {
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

			// echo " / <a href='?table=$tableName&amp;$idQ'>$name</a>";
			echo "<h2>";
			echo "Duplicate ";
			echo $tableTitle;
			echo ": ";
			echo $name;
			echo "</h2>";

			echo "<p>";
			echo "<button>Create</button>";
			echo " | <a href='?table=$tableName&amp;$idQ'>Cancel</a>";
			echo "</p>";
		} elseif($_GET['action'] === 'delete') {
			$name = '';
			$anyLink = false;
			foreach ($table->columns() as $col) {
				$comment = $col->getComment();
				$attributes = parseColumnAttributes($comment);
				if(array_key_exists('link', $attributes)) {
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
			// echo " / <a href='?table=$tableName&amp;$idQ'>$name</a>";
			echo "<h2>";
			echo "Delete ";
			echo $tableTitle;
			echo ": ";
			echo $name;
			echo "</h2>";

			
			echo "<p>";
			echo "<button>Confirm</button>";
			echo " | <a href='?table=$tableName&amp;$idQ'>Cancel</a>";
			echo "</p>";
		}
	}

	echo '</div>';
}
?>

<?php
function renderTable($table, $data, $page, $baseUrl, $parentTable) {
	$parentForeignKeys = $parentTable ? $parentTable->reverseForeignKeys() : [];
	$columns = $table->columns(false);
	$foreignKeys = array_filter($table->foreignKeys(), function($fk) use ($parentForeignKeys) {
		return !in_array($fk, $parentForeignKeys);
	});
	$tableName = $table->getName();
	$tableTitle = ucwords(str_replace('_', ' ', $tableName));


	echo "<div><a href='?table=$tableName&amp;action=add'>+ New $tableTitle</a></div>";

	echo "<div>";
	echo "Export: ";
	echo "<a>XML</a>";
	echo " | ";
	echo "<a>JSON</a>";
	echo " | ";
	echo "<a>CSV</a>";
	echo " | ";
	echo "<a>Excel</a>";
	echo "</div>";

	if (!empty($data) && ($page > 0 || count($data) > 20)) {
		if ($page > 0) {
			$prevPage = $page - 1;
			echo "<a href='{$baseUrl}&amp;{$tableName}[page]={$prevPage}'>Back</a>";
		} else {
			echo "<span class='disabled'>Back</span>";
		}
		echo " | ";
		if (count($data) > 20) {
			$nextPage = $page + 1;
			echo "<a href='{$baseUrl}&amp;{$tableName}[page]={$nextPage}'>Next</a>";
		} else {
			echo "<span class='disabled'>Next</span>";
		}
	}


	echo "<div class=searchbox><input placeholder=term... type='search' /><button>Search</button></div>";
	

	echo "<table>";
	echo "<thead>";
	echo "<tr>";
	$colCount = 0;
	foreach ($columns as $cidx => $col) {
		$comment = $col->getComment();
		$attributes = parseColumnAttributes($comment);
		if(array_key_exists('hideInList', $attributes)) {
			continue;
		}
		$colCount++;
		echo "<th>";
		if($_GET['order']['dir'] == 'asc' && $_GET['order']['col'] == $col->getName()) {
			echo " <a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=desc'>";
		} else {
			echo " <a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=asc'>";
		}
		if($col->belongsToPrimaryKey()) {
			echo "*";
		}
		if($col->isSerialColumn()) {
			echo "[S]";
		}
		echo ucwords(str_replace('_', ' ', $col->getName()));
		echo "</a> ";
		echo "<a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=asc' class=sort>";
		if($_GET['order']['dir'] == 'asc' && $_GET['order']['col'] == $col->getName()) {
			echo "▲"; //
		} else {
			echo "△";
		}
		echo "</a>";
		echo "<a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=desc' class=sort>";
		if($_GET['order']['dir'] == 'desc' && $_GET['order']['col'] == $col->getName()) {
			echo "▼"; //
		} else {
			echo "▽";
		}
		echo "</a>";
		echo "</th>";
	}
	foreach ($foreignKeys as $fkidx => $fk) {
		$colCount++;
		echo "<th>";
		// if($_GET['order']['dir'] == 'asc' && $_GET['order']['col'] == $col->getName()) {
		// 	echo " <a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=desc'>";
		// } else {
		// 	echo " <a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=asc'>";
		// }
		$targetTable = $fk->getTargetTable();

		echo ucwords(str_replace('_', ' ', 
			preg_replace('/^fk_.+__/i', '', $fk->getName())
		));
		//echo "</a> ";
		// echo "<a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=asc' class=sort>";
		// if($_GET['order']['dir'] == 'asc' && $_GET['order']['col'] == $col->getName()) {
		// 	echo "▲"; //
		// } else {
		// 	echo "△";
		// }
		// echo "</a>";
		// echo "<a href='$baseUrl&amp;order[col]={$col->getName()}&amp;order[dir]=desc' class=sort>";
		// if($_GET['order']['dir'] == 'desc' && $_GET['order']['col'] == $col->getName()) {
		// 	echo "▼"; //
		// } else {
		// 	echo "▽";
		// }
		// echo "</a>";
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
		if(array_key_exists('hideInList', $attributes)) {
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
				if(array_key_exists('hideInList', $attributes)) {
					continue;
				}
				echo "<td>";
				
				$val = $row->{$table->getName().'_'.$col->getName()};
				$isLink = array_key_exists('link', $attributes) && !is_null($val) || $col->belongsToPrimaryKey();
				$isPassword = array_key_exists('password', $attributes);

				if($isPassword) {
					$val = '*****';
				}

				if($attributes['hyper'] === 'email') {
					echo " <a class='hyper' href='mailto:$val'>💬</a> ";
				} elseif($attributes['hyper'] === 'country') {
					$lower = strtolower($val);
					echo "<img width='50' src='https://lipis.github.io/flag-icon-css/flags/4x3/$lower.svg' alt='$val' /> ";
				}

				if ($isLink) {
					$idQ = buildIdQuery($table, $row);
					echo "<a href='?table=$tableName&amp;$idQ'>";
				}
				switch($attributes['display']) {
					case 'boolean':
						echo $val === '1' ? '<input type="checkbox" checked disabled>' : '<input type="checkbox" disabled>';
						break;
					
					case 'url':
						echo is_null($val) ? '<span class="empty">-</span>' : "<a href='$val'>$val</a>";
						break;
					
					case 'currency, euro':
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

				if($targetTable->hasPrimaryKeys()) {
					$tableComment = $targetTable->getComment();
					$tableAttributes = parseColumnAttributes($tableComment);

					if(array_key_exists('display', $tableAttributes)) {
						$template = $tableAttributes['display'];

						$val = preg_replace_callback('~\{(?<col>[^\}]+)\}~i', function($m) use ($row, $targetName) {
							$idx = $targetName . '_' . $m['col'];
							return $row->$idx ?: '';
						}, $template);
					} else {
						$name = implode(',', array_map(function($c) use ($targetName, $row) {
							$key = $targetName . '_' . $c->getName();
							return $row->$key;
						}, $targetTable->primaryKeys()));
					}

					$idQ = buildIdQuery($targetTable, $row, $targetName);
					echo "<a href='?table={$targetTable->getName()}&amp;$idQ'>";
					echo $val;
					echo "</a>";
				} else {
					echo "<span class='empty'>-</span>";
				}
				echo "</td>";
			}
			if($table->hasPrimaryKeys()) {
				echo "<td>";
				$idQ = buildIdQuery($table, $row, $targetName);
				echo "<a title=edit href='?table=$tableName&amp;$idQ&amp;action=edit'>&#x270E;</a> ";
				echo "<a title=delete href='?table=$tableName&amp;$idQ&amp;action=delete'>&#x2715;</a> ";
				echo "<a title=copy href='?table=$tableName&amp;$idQ&amp;action=copy'>&#x2398;</a> ";
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
			echo "<a href='{$baseUrl}&amp;{$tableName}[page]={$prevPage}'>Back</a>";
		} else {
			echo "<span class='disabled'>Back</span>";
		}
		echo " | ";
		if (count($data) > 20) {
			$nextPage = $page + 1;
			echo "<a href='{$baseUrl}&amp;{$tableName}[page]={$nextPage}'>Next</a>";
		} else {
			echo "<span class='disabled'>Next</span>";
		}
	}

	echo "<div>";
	echo "Export: ";
	echo "<a>XML</a>";
	echo " | ";
	echo "<a>JSON</a>";
	echo " | ";
	echo "<a>CSV</a>";
	echo " | ";
	echo "<a>Excel</a>";
	echo "</div>";

	echo "<div><a href='?table=$tableName&amp;action=add'>+ New $tableTitle</a></div>";
}

function isJoinTable($table) {
	return count($table->columns(FALSE)) === 1;
}

function parseColumnAttributes($string) {
	$attributes = [];
	if($string && preg_match_all('~@(?<key>[^\(\s@]+)(\((?<val>[^\)]+)\))?~i', $string, $matches, PREG_SET_ORDER)) {
		foreach($matches as $conf) {
			if(array_key_exists($conf['key'], $attributes)) {
				throw new \Exception("Duplicate attribute {$conf['key']}");
			}
			$attributes[$conf['key']] = $conf['val'];
		}
	}

	return $attributes;
}

function buildTableQuery($table, $single = false) {
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

	foreach ($table->foreignKeys() as $fk) {
		$targetTable = $fk->getTargetTable();
		//$tables []= $targetTable->getName();

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

	$order = $table->hasPrimaryKeys() ? implode(',', array_map(function($c) use ($table) {
		return $table->getName() . '_' . $c->getName();
	}, $table->primaryKeys())) : '1';

	return sprintf("SELECT \n\t%s \nFROM\n\t%s\n%s \nWHERE \n\t%s \nORDER BY %s \nLIMIT :limit \nOFFSET :offset",implode(", \n\t", $columns), implode(', ', $tables), empty($joins) ? '' : "LEFT JOIN\n\t" . implode("\nLEFT JOIN\n\t", $joins), implode(' AND ', $conditions) ?: '1', $order);
}

function buildIdQuery($table, $data, $fkName = NULL, $prefix = 'id') {
	$pks = $table->primaryKeys();
	$dataPrefix = $fkName !== NULL ? $fkName : $table->getName();

	if(count($pks) === 1) {
		$key = $dataPrefix . '_' . $pks[0]->getName();
		return $prefix . '=' . $data->$key;
	}

	return implode('&amp;', array_map(function($c) use ($dataPrefix, $prefix, $data) {
		$key = $dataPrefix . '_' . $c->getName();
		return $prefix . '[' . $c->getName() . ']=' . $data->$key;
	}, $pks));
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
		}
	}, false)
</script>
</body>
</html>