@org
Feature: When I go to organizations
		 As an authenticated user
		 I want to see all my org with the relevant informations

	Background:
	   Given I am on "/Shibboleth.sso/Login"
		Then I wait for "Username" to appear
		When I fill in "username" with "e"
		 And I fill in "password" with "pass"
		 And I press "Login"
		Then I should be on "/"
		Then I wait for "Welcome to" to appear
		 And I should see "employee@project.local"

	Scenario: Navigate to Add org
	   Given I am on "/"
	    Then I wait for "Add organization" to appear
	    When I follow "Add organization"
	    Then I should be on "/organization/addStepOne"
	     And I should see "Create virtual organization"

	Scenario: Add organization step one: base data
	   Given I am on "/organization/addStepOne"
	     And I wait for "Create virtual organization" to appear
	     And I should see a "Next" button
	     And I should see 2 "input" elements
	     And I should see "Add meg a szervezeted alapadatait."
	     And a field should contain placeholder "Organization name"
	     And a field should contain placeholder "Description"

	Scenario: Navigate Org wizard step two: roles
	   Given I am on "/organization/addStepTwo"
	     And I wait for "Szerepkörök" to appear
	     And I should see "Tagok listázásának tiltása a szervezetben"
	     And I should see "Tagok listázásának tiltása a szerepkörökben"

	Scenario: Add organization step three: permissions
	   Given I am on "/organization/addStepThree"
	     And I wait for "Szolgáltatás" to appear
	     And I should see "A szolgáltatás összes jogosultsága a Tagok szerepkörben lesz elérhető."

	Scenario: Add organization step four: people
	   Given I am on "/organization/addStepFour"
	     And I wait for "Meghívó" to appear

	Scenario: Add organization step five: summary
	   Given I am on "/organization/addStepFive"
	     And I wait for "Összegzés" to appear

	Scenario: Add organization step five: success
	   Given I am on "/organization/addStepSix"
	     And I wait for "Siker" to appear
