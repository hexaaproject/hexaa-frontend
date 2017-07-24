@serv

Feature: When I go to a specific service
		I want to see service properties

	Background:
		Given I prepare a DELETE request on "/all"
		When I send the request
		Then I should receive a 204 response
		Given I prepare a GET request on "/setup"
		When I send the request
		And I should receive a 204 response
        Given I am on "/"
        And I should see "employee@project.local"
        And I should see "testService1"


	Scenario: Navigate to service show page
	   Given I am on "/"
		Then I wait for "testService1" to appear
	    Then I follow "testService1"
		And I wait for "testService1" to appear
	    Then I should see "testService1"
	     And I should see "Properties"
	     And I should see "Managers"
	     And I should see "Attributes"
	     And I should see "Permissions"
	     And I should see "Permissions sets"
	     And I should see "Connected Organizations"
	     And I should see "Create permission"
	     And I should see "Invite organization"
	     And I should see "Add attribute specification"
	     And I should see "Invite manager"
	     And I should see "Contact admin"
		 And I should see "View history"
		 And I should see "Delete service"

	Scenario: Navigate to service properties
	   Given I am on "/"
		 And I wait for "testService1" to appear
	    Then I follow "testService1"
		 And I wait for "Properties" to appear
	     And I follow "Properties"
	    Then I should see "Properties"
	     And I should see "Ez a szolgáltatás teszteléshez készült."
	     And I should see "Owner details"
	     And I should see "Privacy Information"

        Scenario: Edit service properties
	   Given I am on "/"
		 And I wait for "testService1" to appear
	    Then I follow "testService1"
		 And I wait for "Properties" to appear
	     And I follow "Properties"
	    Then I should see "Properties"
	    When I press "pencil-1"
	    And I fill in "Home page" with "https://service.com/4"
	    And I press "done"
	    Then I should see "https://service.com/4"

	Scenario: Navigate to service managers
	   Given I am on "/"
	    When I wait for "testService1" to appear
	    Then I follow "testService1"
	     And I wait for "Managers" to appear
	     And I follow "Managers"
	    Then I should see "Remove"
	     And I should see "Invite"
	     And I should see "Managers"

        Scenario: Navigate to service attributes
	   Given I am on "/"
	    When I wait for "testService1" to appear
	    Then I follow "testService1"
	     And I wait for "Attributes" to appear
	     And I follow "Attributes"
	    Then I should see "Remove"
	     And I should see "Add"
	     And I should see "Used attribute specification"
	    

	Scenario: Service managers tables
	   Given I am on "/"
	    When I wait for "testService1" to appear
	    Then I follow "testService1"
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
	    When I wait for "testService1" to appear
	    Then I follow "testService1"
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

