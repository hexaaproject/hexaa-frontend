@organization
@properties
Feature: When I go to organization's properties
  As an authenticaed user
  I can see and edit the properties

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testOrg1"
    Then I wait for "Properties" to appear

  Scenario: I see the properties
    When I follow "Properties"
    Then I should see "Ez a szervezet"

  Scenario: Switch to edit mode
    When I follow "Properties"
    And I press "create"
    Then I fill in "Home page" with "www.example.com"

  Scenario: Cancel the filled form
    When I follow "Properties"
    And I press "create"
    And I fill in "Home page" with "www.example.com"
    And I press "clear"
    Then I should not see "www.example.com"

  Scenario: Submit the form
    When I follow "Properties"
    And I press "create"
    And I fill in "Home page" with "www.example.com"
    And I press "done"
    Then I should see "www.example.com"

