<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;


class Warning
{
    private $title;
    private $shortDescription;
    private $details;
    private $class;

    /**
     * Warning constructor.
     *
     * @param $title
     * @param $shortDescription
     * @param $details
     */

    public function __construct($title=null, $shortDescription=null, $details=null)
    {
        $this->title = $title;
        $this->shortDescription = $shortDescription;
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param mixed $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

}
