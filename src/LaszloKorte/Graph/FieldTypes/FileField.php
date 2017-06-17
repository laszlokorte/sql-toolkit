<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class FileField implements FieldType, Serializable {
	private $targetDir;

	private $pathColumnId;
	private $sizeColumnId;
	private $mimeColumnId;

	public function __construct($targetDir, Identifier $pathColumnId, Identifier $sizeColumnId, Identifier $mimeColumnId) {
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
			'path' => $this->pathColumnId, 
			'size' => $this->sizeColumnId, 
			'mime' => $this->mimeColumnId, 
		];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}

	public function serialize() {
		return serialize([
			$this->pathColumnId,
			$this->targetDir,
			$this->sizeColumnId,
			$this->mimeColumnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->pathColumnId,
			$this->targetDir,
			$this->sizeColumnId,
			$this->mimeColumnId,
		) = unserialize($data);
	}
}