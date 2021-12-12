<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;

use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function json_decode;

/**
 * @Route("/api/todo")
 */
class TodoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TodoRepository $todoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TodoRepository $todoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->todoRepository = $todoRepository;
    }

    /**
     * @Route("/read", name="api_todo_read", methods={"GET"})
     */
    public function index(): Response
    {
        $todos = $this->todoRepository->findAll();

        return $this->json($todos);
    }

    /**
     * @Route("/create", name="api_todo_create", methods={"POST"})
     * @throws JsonException
     */
    public function create(Request $request): Response
    {
        $content = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $todo = new Todo();
        $todo->setName($content->name);

        try {
            $this->entityManager->persist($todo);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json([
                'message'   =>  [
                    'text' => 'Could not submit To-Do to the database!',
                    'level' =>  'error',
                ],
            ]);
        }

        return $this->json([
            'todo' => $todo->toArray(),
            'message'   =>  [
                'text' => 'To-Do has been created!',
                'level' =>  'success',
            ],
        ]);
    }

    /**
     * @Route("/update/{id}", name="api_todo_update", methods={"PUT"})
     * @throws JsonException
     */
    public function update(Todo $todo, Request $request): Response
    {
        $content = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);
        $todo->setName($content->name);

        try {
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json([
                'message'   =>  [
                    'text' => 'Could not reach database when attempting to update a To-Do',
                    'level' =>  'error',
                ],
            ]);
        }

        return $this->json([
            'todo' => $todo->toArray(),
            'message'   =>  [
                'text' => 'To-Do successfully updated!',
                'level' =>  'success',
            ],
        ]);
    }

    /**
     * @Route("/delete/{id}", name="api_todo_delete", methods={"DELETE"})
     */
    public function delete(Todo $todo): Response
    {
        try {
            $this->entityManager->remove($todo);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json([
                'message'   =>  [
                    'text' => 'Could not reach database when attempting to delete a To-Do',
                    'level' =>  'error',
                ],
            ]);
        }

        return $this->json([
            'message'   =>  [
                'text' => 'To-Do has successfully been deleted!',
                'level' =>  'success',
            ],
        ]);
    }
}
