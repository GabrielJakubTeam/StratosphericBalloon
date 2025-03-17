Device side consists of two key components. First, Python script to send location coordinates run by [systemd](https://en.wikipedia.org/wiki/Systemd) during start-up. Second, [Wvdial](https://wiki.archlinux.org/title/Wvdial),  also run by systemd, to establish GSM connection.

# Setup
~~~ bash
# assume you are at $HOME
git clone https://github.com/GabrielJakubTeam/StratosphericBalloon.git

# copy files to appropriate locations 
sudo cp StratosphericBalloon/raspberry_pi/*.service /etc/systemd/system/
cp StratosphericBalloon/raspberry_pi/gps2402.py ~/

# reload and start services
sudo daemon-reload
sudo systemctl start baloon_gps gprs_connection
sudo systemctl enable baloon_gps gprs_connection
~~~
