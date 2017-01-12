@org
Feature: When I go to organizations
		 As an authenticated user
		 I want to see all my org with the relevant informatins

	Background:
	   Given I am on "/Shibboleth.sso/Login"
		Then I should see "Username"
		When I fill in "username" with "e"
		 And I fill in "password" with "pass"
		 And I press "Login"
		Then I should be on "/"
		 And I should see "Welcome to"
		 And I should see "employee@project.local"

	Scenario: Navigate to Add org
	   Given I am on "/"
	    Then I should see "Add organization"
	    When I follow "Add organization"
	    Then I should be on "/organization/add"
	     And I should see "Create virtual organization"