require_relative "./../lib/symfony"
require_relative "./../lib/utils"

PROJECT_ROOT = File.realpath("#{__dir__}/../../")
FIXTURE_PATH = File.realpath("#{PROJECT_ROOT}/features/fixtures/#{Symfony.fixture}")

Maze.hooks.after_configuration do
  # log to console, not the file
  Maze.config.file_log = false
  Maze.config.log_requests = true

  # don't wait so long for requests/not to receive requests
  Maze.config.receive_requests_wait = 10
  Maze.config.receive_no_requests_wait = 10

  # bugsnag-symfony doesn't send the integrity header (because it's not needed)
  Maze.config.enforce_bugsnag_integrity = false

  if ENV["DEBUG"]
    puts "Installing bugsnag-symfony from '#{PROJECT_ROOT}' to '#{FIXTURE_PATH}'"
  end

  Utils.install_bugsnag(PROJECT_ROOT, FIXTURE_PATH)
end

Maze.hooks.before do
  ENV["BUGSNAG_API_KEY"] = $api_key
  ENV["BUGSNAG_ENDPOINT"] = "http://#{Utils.current_ip}:9339/notify"
  Symfony.reset!
end
