<?php

namespace LaszloKorte\Graph;

final class FieldDefinition {

	private $type;
	private $title;
	private $description;
	private $isRequired;
	private $isVisible;
	private $isVisibleInCollection;
	private $isSecret;
	private $priority;
	private $isLinked;

	public function __construct($title, $fieldType) {
		$this->title = $title;
		$this->type = $fieldType;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setLinked($isLinked) {
		$this->isLinked = $isLinked;
	}

	public function setRequired($isRequired) {
		$this->isRequired = $isRequired;
	}

	public function setVisibility($isVisible) {
		$this->isVisible = $isVisible;
	}

	public function setCollectionVisibility($isVisible) {
		$this->isVisibleInCollection = $isVisible;
	}

	public function setGroup($groupName) {
		
	}

	public function setPriority($prio) {
		$this->priority = $prio;
	}

	public function setSecret($isSecret) {
		$this->isSecret = $isSecret;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getTitle() {
		return $this->title;
	}

	public function isRequired() {
		return $this->isRequired;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getType() {
		return $this->type;
	}

	public function isSecret() {
		return $this->isSecret;
	}

	public function isVisibleInCollection() {
		return $this->isVisibleInCollection;
	}

	public function isVisible() {
		return $this->isVisible;
	}

	public function isLinked() {
		return $this->isLinked;
	}

	public function getPriority() {
		return $this->priority;
	}
}