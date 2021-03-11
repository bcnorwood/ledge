<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\EntityIdException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends ResourceController {
	protected const ENTITY = User::class;
	private const PAGE_SIZE = 10;

	#[Route('/users', name: 'users_GET', methods: ['GET'])]
	/**
	 * Fetch paginated list of users (page in query string)
	 *
	 * @OA\Parameter(
	 *   name="page",
	 *   in="query",
	 *   description="Page number (1-based)",
	 *   @OA\Schema(type="int")
	 * )
	 *
	 * @OA\Response(
	 *   response=200,
	 *   description="Paginated list of users",
	 *   @OA\JsonContent(
	 *     type="array",
	 *     @OA\Items(ref=@Model(type=User::class))
	 *   )
	 * )
	 */
	public function users_GET(Request $request): Response {
		$total = $this->repository->count([]);
		$pages = ceil($total / static::PAGE_SIZE);
		$page = max(1, min($total, $request->query->get('page')));

		return $this->response->collection(
			$this->repository->findBy([], limit: static::PAGE_SIZE, offset: ($page - 1) * static::PAGE_SIZE),
			...compact('page', 'total', 'pages')
		);
	}

	#[Route('/users', name: 'users_POST', methods: ['POST'])]
	/**
	 * Create new user
	 *
	 * @OA\RequestBody(
	 *   request="User",
	 *   required=true,
	 *   @OA\MediaType(
	 *     mediaType="application/json",
	 *     @OA\Schema(ref=@Model(type=User::class, groups={"json_schema"}))
	 *   )
	 * )
	 *
	 * @OA\Response(
	 *   response=201,
	 *   description="Newly created user",
	 *   @OA\Header(
	 *     header="Location",
	 *     description="Path to new user",
	 *     @OA\Schema(type="string")
	 *   ),
	 *   @OA\JsonContent(ref=@Model(type=User::class))
	 * )
	 */
	public function users_POST(Request $request): Response {
		try {
			$user = $this->hydrate($request->getContent());
		} catch (HttpExceptionInterface $exception) {
			return $this->response->error($exception);
		}

		$this->save($user);

		return $this->response->entity(
			$user,
			created: $this->generateUrl('user_GET', ['id' => $user->getId()])
		);
	}

	#[Route('/users/{id}', name: 'user_GET', methods: ['GET'])]
	/**
	 * Fetch user by ID
	 *
	 * @OA\Parameter(
	 *   name="id",
	 *   in="path",
	 *   description="User ID",
	 *   @OA\Schema(type="int")
	 * )
	 *
	 * @OA\Response(
	 *   response=200,
	 *   description="User",
	 *   @OA\JsonContent(ref=@Model(type=User::class))
	 * )
	 */
	public function user_GET(int $id): Response {
		try {
			$user = $this->find($id);
		} catch (HttpExceptionInterface $exception) {
			return $this->response->error($exception);
		}

		return $this->response->entity($user);
	}

	#[Route('/users/{id}', name: 'user_PUT', methods: ['PUT'])]
	/**
	 * Find user by ID and replace data
	 *
	 * @OA\Parameter(
	 *   name="id",
	 *   in="path",
	 *   description="User ID",
	 *   @OA\Schema(type="int")
	 * )
	 *
	 * @OA\RequestBody(
	 *   request="User",
	 *   required=true,
	 *   @OA\MediaType(
	 *     mediaType="application/json",
	 *     @OA\Schema(ref=@Model(type=User::class, groups={"json_schema"}))
	 *   )
	 * )
	 *
	 * @OA\Response(
	 *   response=200,
	 *   description="Replaced user object",
	 *   @OA\JsonContent(ref=@Model(type=User::class))
	 * )
	 */
	public function user_PUT(int $id, Request $request): Response {
		try {
			$user = $this->find($id);
			$this->hydrate($request->getContent(), $user);
		} catch (HttpExceptionInterface $exception) {
			return $this->response->error($exception);
		}

		$this->save();

		return $this->response->entity($user);
	}

	#[Route('/users/{id}', name: 'user_PATCH', methods: ['PATCH'])]
	/**
	 * Find user by ID and update data (preserving omitted properties)
	 *
	 * @OA\Parameter(
	 *   name="id",
	 *   in="path",
	 *   description="User ID",
	 *   @OA\Schema(type="int")
	 * )
	 *
	 * @OA\RequestBody(
	 *   request="User",
	 *   required=true,
	 *   @OA\MediaType(
	 *     mediaType="application/json",
	 *     @OA\Schema(ref=@Model(type=User::class, groups={"json_schema"}))
	 *   )
	 * )
	 *
	 * @OA\Response(
	 *   response=200,
	 *   description="Updated user object",
	 *   @OA\JsonContent(ref=@Model(type=User::class))
	 * )
	 */
	public function user_PATCH(int $id, Request $request): Response {
		try {
			$user = $this->find($id);

			// pass $patch flag
			$this->hydrate($request->getContent(), $user, true);
		} catch (HttpExceptionInterface $exception) {
			return $this->response->error($exception);
		}

		$this->save();

		return $this->response->entity($user);
	}

	#[Route('/users/{id}', name: 'user_DELETE', methods: ['DELETE'])]
	/**
	 * Delete user by ID
	 *
	 * @OA\Parameter(
	 *   name="id",
	 *   in="path",
	 *   description="User ID",
	 *   @OA\Schema(type="int")
	 * )
	 *
	 * @OA\Response(response=204, description="No Content")
	 */
	public function user_DELETE(int $id): Response {
		try {
			$this->delete($id);
		} catch (HttpExceptionInterface $exception) {
			return $this->response->error($exception);
		}

		$this->save();

		// invoke ResponseFactory directly
		return ($this->response)(status: Response::HTTP_NO_CONTENT);
	}
}
