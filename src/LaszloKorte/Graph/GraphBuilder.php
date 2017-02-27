<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Configurator\Aspect\Template;
use LaszloKorte\Configurator\SchemaConfiguration;
use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Configurator\ColumnAnnotation as CA;

use LaszloKorte\Graph\Template\Parser;
use LaszloKorte\Graph\Template\Lexer;
use LaszloKorte\Graph\Template\Compiler;
use LaszloKorte\Graph\Template\Nodes\Sequence;
use LaszloKorte\Graph\Template\Nodes\OutputTag;

use LaszloKorte\Graph\Auth\Authenticator;

use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\IdentifierMap;

use LaszloKorte\Graph\Path;
use LaszloKorte\Graph\Identifier;


use Doctrine\Common\Inflector\Inflector;

final class GraphBuilder {

	private $templateParser;
	private $templateLexer;
	private $inflector;
	private $config;

	private $authenticators = [];

	public function __construct(array $config) {
		$this->config = $config;
		$this->templateParser = new Parser();
		$this->templateLexer = new Lexer();
		$this->templateCompiler = new Compiler();
		$this->inflector = new Inflector();
	}

	public function buildGraph(SchemaConfiguration $configuration) {
		$graphDef = new GraphDefinition();
		$entityBuilders = new IdentifierMap();

		$templates = $this->processTemplates($configuration);

		foreach($configuration->getTableIds() as $tableId) {
			$tableConf = $configuration->getTableConf($tableId);

			$table = $tableConf->getTable();
			$entityBuilder = new EntityBuilder($table);
			if(isset($templates[TA\Display::class][$tableId])) {
				$entityBuilder->setDisplayTemplateCompiled($templates[TA\Display::class][$tableId]);
			}

			foreach($tableConf->getAnnotations() AS $tableAnnotation) {
				//$entityBuilder->build($entityBuilder);
				$this->processTable($entityBuilder, $tableAnnotation);
			}

			foreach($table->foreignKeys() AS $fk) {
				$fieldBuilder = new RelationFieldBuilder($fk, false);

				foreach($fk->getOwnColumns() AS $fkCol) {
					$columnConf = $tableConf->getColumnConf($fkCol->getName());

					foreach($columnConf->getAnnotations() AS $colAnnotation) {
						$this->processColumn($fieldBuilder, $colAnnotation);
					}
				}

				$entityBuilder->attachFieldBuilder($fieldBuilder);
			}

			foreach($table->reverseForeignKeys() AS $revFk) {
				$fieldBuilder = new RelationFieldBuilder($revFk, true);

				$entityBuilder->attachFieldBuilder($fieldBuilder);
			}

			$colCount = count($tableConf->getColumnIds());
			foreach($tableConf->getColumnIds() AS $columnIndex =>  $columnId) {
				$columnConf = $tableConf->getColumnConf($columnId);
				$column = $columnConf->getColumn();

				if($entityBuilder->isColumnAlreadyHandled($columnId)) {
					continue;
				}

				$fieldBuilder = new ColumnFieldBuilder($colCount-$columnIndex, (string) $column->getName(), $column);

				foreach($columnConf->getAnnotations() AS $colAnnotation) {
					$this->processColumn($fieldBuilder, $colAnnotation);
				}

				$entityBuilder->attachFieldBuilder($fieldBuilder);
			}

			$entityBuilders[$tableId] = $entityBuilder;
		}

		foreach($entityBuilders AS $tableId) {
			$builder = $entityBuilders[$tableId];

			$builder->buildEntity($this, $graphDef);
		}

		foreach($this->authenticators AS $auth) {
			$graphDef->addAuthenticator($auth);
		}


		return $graphDef;
	}

	private function addAuthentication($table, $loginCol, $passwordCol) {
		if(!$table->hasColumn($loginCol)) {
			throw new \Exception("Login Column does not exist");
		}
		if(!$table->hasColumn($passwordCol)) {
			throw new \Exception("Login Column does not exist");
		}

		$this->authenticators[] = new Authenticator(new Identifier((string) $table->getName()), new Identifier($loginCol), new Identifier($passwordCol));
	}

	private function processTemplates(SchemaConfiguration $configuration) {
		$templates = [];

		foreach($configuration->getTableIds() as $tableId) {
			$tableConf = $configuration->getTableConf($tableId);

			$table = $tableConf->getTable();

			foreach($tableConf->getAnnotations() AS $tableAnnotation) {
				if($tableAnnotation instanceof Template) {
					$tplType = get_class($tableAnnotation);
					if(!isset($templates[$tplType])) {
						$templates[$tplType] = new IdentifierMap();
					}
					$templates[$tplType][new Identifier((string) $tableId)] = (object) [
						'template' => $this->templateParser->parse($this->templateLexer->tokenize($tableAnnotation->getString())),
						'table' => $table,
					];
				}
			}
		}

		$result = [];
		foreach($templates AS $type => $tplCollection) {
			$result[$type] = $this->templateCompiler->compile($tplCollection);
		}
		
		return $result;
	}

