<?php
namespace App\Class;

use App\Entity\DeviceFamily;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

class SearchData
{

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /*
    @var DeviceFamily[]
    */
    //public $categories = [];
    public $categories;
    /*
    #[ORM\ManyToOne(targetEntity: DeviceFamily::class, inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    public $categories;
    */
    /**
     * @var null|string
     */
    public $version;

    /**
     * @var null|string
     */
    public $version_upload;

    /**
     * @var boolean
     */
    public $forced = false;
    
    /**
     * @var boolean
     */
    public $connected = false;

}