@htdm
Feature: Behat hexaa data manager tesztek
	
	@smartStep
	Scenario: Delete all hexaa data
	   Given I prepare a DELETE request on "/all"
		When I send the request
		Then I should receive a 204 response

	@smartStep
	Scenario: Setup the basic hexaa test data
	   Given I prepare a GET request on "/setup"
		When I send the request
		Then I should receive a 200 response