# Dev Patches OpenCart Mod

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
