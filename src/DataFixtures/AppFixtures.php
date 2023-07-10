<?php
// src\DataFixtures\AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Medecin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
            $listMedecin = [];
            for ($i = 0; $i < 5; $i++) {
                // Création de l'auteur lui-même.
                $medecin = new Medecin();
                $medecin->setNom("Nom " . $i);
                $medecin->setNbrDeJours($i);
                $medecin ->setTauxJournalier($i);
                $manager->persist($medecin);
                // On sauvegarde l'auteur créé dans un tableau.
                $listMedecin[] = $medecin;
           }

        $manager->flush();
    }
}