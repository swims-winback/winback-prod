<?php

namespace App\Entity\Main;

use App\Repository\DeviceFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceFamilyRepository::class)]
#[ORM\Table(name:"`device_family`")]
class DeviceFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private $name;


    #[ORM\Column(type: 'string', nullable: true, length: 255, name:"`therapy_type`")]
    private $therapyType;

    #[ORM\OneToMany(mappedBy: 'deviceFamily', targetEntity: Device::class)]
    private $devices;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true, name:"`hexa_id`")]
    private $hexaId;

    #[ORM\Column(type: 'integer', nullable: true, unique: true, name:"`number_id`")]
    private $numberId;

    #[ORM\OneToMany(mappedBy: 'deviceFamily', targetEntity: Software::class, fetch:"EXTRA_LAZY", orphanRemoval:true)]
    /**
     * @ORM\OrderBy({"version" = "DESC"})
     */
    private $softwares;

    /*
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Software $actualVersion = null;
    */

    //TODO
    
    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true, name:"`actual_version`")]
    private $actualVersion;
    
    /*
    #[ORM\OneToMany(mappedBy: 'deviceFamily', targetEntity: Software::class)]
    private Collection $softwares;
    */

    public function __construct()
    {
        $this->softwares = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }
    /*
    public function __toString()
    {
        return $this->numberId;
    }
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTherapyType(): ?string
    {
        return $this->therapyType;
    }

    public function setTherapyType(string $therapyType): self
    {
        $this->therapyType = $therapyType;

        return $this;
    }
    
    /**
     * @return Collection<int, Software>
     */
     
     public function getSoftwares(): Collection
     {
         return $this->softwares;
     }
 
     public function addSoftware(Software $software): self
     {
         if (!$this->softwares->contains($software)) {
             $this->softwares[] = $software;
             $software->setDeviceFamily($this);
         }
 
         return $this;
     }
 
     public function removeSoftware(Software $software): self
     {
        
        if ($this->softwares->removeElement($software)) {
             // set the owning side to null (unless already changed)
             if ($software->getDeviceFamily() === $this) {
                 $software->setDeviceFamily(null);
             }
         }
        
         return $this;
     }
     
    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setDeviceFamily($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getDeviceFamily() === $this) {
                $device->setDeviceFamily(null);
            }
        }

        return $this;
    }

    public function getHexaId(): ?string
    {
        return $this->hexaId;
    }

    public function setHexaId(string $hexaId): self
    {
        $this->hexaId = $hexaId;

        return $this;
    }

    public function getNumberId(): ?string
    {
        return $this->numberId;
    }

    public function setNumberId(string $numberId): self
    {
        $this->numberId = $numberId;

        return $this;
    }

    
    // @return Collection<int, Software>
     
    /*
    public function getSoftwares(): Collection
    {
        return $this->softwares;
    }

    public function addSoftware(Software $software): self
    {
        if (!$this->softwares->contains($software)) {
            $this->softwares->add($software);
            $software->setDeviceFamily($this);
        }

        return $this;
    }

    public function removeSoftware(Software $software): self
    {
        if ($this->softwares->removeElement($software)) {
            // set the owning side to null (unless already changed)
            if ($software->getDeviceFamily() === $this) {
                $software->setDeviceFamily(null);
            }
        }

        return $this;
    }
    */

    /*
    public function getActualVersion(): ?Software
    {
        return $this->actualVersion;
    }

    public function setActualVersion(Software $actualVersion): self
    {
        $this->actualVersion = $actualVersion;
        return $this;
    }
    */

    //TODO
    
    public function getActualVersion(): ?string
    {
        return $this->actualVersion;
    }

    public function setActualVersion($actualVersion): self
    {
        $this->actualVersion = $actualVersion;
        return $this;
    }
    
}
