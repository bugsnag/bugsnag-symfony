require_relative "../lib/symfony"

Given("I start the symfony fixture") do
  steps %{
    Given I start the service "#{Symfony.fixture}"
    And I wait for the host "localhost" to open port "#{Symfony.fixture_port}"
  }
end

Given("I run the {string} command in the symfony fixture") do |command|
  step "I run the service '#{Symfony.fixture}' with the command './bin/console #{command}'"
end

When("I navigate to the route {string}") do |route|
  Symfony.navigate_to(route)
end

Then("the event {string} matches the current PHP version") do |path|
  steps %{
    Then the event '#{path}' starts with '#{ENV["PHP_VERSION"]}'
    And the event '#{path}' matches '^(\\d\\.){2}\\d+$'
  }
end

Then("the event {string} matches the current major Symfony version") do |path|
  steps %{
    Then the event '#{path}' starts with '#{ENV["SYMFONY_VERSION"]}'
    And the event '#{path}' matches '^(\\d\\.){2}\\d+$'
  }
end

Then("the Symfony response matches {string}") do |regex|
  wait = Maze::Wait.new(timeout: 10)
  success = wait.until { Symfony.last_response != nil }

  raise 'No response from the Symfony fixture!' unless success

  assert_match(Regexp.new(regex), Symfony.last_response)
end
