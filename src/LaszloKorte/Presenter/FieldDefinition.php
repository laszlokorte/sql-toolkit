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

	}

	public function setRequired($isRequired) {

	}

	public function setVisibility($isVisible) {
		
	}

	public function setCollectionVisibility($isVisible) {
		
	}

	public function setGroup($groupName) {
		
	}

	public function setPriority($prio) {
		
	}

	public function setDescription($description) {

	}
}