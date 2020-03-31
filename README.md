# PrivacyTube
![GitHub issues](https://img.shields.io/github/issues/0xhtml/PrivacyTube.svg?style=for-the-badge) ![GitHub](https://img.shields.io/github/license/0xhtml/PrivacyTube.svg?style=for-the-badge)

PrivacyTube is a privacy oriented YouTube frontend. PrivacyTube uses the Google API to get content from YouTube.

It has it's own account system with that you can subscribe to channels.

## Installation
1. Clone the repository for experimental features or download the latest release.
2. Build the docker image: `docker build -t 0xhtml/privacytube:latest .`
3. Get an API-Key from Google and put it into `.env`

    `api_key=AI***********************************Ud`

3. Run docker-compose: `docker-compose up -d`
4. Connect to `localhost:8080`.

## Community
You can help making PrivacyTube better! Erveryone can create a pull request and develop PrivacyTube. I would also love to see feature request for this project.

### TODO
If you wanna help but you don't know what to implement, then here is the perfect list for you:
- Change password page
- YouTube comments support
- Showing the length of videos
