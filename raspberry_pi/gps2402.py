import serial
import time
import requests


# function checks the correctness of the entered data
def isfloat(num) -> bool:
    try:
        float(num)
        return True
    except ValueError:
        return False


# function extracts longitude and latitude from data received from GPS
def decode_gps(gps: str) -> tuple[float, float, str, str]:
    gps_list: list[str] = gps.split(',')

    if isfloat(gps_list[3]) and isfloat(gps_list[4]):
        lat: float = float(gps_list[3])
        long: float = float(gps_list[4])
        return lat, long, gps_list[4], gps_list[3]
    else:
        return 0, 0, '0', '0'


def send_coordinates_server(lat: str, long: str) -> None:
    api_key = 'Api_key'
    api_url = 'http://ip_v4/BallonPage/api.php'

    data = {
        'longitude': f'{long}',
        'latitude': f'{lat}'
    }

    headers = {
        'Content-Type': 'application/json',
        'Authorization': f'Bearer {api_key}'
    }

    response = requests.post(api_url, headers=headers, json=data, timeout=10)

    if response.status_code == 200:
        print('Api response:', response.json())
    else:
        print('Error:', response.status_code, response.text)


# function send command to gps module
def send_at_command(command: str, timeout: float = 1) -> str:
    try:
        with serial.Serial('/dev/ttyS0', 115200, timeout=timeout) as ser:
            ser.write((command + '\r\n').encode('utf-8'))
            time.sleep(0.5)
            response = ser.read_all().decode('utf-8').strip()
            return response if response else 'No response'
    except serial.SerialException as e:
        return f'Connection error: {e}'


# program main loop
if __name__ == '__main__':
    attempts = 5

    while attempts > 0:
        print(f'Starting the device...')

        try:
            response1 = send_at_command(command='AT')
            response2 = send_at_command(command='AT+CGNSPWR=1')
        except Exception as error:
            print(f'Error: {error}')
        else:
            response3 = send_at_command(command='AT+CGNSINF')
            print(f'Response on "AT+CGNSINF": {response3}')

            while True:
                try:
                    response3 = send_at_command(command='AT+CGNSINF')
                    prepare_data = response3.split()
                    latitude, longitude, latitudeStr, longitudeStr = decode_gps(gps=prepare_data[2])
                    send_coordinates_serwer(latitude=latitudeStr, longitude=longitudeStr)
                    time.sleep(10)
                except Exception as error:
                    print(f'Program encountered an error: {error}')
                    break
    attempts -= 1
    print(f'Device startup failed, attempts left: {attempts}')
