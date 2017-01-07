<?php

namespace LaszloKorte\Configurator;

use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\Column;
use LaszloKorte\Schema\Schema;

use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\AnnotationException;

use Exception;

class ConfigurationBuilder {

	public function __construct() {
		$this->tableParser = new DocParser();
		$this->tableParser->addNamespace('LaszloKorte\\Configurator\\TableAnnotation');

		$this->columnParser = new DocParser();
		$this->columnParser->addNamespace('LaszloKorte\\Configurator\\ColumnAnnotation');
	}

	public function buildConfigurationFor(Schema $schema) {
		$conf = new SchemaConfiguration();

		foreach($schema->tables() AS $table) {
			$tableConf = $conf->configureTable($table, $this->parseTable($table));
			foreach($table->columns() AS $col) {
				$tableConf->configureColumn($col, $this->parseColumn($col));
			}
		}

		return $conf;
	}

	private function parseTable(Table $table) {
		try {
			return $this->tableParser->parse($table->getComment());
		} catch(AnnotationException $e) {
			throw new Exception(sprintf("Error while parsing annotations on table '%s'", $table->getName()), 1, $e);
		}
	}

	private function parseColumn(Column $column) {
		try {
			return $this->columnParser->parse($column->getComment());
		} catch(AnnotationException $e) {
			throw new Exception(sprintf("Error while parsing annotations on column '%s' in table '%s'", $column->getName(), $column->table()->getName()), 1, $e);
		}
	}
}
