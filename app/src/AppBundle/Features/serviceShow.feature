@serv

Feature: When I go to a specific service
		I want to see service properties

	Background:

	   Given I am on "/Shibboleth.sso/Login"
		Then I wait for "Username" to appear
		When I fill in "username" with "e"
		 And I fill in "password" with "pass"
		 And I press "Login"
		Then I should be on "/"
		Then I wait for "Welcome to" to appear
		Then I should see "employee@project.local"
		 And I should see "testService5"

	Scenario: Navigate to service show page
	   Given I am on "/"
		Then I wait for "testService5" to appear
	    Then I follow "testService5"
		And I wait for "testService5" to appear
	    Then I should see "testService5"
	     And I should see "Properties"
	     And I should see "Managers"
	     And I should see "Attributes"
	     And I should see "Permissions"
	     And I should see "Permissions sets"
	     And I should see "Connected Organizations"
	     And I should see "Create permission"
	     And I should see "Invite organiztaion"
	     And I should see "Add attribute specification"
	     And I should see "Invite manager"
	     And I should see "Contact admin"
             And I should see "View history"
             And I should see "Delete service"

	Scenario: Navigate to service properties
	   Given I am on "/"
		 And I wait for "testService5" to appear
	    Then I follow "testService5"
		 And I wait for "Properties" to appear
	     And I follow "Properties"
	    Then I should see "Properties"
	     And I should see "Ez a szolgáltatás teszteléshez készült."
	     And I should see "Owner details"
	     And I should see "Privacy Information"

        Scenario: Edit service properties
	   Given I am on "/"
		 And I wait for "testService5" to appear
	    Then I follow "testService5"
		 And I wait for "Properties" to appear
	     And I follow "Properties"
	    Then I should see "Properties"
                When I press "create"
                 And I fill in "Name" with "testService6"
                 And I press "done"
                Then I should see testService6 

	Scenario: Navigate to service managers
	   Given I am on "/"
	    When I wait for "testService5" to appear
	    Then I follow "testService5"
	     And I wait for "Managers" to appear
	     And I follow "Managers"
	    Then I should see "Remove"
	     And I should see "Invite"
	     And I should see "Managers"

        Scenario: Navigate to service attributes
	   Given I am on "/"
	    When I wait for "testService5" to appear
	    Then I follow "testService5"
	     And I wait for "Attributes" to appear
	     And I follow "Attributes"
	    Then I should see "Remove"
	     And I should see "Add"
	     And I should see "Used attribute specification"
	    

	Scenario: Service managers tables
	   Given I am on "/"
	    When I wait for "testService5" to appear
	    Then I follow "testService5"
	    When I wait for "Managers" to appear
	     And I follow "Managers"
	    Then I wait for "Managers" to appear
	     And I should see a table with 1 row
	    When I fill in "Search manager" with ""
	    Then I should see a table with 1 rows
	    When I fill in "Search manager" with "Dolgozo"
	    Then I should see the following table portion
	       | No matching records found |


	Scenario: Navigate to service permissions
	   Given I am on "/"
	    When I wait for "testService5" to appear
	    Then I follow "testService5"
	    When I wait for "Permissions" to appear
	     And I follow "Permissions"
	    When I wait for "Permissions" to appear
	    Then I should see "Permissions" in the ".accordion-header" element
	     And I should see "Permission 1"
	     And I should see "Permission 2"
             And I should see "Permission 3"

	    When I click on accordion "Permission 1"
		Then I should see "Description"
		 And I should see "URI"

