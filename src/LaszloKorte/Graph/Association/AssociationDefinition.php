<?php

namespace LaszloKorte\Graph\Association;

use LaszloKorte\Graph\Identifier;

use Serializable;

final class AssociationDefinition implements Serializable {
	private $targetId;
	private $joinColumns;
	private $targetColumns;

	public function __construct(Identifier $targetId, array $joinColumns, array $targetColumns) {
		foreach($joinColumns AS $c) {
			if(!$c instanceof Identifier) {
				throw new \Exception(sprintf('Join column is expected to be an %s but %s given', Identifier::class, get_class($c)));
			}
		}
		$this->targetId = $targetId;
		$this->joinColumns = $joinColumns;
		$this->targetColumns = $targetColumns;
	}

	public function getTargetId() {
		return $this->targetId;
	}

	public function getJoinColumns() {
		return $this->joinColumns;
	}

	public function getTargetColumns() {
		return $this->targetColumns;
	}

	public function serialize() {
		return serialize([
			$this->targetId,
			$this->joinColumns,
			$this->targetColumns,
		]);
	}

	public function unserialize($data) {
		list(
			$this->targetId,
			$this->joinColumns,
			$this->targetColumns,
		) = unserialize($data);
	}
}