Feature: Redacted keys

Scenario: Keys won't be redacted when there are no redacted keys
  Given I start the symfony fixture
  When I navigate to the route "/redacted-keys"
  Then I wait to receive an error
  And the event "metaData.testing.a" equals 1
  And the event "metaData.testing.b" equals 2
  And the event "metaData.testing.c" equals 3
  And the event "metaData.testing.xyz" equals 4

Scenario: Keys can be redacted from metadata
  Given I start the symfony fixture
  When I navigate to the route "/redacted-keys" with the query string parameters:
    | key    | value   |
    | keys[] | A       |
    | keys[] | /^x.z$/ |
  Then I wait to receive an error
  And the event "metaData.testing.a" equals "[FILTERED]"
  And the event "metaData.testing.b" equals 2
  And the event "metaData.testing.c" equals 3
  And the event "metaData.testing.xyz" equals "[FILTERED]"
