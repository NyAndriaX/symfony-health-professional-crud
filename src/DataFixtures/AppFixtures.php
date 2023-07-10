<?php
// src\DataFixtures\AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
            $listAuthor = [];
            for ($i = 0; $i < 10; $i++) {
                // Création de l'auteur lui-même.
                $author = new Author();
                $author->setFirstName("Prénom " . $i);
                $author->setLastName("Nom " . $i);
                $manager->persist($author);
                // On sauvegarde l'auteur créé dans un tableau.
                $listAuthor[] = $author;
           }

           for ($i = 0; $i < 20; $i++) {
                $book = new Book();
                $book->setTitle("Titre " . $i);
                // On lie le livre à un auteur pris au hasard dans le tableau des auteurs.
                
                $book->setAuthor($listAuthor[array_rand($listAuthor)]);
                $manager->persist($book);
            }

        $manager->flush();
    }
}