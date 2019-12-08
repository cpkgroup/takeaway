Feature: Email
  As an API client
  I need to be able to compose an email
  I need to be able to see email logs

  Scenario: Composing a new mail with wrong email address
    And I send a "POST" request to "/api/email/compose" with body:
    """
    {
      "subject": "Hello",
      "body": "Hello world",
      "recipients": [
        {
          "email": "habibi.mh_gmail.com"
        }
      ]
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "status" should be equal to the string "failed"

  Scenario: Composing a new mail
    And I send a "POST" request to "/api/email/compose" with body:
    """
    {
      "subject": "Hello",
      "body": "Hello world",
      "recipients": [
        {
          "email": "habibi.mh@gmail.com"
        }
      ]
    }
    """
    Then the response status code should be 202
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "status" should be equal to the string "succeed"
