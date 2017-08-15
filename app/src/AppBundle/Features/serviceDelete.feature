@servicedelete
Feature: When I go to specific service
  As an authenticated user
  I want to delete this.


  Background:
    Given I am on "/"
    Then I wait for "testService1" to appear
    And I should see "testService1"
    And I should see "testService2"
    Then I follow "testService1"
    And I wait for "testService1" to appear
    And I should see "Delete service"

  Scenario: After click cancel button in delete form
            I should see Delete service again
    When I click the "#deleteService" element
    Then I wait for "Are you sure?" to appear
    Then I press "Cancel"
    Then I should see "Delete service"

  Scenario: Delete service

    When I click the "#deleteService" element
    Then I wait for "Are you sure?" to appear
    Then I press "Delete service"
    Then I am on "/"
    And I should not see "testService1"
    And I should see "testService2"
