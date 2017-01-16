<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class FileField implements FieldType {
	private $targetDir;

	private $pathColumn;
	private $sizeColumn;
	private $mimeColumn;
}