@admin
@attributes
Feature: When I go to admin's attributes
  As an admin
  I can create attribute

  Background:
    Given I am on "/"
    And I should see "employee@project.local"
    Then I wait for "Admin" to appear
    And I follow "Admin"

  Scenario: I create new attribute, cancel the form
    Given I should see "Create"
    When I press "Create"
    Then I should see "Create Attribute Specification"
    And I fill in "Name" with "Size"
    And I fill in "OID" with "test:attribute:size"
    And I check the "user" radio button
    And I check the "string" radio button
    And I check the "true" radio button
    And I press "done"
    Then I should see "Size"
    When I click on accordion "Size"
    Then I should see "test:attribute:size"
    And I should see "user"
    And I should see "string"


    When I press "Create"
    And I fill in "Name" with "Color"
    And I press "clear"
    Then I should not see "Color"
