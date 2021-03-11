<?php

namespace App\Response;

final class ErrorResponse extends SerializedResponse {
	// ErrorResponse doesn't use the second $options parameter - use $_ as a throwaway
	protected function handle($exception, array $_): array {
		return [
			'data' => ['error' => $exception->format()],
			'status' => $this->status = $exception->getStatusCode()
		];
	}
}
