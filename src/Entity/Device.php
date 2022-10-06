<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
//#[ORM\Table(name:"device", indexes:#[ORM\Index(columns:["sn", "version"], flags:"fulltext")])]
/**
 * @ORM\Table(name="device", indexes={@ORM\Index(columns={"sn", "version"}, flags={"fulltext"})})
 */
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\Column(type: 'string', length: 255)]
    private $sn;

    #[ORM\Column(type: 'string', nullable: true)]
    private $version;

    #[ORM\Column(type: 'string')]
    private $versionUpload;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $forced = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $ipAddr;

    #[ORM\Column(type: 'integer')]
    private $logPointeur;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $pub;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $codePin;

    #[ORM\ManyToOne(targetEntity: DeviceFamily::class, inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    private $deviceFamily;

    /*
    #[ORM\ManyToOne(targetEntity: DeviceFamily::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $type;
    */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $type;

    #[ORM\Column(type: 'boolean')]
    private $selected = false;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created_at', type: "datetime_immutable", nullable: true)]
    private $created_at;
    

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name: 'updated_at', type: "datetime", nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'sn', targetEntity: Statistics::class)]
    private $statistics;

    #[ORM\Column(type: 'boolean')]
    private $isActive = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $deviceFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $status = 'inactive';

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'date', nullable: true)]
    private $serverDate;

    #[ORM\ManyToMany(targetEntity: Software::class, inversedBy: 'devices')]
    private $software;

    #[ORM\Column(type: 'boolean')]
    private $connected = false;

    #[ORM\Column(type: 'integer')]
    private $download;

    public function __construct()
    {
        $this->versionUpload = new ArrayCollection();
        $this->statistics = new ArrayCollection();
        $this->software = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersionUpload(): ?string
    {
        return $this->versionUpload;
    }

    public function setVersionUpload(string $versionUpload): self
    {
        $this->versionUpload = $versionUpload;

        return $this;
    }

    public function getForced(): ?bool
    {
        return $this->forced;
    }

    public function setForced(?bool $forced): self
    {
        $this->forced = $forced;

        return $this;
    }

    public function getIpAddr(): ?string
    {
        return $this->ipAddr;
    }

    public function setIpAddr(string $ipAddr): self
    {
        $this->ipAddr = $ipAddr;

        return $this;
    }

    public function getLogPointeur(): ?int
    {
        return $this->logPointeur;
    }

    public function setLogPointeur(int $logPointeur): self
    {
        $this->logPointeur = $logPointeur;

        return $this;
    }

    public function getPub(): ?bool
    {
        return $this->pub;
    }

    public function setPub(?bool $pub): self
    {
        $this->pub = $pub;

        return $this;
    }

    public function getCodePin(): ?string
    {
        return $this->codePin;
    }

    public function setCodePin(?string $codePin): self
    {
        $this->codePin = $codePin;

        return $this;
    }

    public function getDeviceFamily(): ?DeviceFamily
    {
        return $this->deviceFamily;
    }

    public function setDeviceFamily(?DeviceFamily $deviceFamily): self
    {
        $this->deviceFamily = $deviceFamily;

        return $this;
    }
    /*
    public function getType(): ?DeviceFamily
    {
        return $this->type;
    }
    */
    public function getType(): ?string
    {
        return $this->type;
    }
    /*
    public function setType(?DeviceFamily $type): self
    {
        $this->type = $type;

        return $this;
    }
    */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    /**
     * @return Collection<int, Statistics>
     */
    public function getStatistics(): Collection
    {
        return $this->statistics;
    }

    public function addStatistic(Statistics $statistic): self
    {
        if (!$this->statistics->contains($statistic)) {
            $this->statistics[] = $statistic;
            $statistic->setSn($this);
        }

        return $this;
    }

    public function removeStatistic(Statistics $statistic): self
    {
        if ($this->statistics->removeElement($statistic)) {
            // set the owning side to null (unless already changed)
            if ($statistic->getSn() === $this) {
                $statistic->setSn(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDeviceFile(): ?string
    {
        return $this->deviceFile;
    }

    public function setDeviceFile(string $deviceFile): self
    {
        $this->deviceFile = $deviceFile;

        return $this;
    }

    public function getLogFile(): ?string
    {
        return $this->logFile;
    }

    public function setLogFile(?string $logFile): self
    {
        $this->logFile = $logFile;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getServerDate(): ?\DateTimeInterface
    {
        return $this->serverDate;
    }

    public function setServerDate(?\DateTimeInterface $serverDate): self
    {
        $this->serverDate = $serverDate;

        return $this;
    }

    /**
     * @return Collection<int, Software>
     */
    public function getSoftware(): Collection
    {
        return $this->software;
    }

    public function addSoftware(Software $software): self
    {
        if (!$this->software->contains($software)) {
            $this->software[] = $software;
        }

        return $this;
    }

    public function removeSoftware(Software $software): self
    {
        $this->software->removeElement($software);

        return $this;
    }

    public function getConnected(): ?bool
    {
        return $this->connected;
    }

    public function setConnected(bool $connected): self
    {
        $this->connected = $connected;

        return $this;
    }

    public function getDownload(): ?int
    {
        return $this->download;
    }

    public function setDownload(int $download): self
    {
        $this->download = $download;

        return $this;
    }
}
