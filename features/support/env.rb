require "os"
require_relative "./../lib/symfony"
require_relative "./../lib/utils"

PROJECT_ROOT = File.realpath("#{__dir__}/../../")
FIXTURE_PATH = File.realpath("#{PROJECT_ROOT}/features/fixtures/#{Symfony.fixture}")

def current_ip
  return "host.docker.internal" if OS.mac?

  ip_addr = `ifconfig | grep -Eo 'inet (addr:)?([0-9]*\\\.){3}[0-9]*' | grep -v '127.0.0.1'`
  ip_list = /((?:[0-9]*\.){3}[0-9]*)/.match(ip_addr)
  ip_list.captures.first
end

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
  ENV["BUGSNAG_ENDPOINT"] = "http://#{current_ip}:9339/notify"
  Symfony.reset!
end
