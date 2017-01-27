Feature: Login
	In order to see the main page
	As a simple user
	I need to login

	Scenario: Login
	   Given I am on "/Shibboleth.sso/Login"
		 And I wait for 12 seconds
		Then I should see "Username"
		When I fill in "username" with "e"
		 And I fill in "password" with "pass"
		 And I press "Login"
		 And I wait for 12 seconds
		Then I should be on "/"
		 And I should see "Welcome to"
		 And I should see "employee@project.local"

	Scenario: Failed login
	   Given I am on "/Shibboleth.sso/Login"
		 And I wait for 3 seconds
		Then I should see "Username"
		When I fill in "username" with "e"
		 And I fill in "password" with "not too good pass"
		 And I press "Login"
		 And I wait for 3 seconds
		Then I should see "Incorrect username or password"
