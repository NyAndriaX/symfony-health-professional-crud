<?php

namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedecinRepository::class)]
class Medecin
{

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $numed = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $nbrDeJours = null;

    #[ORM\Column]
    private ?int $tauxJournalier = null;


    public function getNumed(): ?int
    {
        return $this->numed;
    }

    public function setNumed(int $numed): static
    {
        $this->numed = $numed;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNbrDeJours(): ?int
    {
        return $this->nbrDeJours;
    }

    public function setNbrDeJours(int $nbrDeJours): static
    {
        $this->nbrDeJours = $nbrDeJours;

        return $this;
    }

    public function getTauxJournalier(): ?int
    {
        return $this->tauxJournalier;
    }

    public function setTauxJournalier(int $tauxJournalier): static
    {
        $this->tauxJournalier = $tauxJournalier;

        return $this;
    }
}
