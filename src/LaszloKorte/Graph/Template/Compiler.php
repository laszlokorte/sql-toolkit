<?php

namespace LaszloKorte\Graph\Template;

use LaszloKorte\Schema\Table;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Template\Nodes\Sequence;
use LaszloKorte\Graph\Template\Nodes\OutputTag;
use LaszloKorte\Graph\Template\Nodes\StaticText;
use LaszloKorte\Graph\Path;
use LaszloKorte\Schema\IdentifierMap;

use LaszloKorte\Graph\Template\Processed;

final class Compiler {
	public function compile(IdentifierMap $templates) {
		$preprocessed = new IdentifierMap();
		$result = new IdentifierMap();
		
		foreach($templates AS $tableId) {
			$template = $templates[$tableId]->template;
			$table = $templates[$tableId]->table;

			$preproc = $this->preprocessTemplate($template, $table);
			if(FALSE === $preproc) {
				throw new \Exception(sprintf("Invalid template for table %s", $table));
			}
			$preprocessed[$tableId] = $preproc;
		}

		foreach($preprocessed AS $tableId) {
			$template = $preprocessed[$tableId];
			$result[$tableId] = new Processed\Sequence($this->process($template, $preprocessed));
		}

		return $result;
	}

	private function process($currentTemplate, $others) {
		return array_merge([], ...array_map(function($t) use($others) {
			if($t instanceof Processed\TableRef) {
				$basePath = $t->getPath();
				$targetId = $basePath->getTarget();
				$foreignTemplate = $others[$targetId];

				return array_map(function($seg) use ($basePath) {
					if($seg instanceof Processed\ColumnValue) {
						return $seg->relativeTo($basePath);
					} else {
						return $seg;
					}
				}, $this->process($foreignTemplate, $others));
			} else {
				return [$t];
			}
		}, $currentTemplate));
	}


	private function preprocessTemplate(Sequence $seq, Table $table) {
		$result = [];
		foreach ($seq as $value) {
			if($value instanceof StaticText) {
				$result[] = new Processed\StaticText($value->getText());
			} elseif($value instanceof OutputTag) {
				$path = NULL;
				$currentTable = $table;
				foreach($value->getPath() AS $segment) {
					if($currentTable === NULL) {
						return false;
					}

					if($currentTable->hasForeignKey($segment)) {
						$fk = $currentTable->foreignKey($segment);
						$sourceTable = $fk->getOwnTable();
						$targetTable = $fk->getTargetTable();
						$currentTable = $targetTable;

						$link = new Path\PathLink(new Identifier($segment), new Identifier((string)$sourceTable->getName()), new Identifier((string)$targetTable->getName()), array_map(function($c) {
								return new Identifier((string)$c->getName());
							}, iterator_to_array($fk->getOwnColumns())), array_map(function($c) {
								return new Identifier((string)$c->getName());
							}, iterator_to_array($fk->getForeignColumns()))
						);

						if($path === NULL) {
							$path = new Path\TablePath($link);
						} else {
							$path->append($link);
						}
					} elseif($currentTable->hasColumn($segment)) {
						if($path === NULL) {
							$path = new Path\OwnColumnPath(new Identifier((string)$table->getName()), new Identifier($segment));
						} else {
							$path = new Path\ForeignColumnPath($path, new Identifier($segment));
						}
						$currentTable = NULL;
					} else {
						return false;
					}
				}

				if($path instanceof Path\TablePath) {
					$result[] = new Processed\TableRef($path);
				} elseif($path instanceof Path\ColumnPath) {
					$result[] = new Processed\ColumnValue($path, array_map(function($f) {
						return new Processed\Filter($f->getName());
					}, $value->getFilters()));
				}
			}
		}

		return $result;
	}



}