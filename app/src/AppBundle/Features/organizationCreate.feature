@org
@create
Feature: When I go to create organization
  As an authenticaed user
  I can create an organization

  Scenario: Navigate to create organization first step page
    Given I am on "/"
    Then I follow "Add organization"
    And I wait for "Enter your organization's main parameters" to appear
    And a field should contain placeholder "Name of organization"
    And a field should contain placeholder "Description of organization"

    # Fill the first step
    When I fill in "Name of organization" with "Teszt szervezet"
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
    Then I should see "Success"
    #And I should see the connected service, and permissions in members role
    And I should see "Get your new organziation"
    And there is a mail to "user@example.com"

  Scenario: Step backs in steps
  Scenario: Required fields validation
