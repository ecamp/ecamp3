import std;

sub purge_all_recv {
  if (req.method == "PURGEALL") {
    # only allow on the purge port, and only if enabled, which is meant for development and tests
    if (std.port(server.ip) != 8081 || std.getenv("ENABLE_PURGE_ALL") != "true") {
      return (synth(405, "Not allowed"));
    }

    ban("req.url ~ .");
    return (synth(200, "Purged all objects"));
  }
}
