# symfony-health-professional-crud

# project-vueJS-L2

# Back end

# step 1: clone project

git clone https://github.com/TsilavinaDevJS/symfony-health-professional-crud.git

# step 2: Installation of dependencies

composer install

# step 3: create entity

php bin/console make:migrations

#

php bin/console make:migrations:migrate

# step 4 : create fixtures

php bin/console doctrine:fixtures:load

# step 5 : start to project

symfony serve:start

# To Know:

# Publishing and updating with table join

add this code on the variable in your entity

# #[Groups(["getBooks"])]

for exemple update
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

for exemple post
#[Route('/api/book', name:"createBook", methods: ['POST'])]
public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository): JsonResponse
{

        $book = $serializer->deserialize($request->getContent(), \App\Entity\Book::class, 'json');
        if($book){
            $content = $request->toArray();
            $author_id = $content['author_id'] ?? -1;
            $book->setAuthor($authorRepository->find($author_id));
            $em->persist($book);
            $em->flush();
            $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
            $location = $urlGenerator->generate('detail_book', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
        }
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST, [], true);

}
