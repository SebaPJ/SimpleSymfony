#!/usr/bin/env bash

echo "This installation is only for Ubuntu Linux"

VERSION="1.9.2"

sudo apt-get install virtualbox -y
sudo apt-get install virtualbox-dkms -y

#i386 and i686 are both 32-bit.
#x86_64 is 64-bit
CHOOSE="vagrant_${VERSION}_x86_64.deb"

wget -nc https://releases.hashicorp.com/vagrant/${VERSION}/${CHOOSE}
sudo dpkg -i ${CHOOSE}
rm ${CHOOSE}

sudo apt-get install -y phpunit