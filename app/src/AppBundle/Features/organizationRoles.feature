@org
@roles

Feature: When I go to organization's roles
  As an authenticaed user
  I can manage the organization roles

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testOrg2"
    Then I wait for "Roles" to appear
    And I follow "Roles"

  Scenario: I create new Role, and check out the want to be a member, cancel the form
    Given I should see "New role"
    When I press "New role"
    Then I should see "Create role"
    And I fill in "Name" with "Brand new role"
    And I press "done"
    Then I should see "Brand new role"

    When I press "New role"
    Then I should see "Create role"
    And I fill in "Name" with "Second brand new role"
    And I uncheck the "organization_role_wantToBeAMember" checkbox
    And I press "done"
    Then I should see "Second brand new role"

    When I press "New role"
    And I fill in "Name" with "Third brand new role"
    And I press "clear"
    Then I should not see "Create role"

  Scenario: Delete role
    Given I should see "Second brand new role"
    When I click on accordion "Second brand new role"
    Then I should see "Permissions"
#    And I click the "i[data-id=6]" element
#    Then I should see "Are you sure?"
#    When I press "Delete"
#    Then I should see "Success"
#    And I should not see "Second brand new role"

