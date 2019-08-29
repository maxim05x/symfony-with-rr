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

    Examples:
      | email           | password       | code |
      | {{user1_email}} | {{user1_pass}} | 200  |
      | {{user2_email}} | test           | 401  |
      | {{user3_email}} | {{user3_pass}} | 401  |
