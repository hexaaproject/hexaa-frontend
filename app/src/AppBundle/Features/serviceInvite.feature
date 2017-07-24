@serviceinvitation
Feature: When I go to services
  As an authenticated user
  I want to invite manager to my service

  Background:
    Given I prepare a DELETE request on "/all"
    When I send the request
    Then I should receive a 204 response
    Given I prepare a GET request on "/setup"
    When I send the request
    And I should receive a 204 response
    Given mailhog inbox is empty
    And I am on "/"
    And I should see "employee@project.local"
     And I follow "testService1"
    Then I wait for "Managers" to appear
     And I follow "Managers"
    Then I wait for "Invite" to appear
     And I press "Invite"
     And I wait for "Create invitation" to appear

  Scenario: Invite a manager to Service
            with url
     When I fill in "Landing url" with "https://www.invite.hu"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     Then I should not see "Your invitation is done"
      And I wait for "Managers" to appear

  Scenario: Invite a manager to Service
            with limit
     When I fill in "Limit" with "3"
     And I press "Create"
     And I wait for "Your invitation is done" to appear
     And I press "Done"
     Then I should not see "Your invitation is done"
      And I wait for "Managers" to appear

  Scenario: Invite a manager to Service
            with Date check
    When I select "2017" from "service_user_invitation_start_date_year"
    And I select "Jul" from "service_user_invitation_start_date_month"
    And I select "18" from "service_user_invitation_start_date_day"
    And I select "2017" from "service_user_invitation_end_date_year"
    And I select "Jun" from "service_user_invitation_end_date_month"
    And I select "17" from "service_user_invitation_end_date_day"
    And I press "Create"
    Then I should see "Invalid date range."

  Scenario: Invite and send email to "student@project.local"
     When I press "Create"
     And I wait for "Your invitation is done" to appear
     Then I fill in the following:
      | Send invitation by email | student@project.local |
      | Message                  | Legyél manager nálunk! |
     And I press "Done"
     And I wait for "Invitations sent succesfully." to appear
    Then there is a mail to "student@project.local"
    Then there is a mail from "no_reply@hexaa.eduid.hu"
    Then there is a mail that contains "Legyél manager nálunk!"

  Scenario: Invalid email addresses
    When I press "Create"
    And I wait for "Your invitation is done" to appear
    Then I fill in the following:
      | Send invitation by email | rosszemailcím |
    And I press "Done"
    And I wait for "does not comply with RFC 2822" to appear
    Then there are 0 mails

  Scenario: Invalid landing url
    When I fill in "Landing url" with "invalid url"
     And I press "Create"
    Then I should see "Please enter a valid URL."

