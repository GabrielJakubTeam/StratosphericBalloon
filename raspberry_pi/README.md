Device side consists of two key components. Firstly, a python script to send location coordinates triggered by [systemd](https://en.wikipedia.org/wiki/Systemd) during start-up. Secondly, a network connection with NetworkManager and auto-reconnect attribute, also triggered by systemd, to establish GSM connection.

# Setup
~~~ bash
git clone https://github.com/GabrielJakubTeam/StratosphericBalloon.git

# copy files to appropriate locations 
sudo cp StratosphericBalloon/raspberry_pi/balon-gps.service /etc/systemd/system/
cp StratosphericBalloon/raspberry_pi/gps2402.py ~/

# enable and start service
sudo systemctl enable balon-gps.service
sudo systemctl start balon-gps.service

# configure connection
sudo nmcli c add con-name "mycon" type gsm ifname "*" apn "internet" # add connection
sudo nmcli c mod mycon connection.autoconnect yes                    # autoconnect
sudo nmcli c mod mycon ipv4.route-metric 999                  # top priority for ipv4
sudo nmcli c mod mycon ipv6.route-metric 999                  # top priority for ipv6
~~~
