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

  Scenario: See email logs
    And I send a "GET" request to "/api/email"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "root.items[0].subject" should be equal to the string "Hello"
    And the JSON node "root.items[0].body" should be equal to the string "Hello world"
    And the JSON node "root.items[0].recipients[0].email" should be equal to the string "habibi.mh@gmail.com"
