# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1-alpha7] - 2018-07-27
### Changed
- Removed unnecessary dependency on `rebelcode/booking-interface`.
- Now providing resource model implementations from the newer `wp-cqrs-resource-models`, which implement `dhii/cqrs-resource-model-interface` version `0.2-alpha1`, and thus return lists of maps instead of just lists of containers.

## [0.1-alpha6] - 2018-07-12
### Fixed
- Installed missing `rebelcode/rcmod-wp-cqrs` dev dependency.
- The Booking Select RM no longer creates booking instances.

## [0.1-alpha5] - 2018-06-14
### Fixed
- The unbooked sessions SELECT CQRS resource model used to give the same session multiple times if it matches multiple bookings.

## [0.1-alpha4] - 2018-06-13
### Fixed
- No sessions being returned from unbooked sessions resource model due to incorrect JOIN type.
- Incorrectly using the source SELECT table name in unbooked sessions resource model.

## [0.1-alpha3] - 2018-06-11
### Added
- A SELECT resource model for unbooked sessions.

## [0.1-alpha2] - 2018-06-04
### Added
- Module now automatically creates its database structure by using migrations.

## [0.1-alpha1] - 2018-05-21
Initial version.
