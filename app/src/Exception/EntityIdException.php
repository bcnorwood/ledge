<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EntityIdException extends NotFoundHttpException {
	private $entity;
	private $id;
	private $related;

	public function __construct(string $entity, int $id, array $related = []) {
		parent::__construct();

		$this->entity = $entity;
		$this->id = $id;
		$this->related = $related;
	}

	public function format(): array {
		$result = ['message' => "invalid $this->entity ID provided"];

		// include related fields whose IDs are part of the query
		if ($this->related) {
			$result['restrict'] = $this->related;
		}

		return $result;
	}
}
