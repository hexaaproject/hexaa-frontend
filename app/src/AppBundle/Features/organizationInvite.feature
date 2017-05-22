@org @invitation
Feature: When I go to organizations
  As an authenticated user
  I want to invite user to my organization

  Background:
    Given mailhog inbox is empty
     And I am on "/Shibboleth.sso/Login"
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
     And I press "Invite"
     And I wait for "Create invitation" to appear

  Scenario: Invite a user to Org
            with landing url
            with Limit
            and to "Test role 1"
    When I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     And I should not see "Your invitation is done"

    When I press "Invite"
     And I wait for "Create invitation" to appear
     And I fill in "Landing url" with "https://www.hup.hu"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     And I should not see "Your invitation is done"

    When I press "Invite"
     And I wait for "Create invitation" to appear
     And I fill in "Limit" with "1"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     And I should not see "Your invitation is done"

    When I press "Invite"
     And I wait for "Create invitation" to appear
     And I select "Test role 1" from "organization_user_invitation_role"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     And I should not see "Your invitation is done"

  Scenario: Invite and send email to "alice@example.com"
    When I select "Test role 1" from "organization_user_invitation_role"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
    When I fill in the following:
      | Send invitation by email | alice@example.com   |
      | Message                  | Gyere hozzám tagnak |
     And I press "Done"
     And I wait for "Invitations sent succesfully." to appear
    Then there is a mail to "alice@example.com"
    Then there is 1 mail
    Then there is a mail from "no_reply@hexaa.eduid.hu"
    Then there is a mail that contains "Gyere hozzám tagnak"

  Scenario: Invalid email addresses
    When I select "Test role 1" from "organization_user_invitation_role"
    And I press "Create"
    And I wait for "Your invitation is done" to appear
    When I fill in the following:
      | Send invitation by email | alice@example.com, nemjóemailcím   |
    And I press "Done"
    And I wait for "does not comply with RFC 2822" to appear
    Then there are 0 mails

  Scenario: Invalid landing url
    When I fill in "Landing url" with "invalid url"
     And I press "Create"
    Then I should see "Please enter a valid URL."

  Scenario: Invalid date
    When I select "2017" from "organization_user_invitation_start_date_year"
     And I select "Feb" from "organization_user_invitation_start_date_month"
     And I select "1" from "organization_user_invitation_start_date_day"
     And I select "2017" from "organization_user_invitation_end_date_year"
     And I select "Jan" from "organization_user_invitation_end_date_month"
     And I select "1" from "organization_user_invitation_end_date_day"
     And I press "Create"
    Then I should see "Invalid date range."
