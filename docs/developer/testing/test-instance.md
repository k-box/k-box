# Test instance

The K-Link DMS can be tested on a specific test instance, available at https://test.klink.asia/dms/

The test instance always start with a fully empty and non-configured DMS.

The only way to login is to use the `admin@klink.local` account. For security reason the password is not written here, please ask it in the #dev channel on [Slack](https://k-link.slack.com)

## Fair usage of the Test instance

The test instance has been created in order to test a feature before it can be added to the current `development`. **You can only test one source code branch at a time**. 
If someone triggers a new test you will loose your work on the test instance.

> Only one feature/issue branch can be tested at time

## Triggering the test instance for a source code branch

The reload of the test instance with the code of a particular branch can only be performed by registered developers.

Every registered developer will receive a token (e.g. `asdsuydbsudhs`) to be used to trigger the build.

Triggering the build can be performed from command line using

```
$ curl -X POST -F token=TOKEN -F ref=BRANCH_NAME https://git.klink.asia/api/v3/projects/25/trigger/builds
```

where `TOKEN` is the token given to you for authentication purposes and `BRANCH_NAME` is the name of the git branch that contains the source code you want to test.

## Connecting to the instance

The build time could vary between 20 to 40 seconds, but the instance might not be fully available until a couple of minutes.

If you see a screen like the one below when connecting to https://test.klink.asia/dms/ means that the all K-Link is still starting. In normal conditions requires a couple of minutes. Periodically refresh the page until you see the usual login screen.

[![](img/test-instance-starting.JPG)](img/test-instance-starting.JPG)

If the screen above persists after 5 or more minutes contact the #dev team.
