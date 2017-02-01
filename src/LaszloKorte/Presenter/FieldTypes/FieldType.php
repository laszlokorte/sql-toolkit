<?php

namespace LaszloKorte\Presenter\FieldTypes;

interface FieldType {
	public function getTemplateName();

	public function getRelatedColumns();

	public function getParentAssociations();

	public function getChildAssociations();
}