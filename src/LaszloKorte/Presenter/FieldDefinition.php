<?php

namespace LaszloKorte\Presenter;

final class FieldDefinition {

	private $type;
	private $title;
	private $description;
	private $isRequired;
	private $isVisible;
	private $isVisibleInCollection;

	public function __construct($title, $fieldType) {
		$this->title = $title;
		$this->type = $fieldType;
	}

	public function setTitle($title) {
		$this->title = $title;
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
}