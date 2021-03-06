@organization
@show
Feature: When I go to a specific organization
  As the owner of organization
  I want to see the organization all properties

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I should see "testOrg1"

  Scenario: Navigate to organization show page
    Given I am on "/"
    Then I wait for "testOrg1" to appear
    Then I follow "testOrg1"
    And I wait for "testOrg1" to appear
    Then I should see "testOrg1"
    And I should see "Properties"
    And I should see "Users"
    And I should see "Roles"
    And I should see "Connected services"
    And I should see "Create role"
    And I should see "Connect to service"
    And I should see "Edit attributes"
    And I should see "Manage users"
    And I should see "Edit properties"
    And I should see "View history"
    And I should see "Delete organization"

#  Scenario: Navigate to organization properties
#    Given I am on "/"
#    And I wait for "testOrg1" to appear
#    Then I follow "testOrg1"
#    And I wait for "Properties" to appear
    When I follow "Properties"
    Then I should see "Ez a szervezet teszteléshez készült. Jól tesztelve is lesz vele az alkalmazás."
    And I should see "Roles"
    And I should see "Test role 1"
    And I should see "Test role 2"

#  Scenario: Organization properties, role accordion
#    Given I am on "/"
#    And I wait for "Welcome to" to appear
#    Then I follow "testOrg1"
#    And I wait for "testOrg1" to appear
#    And I follow "Properties"
#    And I should see "Test role 1"
#    And I should see "Test role 2"
    And I should not see "Permissions"
    And I should not see "Members"
    When I click on accordion "Test role 1"
    Then I should see "Permissions"
    And I should see "Members"
    And I should see "Student Student"
    When I click on accordion "Test role 1"
    Then I should not see "Permissions"
    And I should not see "Members"
    And I should not see "Student Student"

#  Scenario: Navigate to organization users
#    Given I am on "/"
#    When I wait for "testOrg1" to appear
#    Then I follow "testOrg1"
#    And I wait for "Users" to appear
    When I follow "Users"
    Then I should see "Change roles"
    And I should see "Proposal"
    And I should see "Revoke"
    And I should see "Message"
    And I should see "Remove"
    And I should see "Invite"
    And I should see "Managers"
    And I should see "Users"

#  Scenario: Organization users tables and buttons
#    Given I am on "/"
#    When I wait for "testOrg1" to appear
#    Then I follow "testOrg1"
#    When I wait for "Users" to appear
#    And I follow "Users"
#    Then I wait for "Managers" to appear
    And I should see a table with 2 row
    And I should see a table with 4 rows
    When I fill in "Search users" with "nolocal"
    Then I should see a table with  2 rows

#  Scenario: Navigate to organization roles
#    Given I am on "/"
#    When I wait for "testOrg1" to appear
#    Then I follow "testOrg1"
#    When I wait for "Roles" to appear
    When I follow "Roles"
    Then I should see "New role"

#  Scenario: Navigate to organization connected services
#    Given I am on "/"
#    When I wait for "testOrg1" to appear
#    Then I follow "testOrg1"
#    When I wait for "Connected services" to appear
    When I follow "Connected services"
    When I wait for "Connected services" to appear
    Then I should see "Connected entitlement packs" in the ".accordion-header" element
    And I should see "testService1"

    When I click on accordion "testService1"
    Then I should see "Permission sets"
    And I should see "Entitlement Package 1"

    When I click on accordion "Entitlement Package 1"
    Then I should see "Details"
    And I should see "this is a short desc."
    And I should see "Permissions"
    And I should see "Permission 1"
    And I should see "Permission 3"
    And I should not see "Permission 4"
