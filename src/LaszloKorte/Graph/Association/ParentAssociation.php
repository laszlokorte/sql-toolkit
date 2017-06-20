<?php

namespace LaszloKorte\Graph\Association;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Path\PathLink;
use LaszloKorte\Graph\GraphDefinition;

final class ParentAssociation {
	private $graphDef;
	private $sourceId;
	private $name;
	private $assocDef;

	public function __construct(GraphDefinition $graphDef, Identifier $sourceId, Identifier $name, AssociationDefinition $assocDef) {
		$this->graphDef = $graphDef;
		$this->sourceId = $sourceId;
		$this->name = $name;
		$this->assocDef = $assocDef;
	}

	public function getTargetEntity() {
		return new Entity($this->graphDef, $this->assocDef->getTargetId());
	}

	public function getTargetId() {
		return $this->assocDef->getTargetId();
	}

	public function __toString() {
		return "parent:".$this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function toLink() {
		return new PathLink($this->name, $this->sourceId, $this->assocDef->getTargetId(), $this->assocDef->getJoinColumns(), $this->assocDef->getTargetColumns());
	}
}