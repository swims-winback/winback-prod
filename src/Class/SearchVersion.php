<?php
namespace App\Class;

use App\Entity\Main\DeviceFamily;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

class SearchVersion
{

    /*
     @var Device[]
     */
    public $versions = [];
    /*
    #[ORM\ManyToOne(targetEntity: DeviceFamily::class, inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    public $categories;
    */

}