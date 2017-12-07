@organization
@history
Feature: When I go to an organization history
  As an authenticaed user
  I can see an interactive table with the organization log

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testOrg1"
    Then I wait for "Users" to appear

  Scenario: Navigate to create organization first step page
    When I follow "View history"
    And I wait for "History" to appear
    And I wait for "Title" to appear
    Then I should see a table with 4 row
