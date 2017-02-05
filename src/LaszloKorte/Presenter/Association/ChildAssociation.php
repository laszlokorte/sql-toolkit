<?php

namespace LaszloKorte\Presenter\Association;

use LaszloKorte\Presenter\Identifier;
use LaszloKorte\Presenter\Entity;

final class ChildAssociation {
	private $appDef;
	private $sourceId;
	private $name;
	private $assocDef;

	public function __construct($appDef, Identifier $sourceId, Identifier $name, AssociationDefinition $assocDef) {
		$this->appDef = $appDef;
		$this->sourceId = $sourceId;
		$this->name = $name;
		$this->assocDef = $assocDef;
	}

	public function getTargetEntity() {
		return new Entity($this->appDef, $this->assocDef->getTargetId());
	}

	public function __toString() {
		return "child:".$this->name;
	}

	public function getName() {
		return $this->name;
	}
}