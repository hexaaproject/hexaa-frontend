<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;
//use Behat\MinkExtension\Context\MinkContext;
use Knp\FriendlyContexts\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext
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

    /**
     * @When I wait for :arg1 seconds
     */
    public function iWaitForSeconds($arg1)
    {
        sleep($arg1);
    }


    /**
     * @When I wait for :text to appear
     * @Then I should see :text appear
     * @param $text
     * @throws \Exception
     */
    public function iWaitForTextToAppear($text)
    {
        $context = new MinkContext();
        $this->spin(function(FeatureContext $context) use ($text) {
            try {
                $context->assertPageContainsText($text);
                return true;
            }
            catch(ResponseTextException $e) {
                // NOOP
            }
            return false;
        });
    }

    /**
     * @When I wait for :text to disappear
     * @Then I should see :text disappear
     * @param $text
     * @throws \Exception
     */
    public function iWaitForTextToDisappear($text)
    {
        $this->spin(function($context) use ($text) {
            /** @var $context FeatureContext */
            return !$context->getSession()->getPage()->hasContent($text);
        });
    }

    /**
     * Based on Behat's own example
     * @see http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html#adding-a-timeout
     * @param $lambda
     * @param int $wait
     * @throws \Exception
     */
    public function spin($lambda, $wait = 300)
    {
        $time = time();
        $stopTime = $time + $wait;
        while (time() < $stopTime)
        {
            try {
                if ($lambda($this)) {
                    return;
                }
            } catch (\Exception $e) {
                // do nothing
            }

            usleep(250000);
        }

        throw new \Exception("Spin function timed out after {$wait} seconds");
    }

    /**
     * @Then /^I click on accordion "([^"]*)"$/
     */
    public function iClickOnAccordion($text)
    {
        $page = $this->getSession()->getPage();
        $findName = $page->find("css", '[data-name="'.$text.'"]');
        if (!$findName) {
            throw new Exception($text . " could not be found");
        } else {
            $findName->click();
        }
    }
}
