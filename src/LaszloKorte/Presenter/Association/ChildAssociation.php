<?php

namespace LaszloKorte\Presenter\Association;

final class ChildAssociation {
	private $appDef;
	private $sourceId;
	private $name;
	private $linkDef;

	public function __construct($appDef, $sourceId, $name, $linkDef) {
		$this->appDef = $appDef;
		$this->sourceId = $sourceId;
		$this->name = $name;
		$this->linkDef = $linkDef;
	}

	public function getTargetEntity() {
		return new Entity($this->appDef, $this->linkDef->getTargetId());
	}

	public function __toString() {
		return "child:".$this->name;
	}
}