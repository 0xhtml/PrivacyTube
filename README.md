# PrivacyTube
![GitHub issues](https://img.shields.io/github/issues/0xhtml/PrivacyTube.svg?style=for-the-badge) ![GitHub](https://img.shields.io/github/license/0xhtml/PrivacyTube.svg?style=for-the-badge)

PrivacyTube is a privacy oriented YouTube frontend. PrivacyTube uses the Google API to get content from YouTube.

It has it's own account system with that you can subscribe to channels. PrivacyTube uses MySQL for databases and
[youtube-dl](https://github.com/ytdl-org/youtube-dl/) to host the youtube videos privately.

## Installation
1. Install `php-curl` and `youtube-dl` (and of course a MySQL-server, a webserver and PHP).
2. Setup database:
    1. Create a database.
    2. Run the setup script: `php setup.php`.
    3. Put your MySQL-host, -user, password and -database into the `config.json`.
3. Get an API-Key from Google and put it into the `config.json` aswell.
4. Set the webservers root directory to `web`.
5. You're ready!

## Community
You can help making PrivacyTube better! Erveryone can create a pull request and develop PrivacyTube. I would also love to see feature request for this project.

### TODO
If you wanna help but you don't know what to implement, then here is the perfect list for you:
- Change password page
- A way to enable caching of videos and images loaded via the `dl.php` script
