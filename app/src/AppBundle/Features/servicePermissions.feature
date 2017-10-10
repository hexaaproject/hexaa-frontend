@serv
@permissions
Feature: When I go to service's permissions
  As an authenticated user
  I can create service permission

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    And I follow "testService1"
    Then I wait for "Permissions" to appear
    And I follow "Permissions"

  Scenario: I create new Permission, cancel the form
    Given I should see "Create"
    When I press "Create"
    Then I should see "Create permission"
    And I fill in "Name" with "Példa-permission"
    And I fill in "URI" with "álma:"
    And I press "done"
    Then I should see "Példa-permission"
    When I click on accordion "Példa-permission"
    Then I should see "URI"
    And I should see "alma:"


    When I press "Create"
    And I fill in "Name" with "Permission 4"
    And I press "cancel"
    Then I should not see "Permission 4"
