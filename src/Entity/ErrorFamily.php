<?php

namespace App\Entity;

use App\Repository\ErrorFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ErrorFamilyRepository::class)]
class ErrorFamily
{
    /*
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    */
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $error_id;

    #[ORM\OneToMany(mappedBy: 'error', targetEntity: Error::class)]
    private $errors;
    
    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->error_id;
    }
    
    /*
    public function getId(): ?int
    {
        return $this->id;
    }
    */
    
    public function getErrorId(): ?string
    {
        return $this->error_id;
    }

    public function setErrorId(string $error_id): self
    {
        $this->error_id = $error_id;

        return $this;
    }

    /**
     * @return Collection<int, Error>
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }
}
