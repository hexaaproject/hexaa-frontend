@org
@show
Feature: When I go to a specific organization
		 As the owner of organization
		 I want to see the organization all properties

	Background:
	   # Given empty hexaa data
    #    Given setup the basic hexaa test data
	   Given I am on "/Shibboleth.sso/Login"
		Then I should see "Username"
		When I fill in "username" with "e"
		 And I fill in "password" with "pass"
		 And I press "Login"
		Then I should be on "/"
		 And I should see "Welcome to"
		 And I should see "employee@project.local"
		 And I should see "testOrg1"

	Scenario: Navigate to organization show page
	   Given I am on "/"
	    When I follow "testOrg1"
	    Then I should see "testOrg1"
	     And I should see "Properties"
	     And I should see "Users"
	     And I should see "Roles"
	     And I should see "Connected services"
	     And I should see "Create role"
	     And I should see "Connect to service"
	     And I should see "Edit attributes"
	     And I should see "Manage users"
	     And I should see "Edit properties"
	     And I should see "View history"
	     And I should see "Delete organization"

	Scenario: Navigate to organization properties
	   Given I am on "/"
	    When I follow "testOrg1"
	     And I follow "Properties"
	    Then I should see "Ez a szervezet teszteléshez készült. Remélhetőleg majd jól tesztelve is lesz vele az alkalmazás."
	    And I should see "Roles"
	    And I should see "A szerepköröket a szerepkörök menüpont alatt tudod szerkeszteni."

	Scenario: Navigate to organization users
	   Given I am on "/"
	    When I follow "testOrg1"
	     And I follow "Users"
	    Then I should see "Change roles"
	     And I should see "Proposal"
	     And I should see "Revoke"
	     And I should see "Message"
	     And I should see "Remove"
	     And I should see "Invite"
	     And I should see "Managers"
	     And I should see "Users"

	Scenario: Navigate to organization roles
	   Given I am on "/"
	    When I follow "testOrg1"
	     And I follow "Roles"
	    Then I should see "New role"
	     And I should see "Add role to user"
	     And I should see "Invite"

	Scenario: Navigate to organization connected services
	   Given I am on "/"
	    When I follow "testOrg1"
	     And I follow "Connected services"
	    Then I should see "New connection"
