<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSetting;
use App\Exception\EntityIdException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserSettingsController extends ResourceController {
	protected const ENTITY = UserSetting::class;
    protected const REPO_ENTITY = User::class;

    private function findSetting(int $userId, int $id) {
        $user = $this->find($userId);

        foreach ($user->getSettings() as $setting) {
            if ($setting->getId() === $id) {
                return $setting;
            }
        }

        throw new EntityIdException($this->entityName, $id, [$this->repoEntityName => $userId]);
    }

    #[Route('/users/{userId}/settings', name: 'user_settings_GET', methods: ['GET'])]
    /**
     * Fetch settings for user
     *
     * @OA\Parameter(
     *   name="userId",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Response(
     *   response=200,
     *   description="List of user settings",
     *   @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=UserSetting::class))
     *   )
     * )
     */
    public function user_settings_GET(int $userId): Response {
        try {
            $user = $this->find($userId);
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        return $this->response->collection($user->getSettings());
    }

    #[Route('/users/{userId}/settings', name: 'user_settings_POST', methods: ['POST'])]
    /**
     * Create new setting for user
     *
     * @OA\Parameter(
     *   name="userId",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\RequestBody(
     *   request="User Setting",
     *   required=true,
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(ref=@Model(type=UserSetting::class, groups={"json_schema"}))
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
    public function user_settings_POST(int $userId, Request $request): Response {
        try {
            $user = $this->find($userId);
            $setting = $this->hydrate($request->getContent());
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        $user->addSetting($setting);
	    $this->save();

	    return $this->response->entity(
            $setting,
            created: $this->generateUrl('user_setting_GET', ['userId' => $userId, 'id' => $setting->getId()])
        );
    }

    #[Route('/users/{userId}/settings/{id}', name: 'user_setting_GET', methods: ['GET'])]
    /**
     * Fetch setting by ID and user ID
     *
     * @OA\Parameter(
     *   name="userId",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="Setting ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\RequestBody(
     *   request="User Setting",
     *   required=true,
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(ref=@Model(type=UserSetting::class, groups={"json_schema"}))
     *   )
     * )
     *
     * @OA\Response(
     *   response=201,
     *   description="Newly created user setting",
     *   @OA\Header(
     *     header="Location",
     *     description="Path to new user setting",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\JsonContent(ref=@Model(type=UserSetting::class))
     * )
     */
    public function user_setting_GET(int $userId, int $id): Response {
        try {
            $setting = $this->findSetting($userId, $id);
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        return $this->response->entity($setting);
    }

    #[Route('/users/{userId}/settings/{id}', name: 'user_setting_PUT', methods: ['PUT'])]
    /**
     * Fetch setting by ID and user ID and replace data
     *
     * @OA\Parameter(
     *   name="userId",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="Setting ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\RequestBody(
     *   request="User Setting",
     *   required=true,
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(ref=@Model(type=UserSetting::class, groups={"json_schema"}))
     *   )
     * )
     *
     * @OA\Response(
     *   response=200,
     *   description="Replaced user setting object",
     *   @OA\JsonContent(ref=@Model(type=UserSetting::class))
     * )
     */
    public function user_setting_PUT(int $userId, int $id, Request $request): Response {
        try {
            $setting = $this->findSetting($userId, $id);
            $this->hydrate($request->getContent(), $setting);
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        $this->save();

	    return $this->response->entity($setting);
    }

    #[Route('/users/{userId}/settings/{id}', name: 'user_setting_PATCH', methods: ['PATCH'])]
    /**
     * Find setting by ID and user ID and update data (preserving omitted properties)
     *
     * @OA\Parameter(
     *   name="userId",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="Setting ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\RequestBody(
     *   request="User Setting",
     *   required=true,
     *   @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(ref=@Model(type=UserSetting::class, groups={"json_schema"}))
     *   )
     * )
     */
    public function user_setting_PATCH(int $userId, int $id, Request $request): Response {
        try {
            $setting = $this->findSetting($userId, $id);

            // pass $patch flag
            $this->hydrate($request->getContent(), $setting, true);
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        $this->save();

        return $this->response->entity($setting);
    }

    #[Route('/users/{userId}/settings/{id}', name: 'user_setting_DELETE', methods: ['DELETE'])]
    /**
     * Delete setting by ID and user ID
     *
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="User ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="Setting ID",
     *   @OA\Schema(type="int")
     * )
     *
     * @OA\Response(response=204, description="No Content")
     */
    public function user_setting_DELETE(int $userId, int $id): Response {
        try {
            $setting = $this->findSetting($userId, $id);
        } catch (HttpExceptionInterface $exception) {
            return $this->response->error($exception);
        }

        $setting->getUser()->removeSetting($setting);
        $this->save();

        // invoke ResponseFactory directly
        return ($this->response)(status: Response::HTTP_NO_CONTENT);
    }
}
