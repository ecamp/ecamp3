#!/usr/bin/env bash

echo "Waiting for frontend container to start up..."
until curl --output /dev/null --silent --fail http://localhost:3000
do
  sleep 2
done
echo "Frontend container is ready."


echo "Waiting for API container to start up and migrate DB..."
until curl --output /dev/null --silent --fail http://localhost:3001
do
  sleep 2
done
echo "API container is ready."

