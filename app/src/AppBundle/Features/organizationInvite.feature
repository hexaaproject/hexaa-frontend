@org
Feature: When I go to organizations
  As an authenticated user
  I want to invite user to my organization

  Background:
    Given I am on "/Shibboleth.sso/Login"
    Then I wait for "Username" to appear
    When I fill in "username" with "e"
    And I fill in "password" with "pass"
    And I press "Login"
    Then I should be on "/"
    Then I wait for "Welcome to" to appear
    And I should see "employee@project.local"
    And I follow "testOrg1"
    Then I wait for "Users" to appear
    And I follow "Users"
    Then I wait for "Invite" to appear

  Scenario: Invite a user to "Test role 1"
    When I press "Invite"
    And I wait for "Create invitation" to appear
    When I fill in the following:
      | Message     | Gyere hozzám tagnak |
      | Limit       | 1                   |
      | Landing url | http://www.hup.hu   |
    When I select "Test role 1" from "organization_user_invitation_role"
    When I select "Magyar" from "organization_user_invitation_locale"
    And I press "Next"
    And I wait for "Start of accept period" to appear

    When I select "2017" from "organization_user_invitation_start_date_year"
    And I select "Mar" from "organization_user_invitation_start_date_month"
    And I select "1" from "organization_user_invitation_start_date_day"
    And I select "2017" from "organization_user_invitation_end_date_year"
    And I select "Apr" from "organization_user_invitation_end_date_month"
    And I select "1" from "organization_user_invitation_end_date_day"
          # TODO, 500-zal száll el a backend And I fill in "emails" with "alma@gmail.com, korte@gmail.com"
    And I press "submitform"
    And I wait for "Your invitation is ready" to appear

    When I press "Done"
    And I wait for 1 seconds
    Then I should not see "Your invitation is ready"
