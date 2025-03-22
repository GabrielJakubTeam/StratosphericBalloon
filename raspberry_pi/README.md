Device side consists of two key components. Firstly, a python script to send location coordinates triggered by [systemd](https://en.wikipedia.org/wiki/Systemd) during start-up. Secondly, a network connection with NetworkManager and auto-reconnect attribute, also triggered by systemd, to establish GSM connection.

# Setup
~~~ bash
# assume you are at $HOME
git clone https://github.com/GabrielJakubTeam/StratosphericBalloon.git

# copy files to appropriate locations 
sudo cp StratosphericBalloon/raspberry_pi/*.service /etc/systemd/system/
cp StratosphericBalloon/raspberry_pi/gps2402.py ~/

sudo nmcli c add con-name "mycon" type gsm ifname "*" apn "internet"
sudo nmcli c mod mycon connection.autoconnect yes
~~~
