# favEject
#  2013 Akkiesoft / Eject-Command-Users-Group
# Inspire From:
#   http://peter-hoffmann.com/2012/simple-twitter-streaming-api-access-with-python-and-oauth.html

import os
import sys
import json
import tweepy

consumer_key    = ""
consumer_secret = ""
access_key      = ""
access_secret   = ""

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_key, access_secret)
api = tweepy.API(auth)
me = api.me()

class CustomStreamListener(tweepy.StreamListener):
    def on_data(self, data):
	jdata = json.loads(data)
        try:
          if jdata['event'] == "favorite":
            if jdata['source']['id'] != me.id:
              ### Eject.
              os.system('eject -T /dev/disk/by-id/YOUR-CD-ROM-DRIVE')
              os.system('eject -T /dev/disk/by-id/YOUR-CD-ROM-DRIVE')
        except KeyError:
          pass


    def on_error(self, status_code):
        print >> sys.stderr, 'Encountered error with status code:', status_code
        return True # Don't kill the stream

    def on_timeout(self):
        print >> sys.stderr, 'Timeout...'
        return True # Don't kill the stream

sapi = tweepy.streaming.Stream(auth, CustomStreamListener())
sapi.userstream()
