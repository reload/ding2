Feature: Follow tags
  In order to be notified when there is new content for specific tags
  As a library user
  I want to be able to follow and unfollow tags

  Background:
    Given I am logged in as a library user
    Then The list "interests" exists

  @api
  Scenario: Search for a tag
    Given I have searched for "science fiction"
    When I choose the first search result
    Then I should see the tag "science fiction" on the material

  @api
  Scenario: Follow a tag
    Given I have chosen a book material with the tag "science fiction"
    When I follow the tag "science fiction"
    Then I should see the tag "science fiction" on my list "interests"
