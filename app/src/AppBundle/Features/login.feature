Feature: Login
  In order to see the main page
  As a simple user
  I need to login

  Scenario: Login
    Given I am on "/Shibboleth.sso/Login"
    Then I reload the page
    And I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "pass"
    And I press "Login"
    Then I wait for "Welcome to" to appear
    Then I should be on "/"
    And I should see "employee@project.local"

  Scenario: Failed login
    Given I am on "/Shibboleth.sso/Login"
    And I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "not too good pass"
    And I press "Login"
    Then I should see "Incorrect username or password"
