<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class EntitySchemaException extends BadRequestHttpException {
	private $entity;
	private $errors;

	public function __construct(string $entity, array $errors = []) {
		parent::__construct();

		$this->entity = $entity;

		$this->errors = array_map(
			// extract relevant error data and format
			fn ($error) => ['field' => $error['property'], 'error' => $error['message']],
			$errors
		);
	}

	public function format(): array {
		return ['message' => "invalid $this->entity object provided", 'details' => $this->errors];
	}
}
