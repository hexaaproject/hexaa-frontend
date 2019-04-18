@login
Feature: Login
  In order to see the main page
  As a simple user
  I need to login

  Scenario: Login
    Given I am on "/"
    When I press "login_button"
    #Then I reload the page
    And I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "pass"
    And I press "Login"
    Then I wait for "Welcome to" to appear
    Then I should be on "/"
    And I should see "employee@project.local"

  Scenario: Failed login
    Given I am on "/"
    #Given I am on "/Shibboleth.sso/Login"
    When I press "login_button"
    And I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "not too good pass"
    And I press "Login"
    Then I should see "Incorrect username or password"

  @logout
  Scenario: Logout
    Given I am on "/"
    When I press "login_button"
#Then I reload the page
    And I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "pass"
    And I press "Login"
    Then I wait for "Welcome to" to appear
    Then I should be on "/"
    And I should see "employee@project.local"
    When I press "logout_button"
    Then I should not see "employee@project.local"
