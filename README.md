# Vcpkg Http Cache

This project provides a minimal php script to be used as server backend for the vcpkg binary caching http module.

# Usage

For putting packages into the cache, a secret token must be configured.
Reading the cache is public.
This could be used in a setup where a CI pipeline as trusted party is allowed to put prebuild binaries into the cache and anyone can publicly access the cache.

## Read

Set the environment variable `VCPKG_BINARY_SOURCES` to
`clear;http,https://example.com/{triplet}-{name}-{sha},read`.

## Write

Set the environment variable `VCPKG_BINARY_SOURCES` to
`clear;http,https://example.com/{triplet}-{name}-{sha},readwrite,Authorization: Token supersecrettoken`.
