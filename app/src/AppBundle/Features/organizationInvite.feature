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

	@wip
	Scenario: Invite a user to "Test role 1"
	    When I press "Invite"
		 And I wait for "Meghívó készítés" to appear
	    When I fill in the following:
            | Üzenet           | Gyere hozzám tagnak |
            | Limit            | 1                   |
            | Átirányítási url | www.hup.hu          |
        When I select "Test role 1" from "organization_user_invitation_roles"
        When I select "Magyar" from "organization_user_invitation_language"
         And I press "Tovább"
		 And I wait for "Érvényesség kezdete" to appear
		
		When I select "2017" from "organization_user_invitation_begin_year"
		 And I select "Mar" from "organization_user_invitation_begin_month"
		 And I select "1" from "organization_user_invitation_begin_day"
		 And I select "2017" from "organization_user_invitation_end_year"
		 And I select "Apr" from "organization_user_invitation_end_month"
		 And I select "1" from "organization_user_invitation_end_day"
  #        And I press "Tovább"
		#  And I wait for "Befejezés" to appear
		# Then I should see "Meghívód elkészült"
	 #     And the "Link" field should contain "accept"
		# When I press "Befejezés"
		# Then I should not see "Meghívód elkészült"
