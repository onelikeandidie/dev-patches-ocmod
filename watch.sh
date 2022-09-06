#!bin/bash
# This requires Rust to be installed
# As well as the cargo-watch crate
# cargo watch can be installed with cargo install cargo-watch
cargo-watch -C . -w src -i dist -- sh ./build.sh