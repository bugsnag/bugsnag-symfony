require_relative "../lib/symfony"

Given("I start the symfony fixture") do
  steps %{
    Given I start the service "#{Symfony.fixture}"
    And I wait for the host "localhost" to open port "#{Symfony.fixture_port}"
  }
end

Given("I run the {string} command in the symfony fixture") do |command|
  path = Symfony.version == 2 ? 'app' : 'bin'

  step "I run the service '#{Symfony.fixture}' with the command './#{path}/console #{command}'"
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
    Then the event '#{path}' starts with '#{Symfony.version}'
    And the event '#{path}' matches '^(\\d\\.){2}\\d+$'
  }
end

Then("the Symfony response matches {string}") do |regex|
  wait = Maze::Wait.new(timeout: 10)
  success = wait.until { Symfony.last_response != nil }

  raise 'No response from the Symfony fixture!' unless success

  assert_match(Regexp.new(regex), Symfony.last_response)
end

Then("the exception {string} equals one of the following:") do |path, values|
  desired_value = Maze::Helper.read_key_path(
    Maze::Server.errors.current[:body],
    "events.0.exceptions.0.#{path}"
  )

  assert_includes(values.raw.flatten, desired_value)
end
