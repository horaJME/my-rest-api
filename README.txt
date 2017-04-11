Simple REST API for 'Simba'

Simba is mobile banking authentication process simulation.

Simba mobile app uses API in process of Identification and Authentication.

For that purposes API consists of 3 ID methods and 1 Auth method-

IDENTIFICATION
==============

GET /api/user/{user}

For given username, searching if users OTP list exists.

POST /api/PIN

For given info (PIN and username), inserting PIN into users OTP list file.

GET /api/OTP/{user}

For given user, returns OTP list.

AUTHENTICATION
============== 

POST /api/auth

Checking if authentication credentials match.