	private function processColumn(FieldBuilder $fieldBuilder, CA\Annotation $colAnn) {
		switch(get_class($colAnn)) {
			case CA\Aggregatable::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setAggregatable(true);
				break;
			case CA\Priority::class:
				$fieldBuilder->increasePriority($colAnn->value);
				break;
			case CA\Description::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setDescription($colAnn->text);
				break;
			case CA\Display::class:
				$fieldBuilder->requireUnique($colAnn);
				break;
			case CA\HideInList::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setCollectionVisible(false);
				break;
			case CA\Hyper::class:
				$fieldBuilder->requireUnique($colAnn);
				break;
			case CA\Control::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setType($colAnn->name, $colAnn->params);
				break;
			case CA\Link::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setLinked(true);
				break;
			case CA\Secret::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setSecret(true);
				break;
			case CA\Title::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setTitle($colAnn->title);
				break;
			case CA\Visible::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setVisible($colAnn->isVisible);
				break;
			default:
				$fieldBuilder->reportUnknownAnnotation($colAnn);
		}
	}

	private function processTable(EntityBuilder $entityBuilder, TA\Annotation $tblAnn) {
		switch(get_class($tblAnn)) {
			case TA\Display::class:
				// $entityBuilder->requireUnique($tblAnn);
				// $template = $this->templateParser->parse($this->templateLexer->tokenize($tblAnn->templateString));
				
				// $table = $entityBuilder->getTable();
				// $processedTemplate = $this->processTemplate($table, $template);
				// if(FALSE === $processedTemplate) {
				// 	throw new \Exception(sprintf("Invalid display template for table '%s'", $table->getName()));
				// }

				// $entityBuilder->setDisplayTemplate($template);
				break;
			case TA\Display::class:
				$entityBuilder->requireUnique($tblAnn);
				$template = $this->templateParser->parse($this->templateLexer->tokenize($tblAnn->urlTemplte));

				$table = $entityBuilder->getTable();
				$processedTemplate = $this->processTemplate($tabl, $template);
				if(FALSE === $processedTemplate) {
					throw new \Exception(sprintf("Invalid Preview URL template for table '%s'", $table->getName()));
				}

				$entityBuilder->setPreviewUrl($template);
				break;
			case TA\Description::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setDescription($tblAnn->text);
				break;
			case TA\Id::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setId($tblAnn->id);
				break;
			case TA\Login::class:
				$entityBuilder->requireUnique($tblAnn);
				$this->addAuthentication($entityBuilder->getTable(), $tblAnn->loginColumn, $tblAnn->passwordColumn);
				break;
			case TA\NavGroup::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setGroup($tblAnn->groupName);
				break;
			case TA\NoChildren::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->disableChildren();
				break;
			case TA\ParentRel::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setParent($tblAnn->parentName);
				break;
			case TA\Priority::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setPriority($tblAnn->value);
				break;
			case TA\Sort::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setSortColumn($tblAnn->columnName);
				break;
			case TA\Title::class:
				$entityBuilder->requireUnique($tblAnn);
				$singular = $tblAnn->singular;
				$plural = $tblAnn->plural;
				$entityBuilder->setTitle($singular, $plural);
				break;
			case TA\Visible::class:
				$entityBuilder->requireUnique($tblAnn);
				$entityBuilder->setVisible($tblAnn->isVisible);
				break;
			case TA\CollectionView::class:
				$entityBuilder->addCollectionView($tblAnn->name);
				break;
			case TA\RelationName::class:
				$singular = $tblAnn->singular;
				$plural = $tblAnn->plural ?? $this->pluralize($singular);
				$entityBuilder->setForeignKeyName($tblAnn->fkName, $singular, $plural);
				break;
			case TA\SyntheticControl::class:
				$table = $entityBuilder->getTable();
				$fieldBuilder = new SyntheticFieldBuilder($table, $tblAnn->id, $tblAnn->interfaceName, array_map(function($colName) use ($table) {
					return $table->column($colName);
				}, $tblAnn->columns), $tblAnn->params);

				// foreach($columnConf->getAnnotations() AS $colAnnotation) {
				// 	$this->processColumn($fieldBuilder, $colAnnotation);
				// }

				$entityBuilder->attachFieldBuilder($fieldBuilder);
				break;
			default:
				$entityBuilder->reportUnknownAnnotation($colAnn);
		}
	}



	private function processTemplate($table, Sequence $seq) {
		$paths = [];
		foreach ($seq as $value) {
			if(!$value instanceof OutputTag) {
				continue;
			}

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

			$paths[] = $path;
		}

		return $paths;
	}

	private $prevAnnotations = [];

	public function requireUnique(TA\Annotation $a) {
		$class = get_class($a);
		if(in_array($class, $this->prevAnnotations)) {
			throw new \Exception(sprintf('Duplicate annotation "%s"', $class));
		}

		$this->prevAnnotations []= $class;
	}

	public function pluralize($string) {
		$parts = explode(' ', $string);
		$last = array_pop($parts);
		array_push($parts, $this->inflector->pluralize($last));
		return implode(' ', $parts);
	}

	public function titelize($string) {
		return ucwords(str_replace([
			'_'
		], [
			' '
		], $string));
	}
}