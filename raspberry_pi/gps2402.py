"""
This script is responsible for convert gps data into simple form and later sending them into web database
"""

import time
import serial
import requests


def isfloat(num) -> bool:
    """
    Function check correctness transferred value and check if the value is not empty
    :param num:
    :return:
    bool
    """
    try:
        float(num)
        return True
    except ValueError:
        return False


def decode_gps(gps: str) -> tuple[float, float, str, str]:
    """
    Function extract longitude and latitude from data received form gps module
    :param gps:
    :return:
    tuple[float, float, str, str]
    """
    gps_list: list[str] = gps.split(',')

    if isfloat(gps_list[3]) and isfloat(gps_list[4]):
        lat: float = float(gps_list[3])
        long: float = float(gps_list[4])
        return lat, long, gps_list[4], gps_list[3]
    else:
        return 0, 0, '0', '0'


def decode_all_data(gps: str) -> list[str]:
    """
    Function extract additional data from gps like high, speed etc.
    :param gps:
    :return:
    list[str]
    """
    try:
        gps = gps.split(',')
        gps = ['0.0' if item == '' else item for item in gps]
        data = [gps[2], gps[5], gps[6], gps[7]]
    except Exception as err:
        print(f'Error: {err}')
        return ['0', '0', '0', '0']
    else:
        return data


def data_index(data: list[str]) -> int:
    """
    Function is looking for needed data index
    :param data:
    :return:
    int
    """
    for i, element in enumerate(data):
        if ',' in element:
            return i
    else:
        return 404


def send_coordinates_server(lat: str, long: str, data: str) -> None:
    """
    Function is responsible for sending data into server
    :param lat:
    :param long:
    :param data:
    :return:
    None
    """
    api_key = 'my_api_key'
    api_url = 'https://my_website/api.php'

    json_data = {
        'longitude': f'{[long, lat, data[3]]}',
        'latitude': f'{data[0:3]}'
    }

    headers = {
        'Content-Type': 'application/json',
        'authorization': f'Bearer {api_key}'
    }

    response = requests.post(api_url, headers=headers, json=json_data, timeout=10, verify=False)

    if response.status_code == 200:
        print('Api response:', response.json())
    else:
        print('Api error:', response.status_code, response.text)


def send_at_command(command: str, timeout: float = 1) -> str:
    """
    Function send command into gps device and return received response
    :param command:
    :param timeout:
    :return:
    str
    """
    try:
        with serial.Serial('/dev/ttyS0', 115200, timeout=timeout) as ser:
            ser.write((command + '\r\n').encode('utf-8'))
            time.sleep(0.5)
            response = ser.read_all().decode('utf-8').strip()
            return response if response else 'No response from device'
    except serial.SerialException as e:
        return f'Connection error: {e}'


if __name__ == '__main__':
    while True:
        print(f'Device starting...')

        try:
            test_response_1 = send_at_command('AT')
            test_response_2 = send_at_command('AT+CGNSPWR=1')
        except Exception as error:
            print(f'Error: {error}')
        else:
            device_response = send_at_command('AT+CGNSINF')
            print(f'Response for "AT+CGNSINF": {device_response}')

            while True:
                try:
                    device_response = send_at_command('AT+CGNSINF')
                    prepare_data = device_response.split()
                    index = data_index(prepare_data)
                    latitude, longitude, latitudeStr, longitudeStr = decode_gps(prepare_data[index])
                    all_data = decode_all_data(prepare_data[index])
                    send_coordinates_server(lat=latitudeStr, long=longitudeStr, data=all_data)
                    time.sleep(10)
                except Exception as error:
                    print(f'Program found following error: {error}')
                    break
