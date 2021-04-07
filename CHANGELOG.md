# Changelog
All changes to this project will be documented in this file.

## [1.8.9] - 2021-04-07
### Fixed
- fatal error when getting the status and no video was uploaded
  
## [1.8.8] - 2021-02-16
### Added
- property `Live->public`

## [1.8.7] - 2021-01-18
### Added
- add User-Agent header in API requests

## [1.8.5] - 2020-05-15
### Fixed
- reduce chunk size to avoid reaching uploading limit

## [1.8.3] - 2019-01-23
### Removed
- remove unused language property on Player Model

## [1.8.2] - 2019-01-16
### Added
- add Features from Account model

## [1.8.1] - 2019-01-16
### Removed
- remove Term from Account model

## [1.8.0] - 2019-01-15
### Added
- delete player logo method

## [1.7.0] - 2019-01-15
### Added
- Chapter support

## [1.6.1] - 2019-12-16
### Added
- property `Video->updatedAt`

## [1.3.0] - 2019-11-07
### Added
- method `$client->videos->getStatus($videoId)` https://docs.api.video/5.1/videos/show-video-status

## [1.2.2] - 2019-11-07
### Fixed
- missing property video.panoramic

## [1.2.0] - 2019-07-18
### Added
- Improve Documentation
- Light Refactoring to support new Analytics response
- Add Analytics Session Events support

## [1.1.0] - 2019-06-13
### Added
- Constructors for production and sandbox environments

## [1.0.0] - 2019-03-06
### Added
- New resource /account to get quota and term informations.

### Changed
- Update authentication method. Only Api Key is now required.

### Removed
- Old authentication method by email/password
