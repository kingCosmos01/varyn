#!/bin/sh
# This must be executed from within the folder of the test project.
# Assumes phpunit is set up and in the path.
# See https://phpunit.de/getting-started/phpunit-9.html and https://phpunit.readthedocs.io/en/9.5/textui.html

echo "Starting unit test:"
phpunit --testdox-xml CommonTest.xml ./CommonTest.php > CommonTest.log 2>> varyn-test-errors.log
phpunit --testdox-xml EnginesisSDKTest.xml ./EnginesisSDKTest.php > EnginesisSDKTest.log 2>> varyn-test-errors.log
echo "Test complete, check varyn-test-errors.log for errors, check *.log files for results"
