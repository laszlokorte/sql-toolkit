<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\SchemaConfiguration;
use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Configurator\ColumnAnnotation as CA;

use LaszloKorte\Resource\Template\Parser;
use LaszloKorte\Resource\Template\Lexer;

use LaszloKorte\Schema\IdentifierMap;

use Doctrine\Common\Inflector\Inflector;

final class ApplicationBuilder {

	private $templateParser;
	private $templateLexer;
	private $inflector;

	public function __construct() {
		$this->templateParser = new Parser();
		$this->templateLexer = new Lexer();
		$this->inflector = new Inflector();
	}

	public function buildApplication(SchemaConfiguration $configuration) {
		$appDef = new ApplicationDefinition();
		$entityBuilders = new IdentifierMap();

		foreach($configuration->getTableIds() as $tableId) {
			$tableConf = $configuration->getTableConf($tableId);


			$entityBuilder = new EntityBuilder($tableConf->getTable());

			foreach($tableConf->getColumnIds() AS $columnId) {
				$columnConf = $tableConf->getColumnConf($columnId);

				$fieldBuilder = new FieldBuilder($columnConf->getColumn());

				foreach($columnConf->getAnnotations() AS $colAnnotation) {
					$this->processColumn($fieldBuilder, $colAnnotation);
				}
			}

			foreach($tableConf->getAnnotations() AS $tableAnnotation) {
				//$entityBuilder->build($entityBuilder);
				$this->processTable($entityBuilder, $tableAnnotation);
			}

			$entityBuilder->attachFieldBuilder($fieldBuilder);
			$entityBuilders[$tableId] = $entityBuilder;
		}

		foreach($entityBuilders AS $tableId) {
			$builder = $entityBuilders[$tableId];

			$builder->buildEntity($this, $appDef);
		}
	}

	private function processColumn(FieldBuilder $fieldBuilder, CA\Annotation $colAnn) {
		switch(get_class($colAnn)) {
			case CA\Aggregatable::class:
				$fieldBuilder->requireUnique($colAnn);
				$fieldBuilder->setAggregatable(true);
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
		}
	}

	private function processTable(EntityBuilder $entityBuilder, TA\Annotation $tblAnn) {
		switch(get_class($tblAnn)) {
			case TA\Display::class:
				$entityBuilder->requireUnique($tblAnn);
				$template = $this->templateParser->parse($this->templateLexer->tokenize($tblAnn->templateString));
				$entityBuilder->setDisplayTemplate($template);
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
				$this->requireUnique($tblAnn);
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
				$plural = $tblAnn->plural;
				$entityBuilder->setForeignKeyName($tblAnn->fkName, $singular, $plural);
				break;
			case TA\SyntheticControl::class:
				$entityBuilder->addSyntheticControl($tblAnn->interfaceName, $tblAnn->params);
				break;
		}
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
		implode(' ', $parts);
	}
}