@showadmin

Feature: When I am an admin
		I want to see admin features

	Background:
       Given I am on "/"
        And I should see "employee@project.local"

	Scenario: Navigate admin Attributes page
	   Given I am on "/"
		Then I wait for "Admin" to appear
	    Then I follow "Admin"
		And I wait for "Attributes" to appear
	    Then I should see "Attribute specifications"
	     And I should see "Principals"
	     And I should see "Entity IDs"
	     And I should see "Contact"
		When I click on accordion "Favourite coffee of the principal"
		Then I should see "Description"
		And I should see "URI"
		And I should see "Syntax"
		And I should see "Maintainer"
		And I should see "Multivalue"

	Scenario: Navigate to principals
	   Given I am on "/"
		 And I wait for "Admin" to appear
	    Then I follow "Admin"
		 And I wait for "Principals" to appear
	     And I follow "Principals"
	    Then I should see "Student Student"
	     And I should see "Employee Employee"

        Scenario: Navigate to entity ids
	   Given I am on "/"
		 And I wait for "Admin" to appear
	    Then I follow "Admin"
		 And I wait for "Entity IDs" to appear
	     And I follow "Entity IDs"
	    Then I should see "https://example.com/ssp"
		And I should see "https://test.com/ssp"
        When I click on accordion "https://example.com/ssp"
          Then I should see "Type"
          And I should see "Email"