#!/bin/bash
#
# myaac release script
#
# places compressed archives into releases/ directory
#

# define release version
version=`cat VERSION`

echo "Preparing to release version $version of the MyAAC Project!"

# make required directories
mkdir -p releases
mkdir -p tmp && cd tmp

dir="myaac-$version"
if [ -d "$dir" ] ; then
	echo "Fatal error: Version $version already exists!!"
fi

# make version directory
mkdir "$dir"

# copy all AAC files into new created directory
echo "Copying required files.."
cd .. # we are now in the main directory

shopt -s dotglob # turn on hidden files with *
#cp -r * "tmp/$dir"
rsync -Rr --info=progress2 . "tmp/$dir"

cd "tmp/$dir"

# remove unneeded files
echo "Removing unneeded files.."
rm -r .git .github .idea
rm .gitattributes .gitignore
rm release.sh
rm _config.yml
rm -R releases
rm -R tmp

# tar.gz
echo "Creating .tar.gz package.."
file="myaac-$version.tar.gz"
tar -czf $file *
mv $file ../../releases/

# zip
echo "Creating .zip package.."
file="myaac-$version.zip"
zip -rq $file *
mv $file ../../releases/

cd ../..
shopt -u dotglob
rm -R tmp
echo "Done. Released files can be found in 'releases' directory."