@org
Feature: When I go to organizations
		 As an authenticated user
		 I want to see all my org with the relevant informations

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
	    Then I should be on "/organization/addStepOne"
	     And I should see "Create virtual organization"

	Scenario: Add organization step one: base data
	   Given I am on "/organization/addStepOne"
	     And I should see "Create virtual organization"
	     And I should see a "Next" button
	     And I should see 2 "input" elements
	     And I should see "Add meg a szervezeted alapadatait."
	     And a field should contain placeholder "Organization name"
	     And a field should contain placeholder "Description"

	@current
	Scenario: Navigate Org wizard step two: roles
	   Given I am on "/organization/addStepTwo"
	     And I should see "Szerepek"
	     And I should see "Alapértelmezett"
	     And I should see "ebbe a szerepkörbe kerülnek azok a meghívott felhasználók, akikhez meghíváskor nem rendelünk szerepkört."
	     And I should see "Felhasználók szerepkörök szerinti izolálása"
	     And I should see "Később több szerepet is létrehozhat a Szerepek menüpontban"

	Scenario: Add organization step three: permissions
	   Given I am on "/organization/addStepThree"
	     And I should see "Token"
	     And I should see "Később több szolgáltatást is kapcsolhat szervezetéhez a Szolgáltatások menüpontban"

	Scenario: Add organization step four: people
	   Given I am on "/organization/addStepFour"
	     And I should see "Meghívás"
	     And I should see "Szerepkör"
	     And I should see "Felhasználók izolálása"
	     And I should see "Később felhasználót is meghívhat szervezetéhez"
