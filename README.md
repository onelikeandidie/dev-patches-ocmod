# Dev Patches OpenCart Mod

[![OpenCart - 3.0.3.8](docs/OpenCart-Version.svg)](https://github.com/opencart/opencart/tree/3.0.x.x_Maintenance) 
![Mod Version - 0.0.1](docs/Mod-Version.svg)

This extension modifies the following files:

- admin/controller/startup/login.php
  - Admin login checks now uses the token saved in the session when either the 
  *DEV_MODE* constant or the `$_ENV["DEV_MODE"]` variable is set to `"true"`

Also adds some events

- admin/controller/common/header/after
  - Adds a banner announcing "Dev Mode" on the top of the admin panel
- admin/controller/extension/extension/promotion/after
  - Removes the adds on the extension listing and installer

# Build ocmod.zip

Run the build.sh script to create a ocmod.zip file

# Contribuiting

This repo includes a watch.sh, it requires "cargo-watch" to be installed in
your system. All it does is rerun build.sh whenever the src files change.

# License

This project is licensed under `Lesser GNU GPL 3.0`, this basically means you 
can use this code in proprietary software and can modify the files as you like 
but you must include the original license.

![license_png](docs/lgplv3-147x51.png)
