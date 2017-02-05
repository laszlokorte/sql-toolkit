<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Presenter\Entity;
use LaszloKorte\Schema\Schema;
use LaszloKorte\Schema\Table;

final class EntityQueryBuilder {

	private $schema;

	public function __construct(Schema $schema) {
		$this->schema = $schema;
	}

	public function queryForEntity(Entity $entity) {
		$table = $this->schema->table((string)$entity->id());
		$query = new TableQuery($table);

		foreach($entity->idColumns() AS $idCol) {
			$query->includeColumn($idCol);
		}

		foreach($entity->fields() AS $field) {
			foreach($field->relatedColumns() AS $col) {
				$query->includeColumn($col);
			}

			echo implode("<br/> -", $field->getChildAssociations());
			echo "<br>";
			echo implode("<br/> -", $field->getParentAssociations());
			echo "<br>";

		}

		echo implode("<br/>", $entity->getDisplayPaths());

		return $query;
	}
}