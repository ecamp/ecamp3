#!/bin/sh
# Runs the varnishtest files against the real VCL, with the backend replaced by the mock servers of each test.
# Usage (from the repository root):
#   docker compose run --rm --no-deps --entrypoint sh -v "$PWD/api/docker/varnish:/varnish:ro" http-cache /varnish/tests/run.sh
set -e

export COOKIE_PREFIX=localhost_
TEST_VCL_DIR=$(mktemp -d)
cp /varnish/vcl/*.vcl "$TEST_VCL_DIR"
: > "$TEST_VCL_DIR/_config.vcl"

varnishtest -k -D "vcldir=$TEST_VCL_DIR" /varnish/tests/*.vtc
