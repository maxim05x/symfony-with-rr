Feature: List users

  Scenario Outline: List with filters users
    Given the following fixtures are loaded:
      | users.yaml |
    And I am as 'user1'

    When I send a GET request to '/api/v1/users<params>'
    Then Response code: 200
    And the JSON node 'meta.pagination.total' should be equals to '<count>'
    And the JSON node 'data' should have '<count>' element

    Examples:
      | params                         | count |
      |                                | 3     |
      | ?filter[email]={{user1_email}} | 1     |
      | ?filter[email]=                | 3     |


  Scenario Outline: List with ordering users
    Given the following fixtures are loaded:
      | users.yaml |
    And I am as 'user1'

    When I send a GET request to '/api/v1/users<params>'
    Then Response code: 200
    And the JSON node 'data' should have '3' element
    And the JSON node 'data[0].id' should be equals to '<first_user>'
    And the JSON node 'data[2].id' should be equals to '<last_user>'

    Examples:
      | params            | first_user | last_user |
      | ?sort[email]=desc | {{user3}}  | {{user1}} |
      | ?sort[email]=asc  | {{user1}}  | {{user3}} |