@org
@create
Feature: When I go to create organization
  As an authenticaed user
  I can create an organization

  Background:

    Given I am on "/Shibboleth.sso/Login"
    Then I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "pass"
    And I press "Login"
    Then I should be on "/"
    Then I wait for "Welcome to" to appear
    Then I should see "employee@project.local"
    And I should see "Add organization"

  @wip
  Scenario: Navigate to create organization first step page
    Given I am on "/"
    Then I follow "Add organization"
    And I wait for "Add meg a szervezeted alapadatait" to appear
    And a field should contain placeholder "Szervezet neve"
    And a field should contain placeholder "Szervezet leírása"

    # Fill the first step
    When I fill in "Szervezet neve" with "Teszt szervezet"
    And I fill in "Szervezet leírása" with "Ez egy teszt szervezet"
    And I press "next-1"
    Then I should see "Add meg az alapértelmezett szerepkör nevét"

    # Fill the second step
    When I fill in "Alapértelmezett szerepkör" with "members"
    And I press "next-2"
    Then I should see "Szolgáltatás összekapcsolása"

    #Fill the third step
    And a field should contain placeholder "Szolgáltatás token-je"
    When I press "next-3"
    Then I should see "Tagok meghívása az alapértelmezett szerepbe"

    #Fill the fourth step
    When I fill in "Meghívottak email címei" with "user@example.com"
    And I press "next-4"
    Then I should see "Siker"

  Scenario: Step backs in steps
