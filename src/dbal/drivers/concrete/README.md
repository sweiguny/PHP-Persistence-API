For adding a new driver you need to follow the following steps:
- Add the driver as constant to class `DriverManager`
- Add the driver to the `DRIVER_MAP` of `DriverManager`
- Add a config and a schema for testing to path `tests/config`
- Add the driver to `phpunit.xml` -> `<var name="drivers" value="<add new driver>"/>`
- Add the exclusions to class `DynamicConfig`
- Add an integration test to `tests/PPA/dbint/<new driver>`
- Add the new driver to `.travis.yml`