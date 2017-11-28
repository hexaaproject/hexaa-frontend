@serv
@create
Feature: When I go to create service
  As an authenticaed user
  I can create a service

  Background:
    Given I am on "/"
    And I should see "employee@project.local"

  Scenario: Navigate to create service first step page
    Given I am on "/"
    Then I follow "Claim service"
    And I wait for "Enter your service's main parameters" to appear
    And a field should contain placeholder "Name of service"
    And a field should contain placeholder "Description of service"
    And a field should contain placeholder "URL of service"
    And a field should contain placeholder "Start typing entity ID"

    # Fill the first step
    When I fill in "Name of service" with "Test service 4"
    And I fill in "Description of service" with "Ez egy teszt szolgáltatás"
    And I fill in dropdown "typeahead_service_entityid" with "https://test.com/ss"
    And I press "next-1"
    Then I should see "Create your entitlements of your service"

    # Fill the second step
    When I fill in "service_entitlement" with "permission4"
    And I fill in "service_entitlementplus1" with "permission5"
    And I press "next"
    Then I should see "Select which type of SP contact would you like to send email"
    And I press "Finish"
    Then I should see "Your service is done."
    And I should see "Generated token"
    And I should see "Get your new service"

    # Follow link
    When I follow "Get your new service"
    Then I should see "Test ser..."
    And I should see "Permissions"
    And I should see "Permissions sets"
    When I follow "Permissions"
    Then I should see "Permissions" in the ".accordion-header" element
    And I should see "permission4"
    And I should see "permission5"
    When I click on accordion "permission4"
    Then I should see "Description"
    And I should see "URI"
    When I follow "Permissions sets"
    Then I should see "Permission sets" in the ".accordion-header" element
    And I should see "default"
    When I click on accordion "default"
    Then I should see "permission4"
    And I should see "permission5"

  Scenario: Delete service
    Given I am on "/"
    Then I follow "Test service 4"
    And I wait for "Test ser..." to appear
    When I click the "#deleteService" element
    Then I wait for "Are you sure?" to appear
    When I press "Delete service"
    Then I am on "/"
    And I should not see "Test service 4"
