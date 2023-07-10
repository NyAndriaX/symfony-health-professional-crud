<?php

namespace App\Controller;

use App\Repository\MedecinRepository;
use App\Entity\Medecin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class MedecinController extends AbstractController
{
    #[Route('/api/medecin', name: 'all_medecin', methods: ["GET"])]
    public function getBookList(MedecinRepository $medecinRepository, SerializerInterface $serializer): JsonResponse
    {
        $medecinList = $medecinRepository->findAll();
        $jsonBookList = $serializer->serialize($medecinList, 'json');
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/medecin/{id}', name: 'detail_medecin',methods:["GET"])]
    public function getDetailBook(MedecinRepository $medecinRepository,SerializerInterface $serializer,int $id): JsonResponse
    {
        $medecin = $medecinRepository -> find($id);
        if($medecin){
            $jsonmedecin = $serializer ->serialize($medecin,'json');
            return new JsonResponse($jsonmedecin,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/medecin/{id}', name: 'deleteMedecin', methods: ["DELETE"])]
    public function deleteBook(MedecinRepository $medecinRepository, EntityManagerInterface $em,int $id): JsonResponse 
    {
        $medecin = $medecinRepository ->find($id);
        if($medecin){
            $em->remove($medecin);
            $em->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/medecin/{id}', name:"updateMedecin", methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, MedecinRepository $medecinRepository,int $id): JsonResponse 
    {

       $existingMedecin = $em->getRepository(\App\Entity\Medecin::class)->find($id);
        if ($existingMedecin) {
            $updatedMedecin = $serializer->deserialize($request->getContent(), \App\Entity\Medecin::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $existingMedecin]);
            if ($updatedMedecin) {
                $em->merge($updatedMedecin);
                $em->flush();
                return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
            }
        }
    }

    #[Route('/api/medecin', name:"createMedecin", methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator): JsonResponse 
    {

        $medecin = $serializer->deserialize($request->getContent(), \App\Entity\Medecin::class, 'json');
        if($medecin){
            $content = $request->toArray();
            $em->persist($medecin);
            $em->flush();
            $jsonmedecin = $serializer->serialize($medecin, 'json');
            $location = $urlGenerator->generate('detail_medecin', ['id' => $medecin->getNumed()], UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse($jsonmedecin, Response::HTTP_CREATED, ["Location" => $location], true);        
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST, [], true);       


   }


}
