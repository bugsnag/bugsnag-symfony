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
  Given I set environment variable "BUGSNAG_REDACTED_KEYS" to '["A", "/^x.z$/"]'
  And I start the symfony fixture
  When I navigate to the route "/redacted-keys"
  Then I wait to receive an error
  And the event "metaData.testing.a" equals "[FILTERED]"
  And the event "metaData.testing.b" equals 2
  And the event "metaData.testing.c" equals 3
  And the event "metaData.testing.xyz" equals "[FILTERED]"
