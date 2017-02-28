<?php

namespace LaszloKorte\Schema;

use LaszloKorte\Schema\ColumnType;

use PDO;

final class SchemaBuilder {
	public function buildSchemaFor(PDO $connection, $databaseName) {
		$def = new SchemaDefinition();

		$this->defineTables($def, $connection, $databaseName);

		return new Schema($def);
	}

	private function defineTables($def, $connection, $databaseName) {
		$SQL = '
		SELECT 
			table_name AS name,
			table_comment AS comment
		FROM 
			information_schema.tables tbls
		WHERE 
			tbls.table_schema = :database
		';

		$stmt = $connection->prepare($SQL);
		$stmt->bindValue(':database', $databaseName);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		$tableDefs = [];
		foreach ($result as $table) {
			$tableDef = $def->defineTable(new Identifier($table->name), $table->comment);

			$this->defineColumns($tableDef, $connection, $table->name, $databaseName);
			$this->defineForeignKeys($def, $connection, $table->name, $databaseName);
			$this->defineIndices($tableDef, $connection, $table->name, $databaseName);
			$tableDefs[$table->name] = $tableDef;
		}

		$this->definePrimaryKeys($tableDefs, $connection,  $databaseName);
	}

	private function defineColumns($tableDef, $connection, $tableName, $databaseName) {
		$stmt = $connection->prepare('
		SELECT 
			column_name AS name,
			column_comment AS comment,
			is_nullable AS nullable,
			column_type AS type, 
			column_default AS default_value,
			extra
		FROM 
			information_schema.columns cols
		WHERE 
			cols.table_schema = :database 
			AND 
			cols.table_name = :table
		');
		$stmt->bindValue(':database', $databaseName);
		$stmt->bindValue(':table', $tableName);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		foreach ($result as $row) {
			$colId = new Identifier($row->name);
			$columnDef = $tableDef->defineColumn($colId, $this->fromSqlType($row->type), $row->nullable === 'YES', $row->default_value, $row->comment);
			if($row->extra === 'auto_increment') {
				$tableDef->defineSerial(clone $colId);
			}
		}
	}

	private function definePrimaryKeys($tableDefs, $connection, $databaseName) {

		$stmt = $connection->prepare('
		SELECT 
			t.table_name AS table_name,
			k.COLUMN_NAME AS column_name
		FROM 
			information_schema.table_constraints t
		LEFT JOIN 
			information_schema.key_column_usage k
			ON
				t.constraint_name = k.constraint_name 
				AND 
				t.table_schema = k.table_schema
				AND
				t.table_name = k.table_name
		WHERE 
			t.constraint_type=\'PRIMARY KEY\'
			AND t.table_schema=:database
		');
		$stmt->bindValue(':database', $databaseName);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);

		foreach($result AS $tableName => $col) {
			$tableDefs[$tableName]->definePrimaryKey(
				array_map(function($c) {
					return new Identifier($c);
				}, $col)
			);
		}
	}

	private function defineForeignKeys($schemaDef, $connection, $tableName, $databaseName) {
		$stmt = $connection->prepare('
		SELECT 
			ke.constraint_name AS name,
			ke.column_name AS ownColumn,
			ke.referenced_table_name AS targetTable,
			ke.referenced_column_name AS targetColumn,
			rc.update_rule AS updateRule, 
			rc.delete_rule AS deleteRule
		FROM
			information_schema.KEY_COLUMN_USAGE ke,
			information_schema.REFERENTIAL_CONSTRAINTS rc
		WHERE
			ke.constraint_name = rc.constraint_name
			AND
			ke.referenced_table_name IS NOT NULL
			AND
			ke.constraint_schema = :database
			AND
			ke.table_name = :table
		');
		$stmt->bindValue(':database', $databaseName);
		$stmt->bindValue(':table', $tableName);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_OBJ);

		foreach ($result as $name => $row) {
			$columnDef = $schemaDef->defineForeignKey(
				new Identifier($name), 
				new Identifier($tableName),
				new Identifier($this->assumeSame($row, 'targetTable')),
				array_map(function($i) {
					return new Identifier($i->ownColumn);
				}, $row),
				array_map(function($i) {
					return new Identifier($i->targetColumn);
				}, $row),
				$this->constraintRuleFromString($this->assumeSame($row, 'updateRule')),
				$this->constraintRuleFromString($this->assumeSame($row, 'deleteRule'))
			);
		}
	}

	private function assumeSame($array, $prop) {
		return array_reduce($array, function($prev, $item) use ($prop) {
			$current = $item->$prop;
			if($prev === NULL || $current == $prev) {
				return $current;
			} else {
				throw new \Exception("Unexpected value change");
			}
		}, NULL);
	}

	private function constraintRuleFromString($string) {
		switch($string) {
			case 'RESTRICT':
				return ForeignKeyDefinition::RULE_RESTRICT;
			case 'CASCADE':
				return ForeignKeyDefinition::RULE_CASCADE;
			case 'SET NULL':
				return ForeignKeyDefinition::RULE_SET_NULL;
			case 'NONE':
				return ForeignKeyDefinition::RULE_NONE;
			default:
				return $string;
		}
	}

	private function defineIndices($def, $connection, $tableName, $databaseName) {
		$stmt = $connection->prepare('
		SELECT DISTINCT 
			index_name,
			column_name AS name,
			non_unique,
			index_type
		FROM 
			information_schema.statistics s
		WHERE
			s.table_schema = :database
			AND 
			s.table_name = :table
			AND
			s.index_name != \'primary\'
		ORDER BY
			index_name ASC, 
			seq_in_index ASC
		');
		$stmt->bindValue(':database', $databaseName);
		$stmt->bindValue(':table', $tableName);
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_OBJ);


		foreach ($result as $name => $index) {
			$columnNames = array_map(function($col) {
				return new Identifier($col->name);
			}, $index);

			$unique = $this->assumeSame($index, 'non_unique') == 0;

			$indexType = $this->assumeSame($index, 'index_type');

			if ($indexType === 'FULLTEXT') {
				$type = IndexDefinition::TYPE_FULLTEXT;
			} elseif ($indexType === 'BTREE') {
				if ($unique) {
					$type = $type = IndexDefinition::TYPE_UNIQUE;
				} else {
					$type = IndexDefinition::TYPE_KEY;
				}
			} else {
				throw new \Exception("Unknown index type: ". $indexType);
			}

			$columnDef = $def->defineIndex(
				$type,
				new Identifier($name), 
				$columnNames
			);
		}
	}

	private function fromSqlType($typeString) {
		preg_match('/\A(?<type>[a-z_-]+)(\((?<args>.+)\))?(\s(?<more>.+))?\Z/i', $typeString, $m);
		$name = $m['type'];
		$args = isset($m['args']) ? $m['args'] : null;
		$more = isset($m['more']) ? $m['more'] : null;

		switch($name) {
			case 'char':
				return new ColumnType\Chars((int)($m['args']), false);
			case 'varchar':
				return new ColumnType\Chars((int)($m['args']), true);
			case 'tinytext':
				return new ColumnType\Blob(8, false);
			case 'mediumtext':
				return new ColumnType\Blob(16, false);
			case 'text':
				return new ColumnType\Blob(16, false);
			case 'longtext':
				return new ColumnType\Blob(16, false);
			case 'tinyblob':
				return new ColumnType\Blob(8, true);
			case 'blob':
				return new ColumnType\Blob(16, true);
			case 'mediumblob':
				return new ColumnType\Blob(24, true);
			case 'longblob':
				return new ColumnType\Blob(32, true);
			case 'tinyint':
				return new ColumnType\Integer(8, $more === 'unsigned');
			case 'smallint':
				return new ColumnType\Integer(16, $more === 'unsigned');
			case 'mediumint':
				return new ColumnType\Integer(24, $more === 'unsigned');
			case 'int':
				return new ColumnType\Integer(32, $more === 'unsigned');
			case 'bigint':
				return new ColumnType\Integer(64, $more === 'unsigned');
			case 'decimal':
				$precision = explode(',', $args);
				return new ColumnType\Decimal($precision[0], $precision[1]);
			case 'float':
				// $precision = $args ? explode(',', $args) : [10,2];
				return new ColumnType\Float(32);
			case 'double':
				// $precision = $args ? explode(',', $args) : [16,4];
				return new ColumnType\Float(64);
			case 'date':
				return new ColumnType\Date();
			case 'datetime':
				return new ColumnType\DateTime();
			case 'time':
				return new ColumnType\Time();
			case 'timestamp':
				return new ColumnType\TimeStamp();
			case 'year':
				return new ColumnType\Year((int)$args);
			case 'enum':
				$options = array_map(function($o) {
					return trim($o, "'");
				}, explode(',', $args));
				return new ColumnType\Enum(false, $options);
			case 'set':
				$options = array_map(function($o) {
					return trim($o, "'");
				}, explode(',', $args));
				return new ColumnType\Enum(true, $options);
			default:
				throw new \Exception("unknown type: ". $name);
		}
	}
}