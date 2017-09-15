@showadmin
@sendmails

Feature: When I am an admin
		and I want to send emails to service or organization managers

	Background:
       Given I am on "/"
        And I should see "employee@project.local"
		And I wait for "Admin" to appear
		Then I follow "Admin"
		And I wait for "Contact" to appear
		Then I follow "Contact"

	Scenario: Navigate admin Contact page
	    Then I should see "Service Managers"
	     And I should see "Organization Managers"
	     And I should see "Service"
	     And I should see "Title"
		 And I should see "Message"

	@javascript
	Scenario: Send email to Service Managers
	#	When I fill in "typeahead_serv_managers_contact_service" with "test"
	#	And I wait for the suggestion box to appear
	#	Then I should see "testService1"
	#	When I type "test" into search box
	#	And I wait for the suggestion box to appear
	#	Then I should see "testService1"
	#	When I select "testService1" after filling "test" in "typeahead_serv_managers_contact_service"
	#	When I fill in "typeahead_serv_managers_contact_service" with "test"
	#	And I wait 1 seconds
	#	Then I select autosuggestion option "testService1"
	#	And I wait 1 seconds
	    Then I fill in dropdown "typeahead_serv_managers_contact_service" with "testService"
	#	When I fill in "typeahead_serv_managers_contact_service" with "testService1"
		And I fill in "serv_managers_contact_managersTitle" with "Próba üzenet"
		And I fill in "serv_managers_contact_managersMessage" with "Ez az üzenet tesztelés céljából készült."
		And I press "servManagersSend"
		And I wait for "Message sent succesfully." to appear
		Then there is a mail to "employee@project.local"
		Then there is 1 mail
		Then there is a mail from "employee@project.local"
		Then there is a mail that contains "Ez az üzenet tesztelés céljából készült."

	Scenario: Navigate to Organization Managers inside Contact page
		When I press "rightbutton"
		Then I should see "Organization"
		  And I should see "Title"
		  And I should see "Message"

	Scenario: Send email to Organization Managers
		When I press "rightbutton"
		Then I fill in "typeahead_org_managers_contact_organization" with "testOrg1"
		And I fill in "org_managers_contact_orgManagersTitle" with "Próba üzenet2"
		And I fill in "org_managers_contact_orgManagersMessage" with "Ez az üzenet ismét tesztelés céljából készült."
		And I press "orgManagersSend"
		And I wait for "Message sent succesfully." to appear
		Then there is a mail to "employee@project.local"
		Then there is a mail to "employee@server.hexaa.eu"
		Then there is a mail that contains "Ez az üzenet ismét tesztelés céljából készült."

	Scenario: Failed service name
		When I fill in "typeahead_serv_managers_contact_service" with "nincsilyen"
		And I fill in "Title" with "Próba üzenet"
		And I fill in "Message" with "Ez az üzenet rossz service megadásának tesztelésére szolgál."
		And I press "servManagersSend"
		Then I wait for "There was some failure!" to appear
		Then there are 3 mail