<?php
/**
 * Copyright 2016-2018 MTA SZTAKI ugyeletes@sztaki.hu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace AppBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Knp\FriendlyContexts\Context\MinkContext;
//use Behat\MinkExtension\Context\MinkContext;
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
       // $value = strval($arg2).Key::DOWN_ARROW.Key::ENTER;
       /* $this->getSession()
            ->getDriver()
            ->getWebDriverSession()
            ->element('xpath', $xpath)
            ->postValue(['value' => [$value]]);*/

        $element->postValue(array('value' => array($value)));
        sleep(5);
      //  $this->getSession()->wait(500);
       $element->postValue(array('value' => array(Key::DOWN_ARROW)));

       // $value = Key::TAB;
       $element->postValue(array('value' => array(Key::ENTER)));
    }

    /**
     * @When /^I check the "([^"]*)" radio button$/
     */
    public function iCheckTheRadioButton($labelText)
    {
        $page = $this->getSession()->getPage();
        $radioButton = $page->find('named', ['radio', $labelText]);
        if ($radioButton) {
            $select = $radioButton->getAttribute('name');
            $option = $radioButton->getAttribute('value');
            $page->selectFieldOption($select, $option);
            return;
        }

        throw new \Exception("Radio button with label {$labelText} not found");
    }

    /**
     * @Then I fill in full typeahead :arg1 with :arg2
     */
    public function iFillInTypeaheadWith($arg1, $arg2)
    {
        $xpath = $this->getSession()->getPage()->findById($arg1)->getXpath();
        $element = $this->getSession()->getDriver()->getWebDriverSession()->element("xpath", $xpath);

        $existingValueLength = strlen($this->getSession()->getPage()->findById($arg1)->getValue());

        $element->postValue(array('value' => array(str_repeat(Key::BACKSPACE.Key::DELETE, $existingValueLength))));
        sleep(5);

        $element->postValue(array('value' => array(strval($arg2))));
        sleep(5);
        //  $this->getSession()->wait(500);
        $element->postValue(array('value' => array(Key::DOWN_ARROW)));

        // $value = Key::TAB;
        $element->postValue(array('value' => array(Key::ENTER)));
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

    /**
     *
     * elements with data-behattarget attribute at least aimable
     *
     * @When I click the :arg1 behat target
     */
    public function iClickTheBehatTarget($databehattarget)
    {
        $page = $this->getSession()->getPage();
        $locator = '//*[@data-behattarget="'.$databehattarget.'"]';

        $element = $page->find('xpath', $locator);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$locator')");
        }

        $element->click();
    }

    /**
     *
     * elements with data-behattarget attribute at least aimable
     *
     * @When I check the :arg1 behat targeted checkbox
     */
    public function iCheckTheBehatTarget($databehattarget)
    {
        $page = $this->getSession()->getPage();
        $locator = '//input[@type="checkbox" and @data-behattarget="'.$databehattarget.'"]';

        $element = $page->find('xpath', $locator);

        if (empty($element)) {
            throw new \Exception("No html element found for the selector ('$locator')");
        }

        $element->check();
    }

}
