<?php

namespace App\Entity\Main;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /*
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Sn::class)]
    private Collection $serial_number;
    */

    public function __construct()
    {
        //$this->serial_number = new ArrayCollection();
    }

    //#[ORM\Column(length: 200)]
    //private ?string $serial_number = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    /*
    public function getSerialNumber(): ?string
    {
        return $this->serial_number;
    }

    public function setSerialNumber(string $serial_number): self
    {
        $this->serial_number = $serial_number;

        return $this;
    }
    */

    /*
    public function getSerialNumber(): Collection
    {
        return $this->serial_number;
    }

    
    public function addSerialNumber(Sn $serialNumber): self
    {
        if (!$this->serial_number->contains($serialNumber)) {
            $this->serial_number->add($serialNumber);
            $serialNumber->setClient($this);
        }

        return $this;
    }

    public function removeSerialNumber(Sn $serialNumber): self
    {
        if ($this->serial_number->removeElement($serialNumber)) {
            // set the owning side to null (unless already changed)
            if ($serialNumber->getClient() === $this) {
                $serialNumber->setClient(null);
            }
        }

        return $this;
    }
    */

}