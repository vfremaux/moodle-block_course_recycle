@block @block_course_cycle @non_core_block
Feature: Adding and configuring course Cycle blocks
  In order to have custom blocks on a page
  As admin
  I need to be able to create, configure and change Course Cycle blocks

  @javascript
  Scenario: Configuring the Course Cycle block with Javascript on
    Given I log in as "admin"
    When I click on "Turn editing on" "link" in the "Administration" "block"
    And I add the "Course Cycle" block
    And I configure the "(new Course Cycle block)" block
    And I press "Save changes"
    And "block_course_cycle" "block" should exist

  Scenario: Configuring the course Cycle block with Javascript off
    Given I log in as "admin"
    When I click on "Turn editing on" "link" in the "Administration" "block"
    And I add the "Course Cycle" block
    And I configure the "(new Course Cycle block)" block
    And I press "Save changes"
    And "block_course_cycle" "block" should exist
