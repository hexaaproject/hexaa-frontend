@service
@permissionsset
Feature: When I go to service's permissions set
  As an authenticated user
  I can create service permission set

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testService1"
    Then I wait for "Permissions sets" to appear
    And I follow "Permissions sets"

  Scenario: I create new Permission Set, cancel the form
    Given I should see "Create"
    When I press "Create"
    Then I should see "Create Permission Set"
    And I fill in "Name" with "default4"
    And I check the "public" radio button
    And I press "done"
    Then I should see "default4"
    When I click on accordion "default4"
    Then I should see "Type"
    And I should see "public"


    When I press "Create"
    And I fill in "Name" with "default5"
    And I press "clear"
    Then I should not see "default5"
