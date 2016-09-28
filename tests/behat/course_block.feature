@block @block_course_cycle
Feature: Course Cycle blocks in a course
  In order to have one course Cycle block in a course
  As a teacher
  I need to be able to create and change such blocks

  Scenario: Adding Course Cycle block in a course
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Terry1    | Teacher1 | teacher@asd.com  |
      | student1 | Sam1      | Student1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course Cycle" block
    And I configure the "Course Cycle" block
    And I set the field "recycleaction" to "Keep"
    And I press "Save changes"
    And I should see "Course Cycle" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should not see "Course Cycle" "block"
