@profile
@history
Feature: When I go to an account history
  As an authenticaed user
  I can see an interactive table with the profile news log

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "Edit profile"
    Then I wait for "Account history" to appear

  Scenario: Navigate to account history
    When I follow "Account history"
    And I wait for "Account history" to appear
    And I wait for "Title" to appear
    Then I should see a table with 10 row
