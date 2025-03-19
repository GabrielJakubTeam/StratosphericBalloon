# StratosphericBalloon
Hardware stratospheric balloon localization module with server. 

## Table of Contents
* [General info](#general-info)
* [Technologies Used](#technologies-used)
* [Hardware](#hardware)
* [Repository Structure](#repository-structure)
* [Appearance](#appearance)
* [Project Status](#project-status)


## General Info
Project for a stratospheric balloon locator module that allows tracking its position from a website

## Technologies Used
* Python version: 3.12
* Php
* Linux


## Hardware
- SIM7000E LTE GPS HAT - Waveshare 14865 
- UPS HAT - Waveshare 18306
- SIM card
- Raspbery Pi 4B
- Server

## Repository Structure
```
.
├── LICENSE
├── raspberry_pi
│   ├── gps2402.py
│   └── README.md       # more information about device side software
├── README.md           # you are here ;)
└── server
    ├── api.php
    ├── config.php
    ├── get_data.php
    ├── index.php
    └── README.md       # more information about server side software
```

## Appearance
![raspberryPi](images/raspberryPi.jpg)

## Project Status
Project is in a phase of continuous development, the planned completion date is mid-May this year
