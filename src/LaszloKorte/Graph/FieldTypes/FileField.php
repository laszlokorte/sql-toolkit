<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class FileField implements FieldType {
	private $targetDir;

	private $pathColumnId;
	private $sizeColumnId;
	private $mimeColumnId;

	public function __construct($targetDir, Identifier $pathColumnId, Identifier $siteColumnId, Identifier $mimeColumnId) {
		$this->targetDir = $targetDir;
		$this->pathColumnId = $pathColumnId;
		$this->sizeColumnId = $sizeColumnId;
		$this->mimeColumnId = $mimeColumnId;
	}

	public function getTemplateName() {
		return 'file';
	}

	public function getRelatedColumns() {
		return [
			$this->pathColumnId, 
			$this->sizeColumnId, 
			$this->mimeColumnId, 
		];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}