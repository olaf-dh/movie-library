#!/usr/bin/env bash

OS=`uname -s`

if [ $OS != "Darwin" ]; then
  echo "This script is OSX-only. Please do not run it on any other Unix."
  exit 1
fi

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run with sudo/root. Please re-run without sudo." 1>&2
  exit 1
fi

echo ""
echo " +--------------------------------+"
echo " | Bind an IP-address to an alias |"
echo " +--------------------------------+"
echo ""

varIP=10
varIpLines="$(ifconfig | grep 10.255.255)"

function check_ip() {
    if [[ $varIpLines == *".$varIP"* ]]; then
        let "varIP+=10"
        check_ip
    fi
}

check_ip
sudo ifconfig lo0 alias 10.255.255.$varIP up

echo "WARNING: The following lines will create a new file to bind an IP-address permanently."
echo ""
echo -n "Do you wish to proceed? [y]: "
read decision

if [ "$decision" != "y" ]; then
  echo "Exiting. No changes made."
  exit 1
fi

echo ""

echo "== Writing a file - bind IP to an alias..."

sudo touch /Library/LaunchDaemons/org.$COMPOSE_PROJECT_NAME.ifconfig.plist
FILE=/Library/LaunchDaemons/org.$COMPOSE_PROJECT_NAME.ifconfig.plist

LINE="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
<!DOCTYPE plist PUBLIC \"-//Apple Computer//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n
<plist version=\"1.0\">\n
<dict>\n
    <key>Label</key>\n
    <string>org.$COMPOSE_PROJECT_NAME.ifconfig</string>\n
    <key>RunAtLoad</key>\n
    <true/>\n
    <key>ProgramArguments</key>\n
    <array>\n
      <string>/sbin/ifconfig</string>\n
      <string>lo0</string>\n
      <string>alias</string>\n
      <string>10.255.255.$varIP</string>\n
    </array>\n
</dict>\n
</plist>"

sudo tee -a $FILE > /dev/null <<EOT
$LINE
EOT

if grep -q "10.255.255.$varIP" "$FILE"; then
  echo ""
  echo "SUCCESS! The file: $FILE was created."
  else
    echo ""
    echo "Something went wrong!"
fi
