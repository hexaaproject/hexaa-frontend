<?php
/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class Warning
 *
 * @package AppBundle\Tools\Warning
 */
class Warning
{
    /**
     * @var null
     */
    private $title;

    /**
     * @var null
     */
    private $shortDescription;

    /**
     * @var null
     */
    private $details;

    /**
     * @var
     */
    private $class;

    /**
     * Warning constructor.
     *
     * @param string $title
     * @param string $shortDescription
     * @param string $details
     */
    public function __construct($title = null, $shortDescription = null, $details = null)
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
