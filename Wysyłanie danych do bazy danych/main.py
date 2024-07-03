#import modules
import network
import socket
from time import sleep
from machine import Pin, I2C
import bme280
import urequests

ssid = '' #Your network name
password = '' #Your WiFi password

#initialize I2C 
i2c=I2C(0,sda=Pin(0), scl=Pin(1), freq=400000)

def connect():
    #Connect to WLAN
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    wlan.connect(ssid, password)
    while wlan.isconnected() == False:
        print('Waiting for connection...')
        sleep(1)
    ip = wlan.ifconfig()[0]
    print(f'Connected on {ip}')
    return ip

def open_socket(ip):
    # Open a socket
    address = (ip, 80)
    connection = socket.socket()
    connection.bind(address)
    connection.listen(1)
    return connection

def transfer_data(connection):
    while True:
        bme = bme280.BME280(i2c=i2c)
        temp = bme.values[0]
        temperature = temp[:temp.index("C")]
        press = bme.values[1]
        pressure = press[:press.index("h")]
        hum = bme.values[2]
        humidity = hum[:hum.index("%")]
        print(temperature, pressure, humidity)
        request_url = ('http://swiat-wirtualny.cba.pl/insert.php?temperature=' + temperature + '&humidity=' + humidity + '&pressure=' + pressure)
        print(request_url)
        try:
            request = urequests.get(url = request_url).text
            print(request)
        except:
            print("urequest send data error")
        sleep(600)
        
try:
    ip = connect()
    connection = open_socket(ip)
    transfer_data(connection)
except KeyboardInterrupt:
    machine.reset()
        
    
if connect():
    
    transfer_data()
