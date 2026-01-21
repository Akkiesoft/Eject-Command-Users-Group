#! /usr/bin/env ruby

EJECT_COMMAND = ENV["EJECT_COMMAND"] || "/usr/bin/eject -T /dev/sr0"
LISTEN_PORT = (ENV["PORT"] || 8000).to_i

require "webrick"

server = WEBrick::HTTPServer.new(Port: LISTEN_PORT)
trap(:INT) do
  server.shutdown
end
server.mount_proc("/") do |request, response|
  result = request.cookies.find { |cookie|
    "result" == cookie.name
  }&.value.to_s
  response.cookies << WEBrick::Cookie.new("result", "")
  response["Content-Type"] = "text/html"
  response.body = <<EOS
<html>
  <head>
    <meta charset="UTF-8">
    <title>Web Eject</title>
  </head>
  <body>
    <h1>Web Eject Button</h1>
    <p>#{WEBrick::HTMLUtils.escape(result.empty? ? "Press 'Eject' button" : result)}.</p>
    <form action="/eject" method="post">
      <input type="submit" value="Eject">
    </form>
  </body>
</html>
EOS
end
server.mount_proc("/eject") do |request, response|
  result = system(EJECT_COMMAND) ? "Success" : "Failure"
  response.cookies << WEBrick::Cookie.new("result", result)
  response.set_redirect(WEBrick::HTTPStatus::Found, "/")
end
server.start
