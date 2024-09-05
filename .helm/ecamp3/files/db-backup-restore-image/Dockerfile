FROM debian:12.7-slim

ENV DEBIAN_FRONTEND="noninteractive"
RUN apt-get update && \
    apt-get install -y \
            awscli=2.9.19-1 \
            gpg \
            postgresql-client-15
