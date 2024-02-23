<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

class UserController extends AbstractController
{

     /**
     * @Route("/user", methods={"POST"})
     */
    public function createUser(Connection $connection, Request $request): Response
    {
        $data = $request->request->all();

	$name = $data['name'];
	$surname = $data['surname'];

        $sql = 'INSERT INTO users (name, surname) VALUES (?, ?)';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $surname);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     */
    public function updateUser(Connection $connection, Request $request, $id): Response
    {
        $data = $request->request->all();
        
        $name = $data['name'];
	$surname = $data['surname'];

        $sql = 'UPDATE users SET name = ?, surname = ? WHERE user_id = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $surname);
        $stmt->bindValue(3, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

     /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function fetchUser(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();

        $user = $result->fetchAssociative();

        return $this->json($user);
    }

    /**
     * @Route("/user", methods={"GET"})
     */
    public function getUsers(Connection $connection): Response
    {
        $sql = 'SELECT * FROM users';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $users = $result->fetchAllAssociative();

        return $this->json($users);
    }

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     */
    public function deleteUser(Connection $connection, $id): Response
    {
        $sql = 'DELETE FROM users WHERE user_id = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->executeQuery();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/user/{id}/tasks", methods={"GET"})
     */
    public function fetchUserTasks(Connection $connection, $id): Response
    {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();
    
        $user = $result->fetchAssociative();
    
        if (!$user) {
            return new Response('Пользователь не найден', Response::HTTP_NOT_FOUND);
        }
    
        $sql = 'SELECT tasks.task_id, tasks.task, tasks.active FROM tasks WHERE tasks.user_id = ?';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $result = $stmt->executeQuery();
    
        $tasks = $result->fetchAllAssociative();
    
        return $this->json($tasks);
    }
}
