#!/bin/bash
# Functions
result="" # Result from function calls is stored here
### Extract String from the JSON field of a file
extract_string () {
    var_name=$1 # Name of the field in the json file
    json_file=$2 # JSON to extract variable from
    extraction=$(grep "\"$var_name\":" $json_file)
    # Remove the 'field: "' from the string
    extraction=${extraction#*:*\"}
    # Remove the '"'  from the string
    extraction=${extraction%%\"*}
    result=$extraction
}

echo "Building..."
# Move inside source dir
cd ./src
echo "Extracting version number..."
# Find the current version from the src/install.json
extract_string "version" "./install.json"
version=$result
# Get the code name of the extension
extract_string "code" "./install.json"
code=$result
# Concat a good file name
file_name="$code-$version.ocmod.zip"
echo "Removing old builds..."

# Remove the old build
rm -rf ../dist
# Recreate the build dir
mkdir ../dist
# Copy src to build dir
mkdir ../dist/target
cp -r ./* ../dist/target/
echo "Adding headers..."
# Get the license information of the extension
extract_string "license" "./install.json"
license=$result
extract_string "author" "./install.json"
author=$result
extract_string "link" "./install.json"
link=$result
extract_string "copyright" "./install.json"
copyright=$result
# Add license info to the top of the files
header_info="/** \n\
  * @author $author \n\
  * @version $version \n\
  * @license $license \n\
  * @link $link \n\
  * @copyright $copyright \n\
  */"
# Add to every file on the second line
find ../dist/target/* -type f -not -name "*.json" -not -name "*.css"  -exec sed -i "1a\\$header_info" {} \;
echo "Compressing new build..."
# Zip up the src files with the version number
cd ../dist/target
zip -qr "../$file_name" *
# Move back to the original directory
cd ..
echo "Build $file_name complete"