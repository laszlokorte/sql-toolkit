<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\SchemaConfiguration;

final class EntityBuilder {
	
}

final class FieldBuilder {
	
}

final class ApplicationBuilder {

	public function __construct() {
	}

	public function buildApplication(SchemaConfiguration $configuration) {
		$app = new ApplicationDefinition();

		foreach($configuration->getTableIds() as $tableId) {
			$tableConf = $configuration->getTableConf($tableId);

			$entityBuilder = new EntityBuilder();

			foreach($tableConf->getColumnIds() AS $columnId) {
				$columnConf = $tableConf->getColumnConf($columnId);

				$fieldBuilder = new FieldBuilder($entityBuilder);

				foreach($columnConf->getAnnotations() AS $colAnnotation) {
					//$colAnnotation->build($fieldBuilder);
				}
			}

			foreach($tableConf->getAnnotations() AS $tableAnnotation) {
				//$entityBuilder->build($entityBuilder);
			}
		}
	}
}