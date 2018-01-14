@organization
@users
  @wip
Feature: When I go to organization's users
  As an authenticaed user
  I can see and edit the users

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testOrg1"
    Then I wait for "Users" to appear
    And I follow "Users"

  Scenario: Remove roles to users
    Given I wait for "Members" to appear
    When I check the "student@project.local" behat targeted checkbox
    And I check the "student@project.nolocal" behat targeted checkbox
    And I check the "student@server.hexaa.eu" behat targeted checkbox
    And I press "Change roles"
    Then I wait for "Change the selected users role" to appear
    And I check the "Test role 1" checkbox
    And I press "Remove roles"
    Then I should see "Roles of users updated successful."
    When I follow "Roles"
    And I click on accordion "Test role 1"
    Then I should not see "Student"

  Scenario: Add roles to users
    Given I wait for "Members" to appear
    When I check the "student@project.local" behat targeted checkbox
    And I check the "student@project.nolocal" behat targeted checkbox
    And I check the "student@server.hexaa.eu" behat targeted checkbox
    And I press "Change roles"
    Then I wait for "Change the selected users role" to appear
    And I check the "Test role 1" checkbox
    And I press "Add roles"
    Then I should see "Roles of users updated successful."
    When I follow "Roles"
    And I click on accordion "Test role 1"
    Then I should see "Student"
