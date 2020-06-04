#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# vim:ts=4

# below module is required:
# bh1745 ($ sudo pip3 install bh1745 )

import time
import subprocess
from bh1745 import BH1745

def eject():
    status_print("Pokemon is comming! Eject!")
    cmd = "/usr/bin/eject"
    subprocess.call(cmd, shell=True)
    cmd = cmd + " -t"
    subprocess.call(cmd, shell=True)

def status_print(msg):
    print("\n\033[2K" + msg + "\033[1A\r", end="")

def is_green(r, g, b, count):
    if (r < 200 and g > 250 and b < 200):
        # status_print("comming count:" + str(count))
        return count + 1
    return 0

def is_red(r, g, b, count):
    if (r > 230 and g < 150 and b < 150):
        return count + 1
    return 0

bh1745 = BH1745()
bh1745.setup(i2c_addr=0x39)
bh1745.set_leds(0)
time.sleep(1.0)
bh1745.set_leds(1)

challenge = 0
count = 0
try:
    while True:
        r, g, b = bh1745.get_rgb_scaled()
        print("\r\033[48;2;{};{};{}m  \033[0m".format(r, g, b)   \
            + " R: {:03d}, G: {:03d}, B: {:03d}".format(r, g, b) \
            , end="")
        if (not challenge):
            time.sleep(0.3)
            count = is_green(r, g, b, count)
            if count > 1:
                challenge = 1
                eject()
        else:
            time.sleep(0.1)
            count = is_red(r, g, b, count)
            status_print("Challenge: "+str(challenge) \
                       + "  FailCheck: "+str(count))
            if count > 2:
                challenge = 0
                count = 0
                status_print("Failed to catch pokemon.")
            else: 
                challenge = challenge + 1
            if (challenge > 70):
                challenge = 0
                count = 0
                status_print("Caught pokemon!!")
except KeyboardInterrupt:
    print("\n\nbye.\n")
    bh1745.set_leds(0)
