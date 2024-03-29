require "net/http"

# A helper class for interacting with our Symfony fixtures
class Symfony
  class << self
    attr_reader :last_response

    def version
      @version ||= Integer(ENV["SYMFONY_VERSION"])
    end

    def reset!
      @last_response = nil
    end

    def fixture
      "symfony-#{version}"
    end

    def fixture_port
      "1230#{version}"
    end

    def navigate_to(route)
      attempts = 0

      begin
        @last_response = Net::HTTP.get("localhost", route, fixture_port)
      rescue => e
        raise "Failed to navigate to #{route} (#{e})" if attempts > 10

        attempts += 1
        sleep 1

        retry
      end
    end
  end
end
