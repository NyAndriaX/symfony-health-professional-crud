<?php
//src/Controller/BookController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AuthorRepository;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;



class BookController extends AbstractController
{
    #[Route('/api/book', name: 'all_book', methods: ["GET"])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ["groups" => "getBooks"]);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'detail_book',methods:["GET"])]
    public function getDetailBook(BookRepository $bookRepository,SerializerInterface $serializer,int $id): JsonResponse
    {
        $book = $bookRepository -> find($id);
        if($book){
            $jsonbook = $serializer ->serialize($book,'json');
            return new JsonResponse($jsonbook,Response::HTTP_OK,['accept'=>'json'],true);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/book/{id}', name: 'deleteBook', methods: ["DELETE"])]
    public function deleteBook(BookRepository $bookRepository, EntityManagerInterface $em,int $id): JsonResponse 
    {
        $book = $bookRepository ->find($id);
        if($book){
            $em->remove($book);
            $em->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);

        
    }

    #[Route('/api/book', name:"createBook", methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository): JsonResponse 
    {

        $book = $serializer->deserialize($request->getContent(), \App\Entity\Book::class, 'json');
        if($book){
            //register
            //Récupération de l'ensemble des données envoyées sous forme de tableau
            $content = $request->toArray();

            // Récupération de l'idAuthor. S'il n'est pas défini, alors on met -1 par défaut.
            $author_id = $content['author_id'] ?? -1;

            // On cherche l'auteur qui correspond et on l'assigne au livre.
            // Si "find" ne trouve pas l'auteur, alors null sera retourné.
            $book->setAuthor($authorRepository->find($author_id));

            $em->persist($book);
            $em->flush();            

            $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

            $location = $urlGenerator->generate('detail_book', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);


            return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);        
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST, [], true);       


   }

       #[Route('/api/book/{id}', name:"updateBook", methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, AuthorRepository $authorRepository,int $id): JsonResponse 
    {

       $existingBook = $em->getRepository(\App\Entity\Book::class)->find($id);
        if ($existingBook) {
            $updatedBook = $serializer->deserialize($request->getContent(), \App\Entity\Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $existingBook]);
            if ($updatedBook) {
                $content = $request->toArray();
                $author_id = $content['author_id'] ?? -1;
                $updatedBook->setAuthor($authorRepository->find($author_id));
                
                $em->merge($updatedBook);
                $em->flush();
                
                return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
            }
        }


   }



}
