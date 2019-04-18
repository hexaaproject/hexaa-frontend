@organization
@accessdenied
  @wip
Feature: When I go to create organization
  As an authenticaed user
  And revoke manager status
  Then get access denied all write action

  Background:
    Given I am on "/"
    And I should see "employee@project.local"

#  Scenario: Step backs in steps
#  Scenario: Required fields validation
  Scenario: Navigate to create organization first step page
#    Given I am on "/"
    Then I follow "Add organization"
    And I wait for "Enter your organization's main parameters" to appear
    And a field should contain placeholder "Name of organization"
    And a field should contain placeholder "Description of organization"

  # Fill the first step
    When I fill in "Name of organization" with "Access Denied teszt szervezet"
    And I fill in "Description of organization" with "Ez egy teszt szervezet"
    And I press "next-1"
    Then I should see "Enter the name of default role"

  # Fill the second step
    When I fill in "Name of default role" with "members"
    And I press "next-2"
    Then I should see "Connect service with token"

  #Fill the third step
    And a field should contain placeholder "Token of service"
    When I press "next-3"
    Then I should see "Invite members to default role"

  #Fill the fourth step
    When I fill in "Invitation email addresses" with "user@example.com"
    And I press "Finish"
    Then I should see "Your organization is done."
  #And I should see the connected service, and permissions in members role
    And I should see "Get your new organization"
    And there is a mail to "user@example.com"

    #add role
    Given I am on "/"
    Then I follow "Access Denied teszt szerv..."

# revoke manager
#  Scenario: Try to delete, access denied
    When I am on "organization/5/users"
    And I click the "employee@project.local" behat target
    And I press "Revoke"
    And I wait for "Revoke manager status" to appear
    And I click the "revoke" behat target

    #try to delete members role
    When I am on "organization/5/role/5/delete"
    Then I should see "Access denied."

  # try to delete whole organization
    When I am on "organization/5/delete"
    Then I should see "Access denied."
