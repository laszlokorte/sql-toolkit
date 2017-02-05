<?php

namespace LaszloKorte\Presenter\Association;

use LaszloKorte\Presenter\Identifier;
use LaszloKorte\Presenter\Entity;
use LaszloKorte\Presenter\Path\PathLink;

final class ParentAssociation {
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
		return "parent:".$this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function toLink() {
		return new PathLink($this->name, $this->sourceId, $this->assocDef->getTargetId(), $this->assocDef->getJoinColumns(), $this->getTargetEntity()->idColumns());
	}
}