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
    And I fill in "URI" with "test:attribute:size"
    And I select "user" from "admin_attribute_spec_attributeSpecMaintainer"
    And I select "string" from "admin_attribute_spec_attributeSpecSyntax"
    And I select "true" from "admin_attribute_spec_attributeSpecIsMultivalue"
    And I press "done"
    Then I should see "Size"
    When I click on accordion "Size"
    Then I should see "test:attribute:size"
    And I should see "user"
    And I should see "string"


    When I press "Create"
    And I fill in "Name" with "Color"
    And I press "cancel"
    Then I should not see "Color"
