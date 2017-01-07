<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\SchemaConfiguration;

final class ApplicationBuilder {

	public function __construct() {
	}

	public function buildApplication(SchemaConfiguration $configuration) {
		$app = new ApplicationDefinition();
	}
}