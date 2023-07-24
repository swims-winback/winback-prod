<?php
namespace App\Class;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

class SearchError
{

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
    * @var Device[]
    */
    public $sn_category = [];

    /**
     * @var ErrorFamily[]
     */
    public $error_category = [];

    /**
     * @var null|string
     */
    public $version;

    /**
     * @var null|string
     */
    public $date;
}