@htdm
Feature: Behat hexaa data manager tesztek
	
	@smartStep
	Scenario: emtpy hexaa data
	   Given I prepare a DELETE request on "/all"
		When I send the request
		Then I should receive a 204 response

	@smartStep
	Scenario: setup the basic hexaa test data
	   Given I prepare a GET request on "/setup"
		When I send the request
		Then I should receive a 204 response