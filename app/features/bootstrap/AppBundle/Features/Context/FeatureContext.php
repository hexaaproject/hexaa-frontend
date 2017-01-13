<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given a field should contain placeholder :arg1
     */
    public function aFieldShouldContainPlaceholder($arg1)
    {
        $container = $this->getSession()->getPage();
        foreach ($container->findAll('css', 'input') as $element) {
            if ($element->getAttribute('placeholder') == $arg1) {
                return true;
            }
        }
        throw new \Exception("Not found this placeholder on this page", 1);
    }
}
