<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "property_search")]
class PropertySearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $nom;

    // Getter for 'id'
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter for 'nom'
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Setter for 'nom'
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}


?>