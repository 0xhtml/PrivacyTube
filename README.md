# PrivacyTube
PrivacyTube is a privacy oriented YouTube frontend. PrivacyTube uses the Google API to get content from YouTube.

It has it's own account system with that you can subscribe to channels. PrivacyTube uses MySQL for databases and
[youtube-dl](https://github.com/ytdl-org/youtube-dl/) to host the youtube videos privately.

## Installation
1. Install `php-curl` and `youtube-dl`.
2. Setup database (More information comming soon)
3. Set the webservers root directory to `web`.
4. Crete a file called `key.txt` containing the Google API key in the first line and the MySQL passwaord in the second line. Example:
```
AIzht7kdcWb3MyBud8oHHEPiDEDQbfaS8hLQDs
super_secret_password
```
5. You're ready!

## Community
You can help making PrivacyTube better! Erveryone can create a pull request and develop PrivacyTube. I would also love to see feature request for this project.

### TODO
If you wanna help but you don't know what to implement, then here is the perfect list for you:
- Register page
- Change password page
- Information about the MySQL setup
- A MySQL setup script
- A way to enable caching of videos and images loaded via the `dl.php` srcipt
