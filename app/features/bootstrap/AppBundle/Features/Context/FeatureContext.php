<?php

namespace AppBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
//use Knp\FriendlyContexts\Context\MinkContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ResponseTextException;
use WebDriver\Key;

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
     * @Then I fill in dropdown :arg1 with :arg2
     */
    public function iFillInDropdownWith($arg1, $arg2)
    {
        $xpath = $this->getSession()->getPage()->findById($arg1)->getXpath();
        $element = $this->getSession()->getDriver()->getWebDriverSession()->element("xpath", $xpath);
        $value = strval($arg2);
        $element->postValue(array('value' => array($value)));
        sleep(3);
      //  $this->getSession()->wait(500);
        $element->postValue(array('value' => array(Key::DOWN_ARROW)));

       // $value = Key::TAB;
        $element->postValue(array('value' => array(Key::ENTER)));
    }



    /**
     * @Given /^I wait (\d+) seconds$/
     */
  /*  public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }*/

    /**
     * @Then /^I select autosuggestion option "([^"]*)"$/
     *
     * @param $text Option to be selected from autosuggestion
     * @throws \InvalidArgumentException
     */
   /* public function selectAutosuggestionOption($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', '*//*[text()="'. $text .'"]')
        );

        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }

        $element->click();
    }*/

    /**
     * @When I select :entry after filling :value in :field
     */
 /*   public function iFillInSelectInputWithAndSelect($entry, $value, $field)
    {
        $page = $this->getSession()->getPage();
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $page->fillField($field, $value);

        $element = $page->findField($field);
        $this->getSession()->getDriver()->keyDown($element->getXpath(), '', null);
        $this->getSession()->wait(500);
        $chosenResults = $page->findAll('css', '.ui-autocomplete a');
        foreach ($chosenResults as $result) {
            if ($result->getText() == $entry) {
                $result->click();
                return;
            }
        }
        throw new \Exception(sprintf('Value "%s" not found', $entry));
    }*/


    /**
     * @When I wait for the suggestion box to appear
     */
  /*  public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
        PHPUnit_Framework_Assert::assertTrue($this->getSession()->getPage()->has('css', '.suggestions-results'), 'ERROR: Suggestions are not visible');
    }*/


    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
   /* public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );
    }*/

    /**
     * @Then I type :text into search box
     */
    /*public function iTypeTextIntoSearchBox($text)
    {
        $element = $this->getSession()->getPage()->findById('searchInput');
        $script = "$('#searchInput').keypress();";
        $element->setValue($text);
        $this->getSession()->evaluateScript($script);
    }*/

    /**
     * @Given a field should contain placeholder :arg1
     */
    /*public function aFieldShouldContainPlaceholder($arg1)
    {
        $container = $this->getSession()->getPage();
        foreach ($container->findAll('css', 'input') as $element) {
            if ($element->getAttribute('placeholder') == $arg1) {
                return true;
            }
        }
        throw new \Exception("Not found this placeholder on this page", 1);
    }*/

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
    public function spin($lambda, $wait = 30)
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
            throw new \Exception($text . " could not be found");
        } else {
            $findName->click();
        }
    }

    /**
     * @When I click the :arg1 element
     */
    public function iClickTheElement($selector)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$selector')");
        }

        $element->click();
    }
}
