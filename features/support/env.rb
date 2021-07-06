require_relative "./../lib/symfony"
require_relative "./../lib/utils"

PROJECT_ROOT = File.realpath("#{__dir__}/../../")
FIXTURE_PATH = File.realpath("#{PROJECT_ROOT}/features/fixtures/#{Symfony.fixture}")

# TODO: Maze.hooks.after_configuration doesn't seem to work ?
AfterConfiguration do
  Maze.config.file_log = false
  Maze.config.log_requests = true
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
