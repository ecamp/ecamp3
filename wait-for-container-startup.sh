#!/usr/bin/env bash

echo "Waiting for API container to start up and migrate DB..."
until curl --output /dev/null --silent --fail http://localhost:3000/api
do
  sleep 2
done
echo "API container is ready."

echo "Waiting for print container to start up..."
until curl --output /dev/null --silent --fail http://localhost:3000/print/health
do
  sleep 2
done
echo "Frontend container is ready."

echo "Waiting for frontend container to start up..."
until curl --output /dev/null --silent --fail http://localhost:3000
do
  sleep 2
done
echo "Frontend container is ready."
