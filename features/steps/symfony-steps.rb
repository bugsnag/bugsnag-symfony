require_relative "../lib/symfony"

Given("I start the symfony fixture") do
  steps %{
    Given I start the service "#{Symfony.fixture}"
    And I wait for the host "localhost" to open port "#{Symfony.fixture_port}"
  }
end

When("I navigate to the route {string}") do |route|
  Symfony.navigate_to(route)
end

When("I navigate to the route {string} with the query string parameters:") do |route, table|
  # convert the data table into a query string, e.g. [['a', 'b'], ['c', 'd']] => a=b&c=d
  query_string = URI.encode_www_form(table.rows)

  Symfony.navigate_to("#{route}?#{query_string}")
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
