import adafruit_connection_manager
import wifi
#import mdns

class akkie_wifi():
    def __init__(self, ap_list, hostname = None):
        self.ap_list = ap_list
        self.connected = False
        self.pool = None
        self.ssl_context = None
        self.hostname = hostname

    def connect(self):
        wifi.radio.start_station()
        wifi.radio.enabled = True
        #if self.hostname:
        #    mdns_server = mdns.Server(wifi.radio)
        #    mdns_server.hostname = self.hostname
        for ap in self.ap_list:
            try:
                print("Trying to connect with %s..." % ap[0])
                wifi.radio.connect(ap[0], ap[1])
                print("Connected with %s." % ap[0])
                self.pool = adafruit_connection_manager.get_radio_socketpool(wifi.radio)
                self.ssl_context = adafruit_connection_manager.get_radio_ssl_context(wifi.radio)
                self.connected = True
                break
            except Exception as e:
                print("Failed to connect: %s" % e)
        if not self.connected:
            print("Failed to connect for all aps.")

    def disconnect(self):
        wifi.radio.enabled = False
        wifi.radio.stop_station()
        self.connected = False
        self.pool = None