Feature: Authenticate user

  Scenario Outline: Auth user
    Given first the following fixtures are loaded:
      | users.yaml |
    And run next in transaction

    When I send a POST request to '/api/v1/session/token'
    """
    {
      "email": "<email>",
      "password": "<password>"
    }
    """

    Then Response code: <code>
    And print last JSON response

    Examples:
      | email           | password       | code |
      | {{user1_email}} | {{user1_pass}} | 200  |
