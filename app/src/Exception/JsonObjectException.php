<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class JsonObjectException extends BadRequestHttpException {
	private $error;

	public function __construct(int $error = null) {
		parent::__construct();

		$this->error = $error ?: json_last_error();
	}

	public function format(): array {
		return ['message' => 'invalid JSON object provided', 'details' => $this->error];
	}
}
