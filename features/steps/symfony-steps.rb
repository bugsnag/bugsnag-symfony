Given("I start the symfony fixture") do
  steps %{
    Given I start the service "#{Symfony.fixture}"
    And I wait for the host "localhost" to open port "#{Symfony.fixture_port}"
  }
end

When("I navigate to the route {string}") do |route|
  Symfony.navigate_to(route)
end
