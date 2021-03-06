<?php

namespace LaszloKorte\Graph\Association;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Path\PathLink;

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

	public function getSourceEntity() {
		return new Entity($this->appDef, $this->sourceId);
	}

	public function __toString() {
		return "child:".$this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function toLink() {
		return new PathLink($this->name, $this->sourceId, $this->assocDef->getTargetId(), $this->assocDef->getTargetColumns(), $this->assocDef->getJoinColumns());
	}
}