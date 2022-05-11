<?php

namespace App\Controller;

use App\Entity\User;
use App\Models\JsonError;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
    #[Route('/auth/register', name: 'app_auth', methods: 'POST')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher, SerializerInterface $serializer, ValidatorInterface $validator,): Response
    {
        $data = $request->getContent();
        try {
            $newUser = $serializer->deserialize($data, User::class, "json");
        } catch (NotNormalizableValueException $e) {
            return new JsonResponse("Erreur de type pour le champ '". $e->getPath() . "': " . $e->getCurrentType() . " au lieu de : " . implode('|', $e->getExpectedTypes()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Checking form datas
        $errors = $validator->validate($newUser);

        if (count($errors) > 0) {
            // Custom Json errors
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, "Des erreurs de validation ont été trouvées");
            $myJsonError->setValidationErrors($errors);
            return $this->json($myJsonError, $myJsonError->getError());
        }
        $hashedPassword = $hasher->hashPassword($newUser, $newUser->getPassword());
        $newUser->setPassword($hashedPassword);

        $userRepository->add($newUser);

        // Returns 201
        return $this->json(
            $newUser, Response::HTTP_CREATED,
            [],
            ['groups' => ['show_user']]
        );
    }  

      /**
   * @param UserInterface $user
   * @param JWTTokenManagerInterface $JWTManager
   * @return JsonResponse
   */
  public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
  {
   return new JsonResponse(['token' => $JWTManager->create($user)]);
  }
}
