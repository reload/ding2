# Ding Behat tests #

## Usage ##

composer install
make test-setup
./bin/behat --tags '~wip'

### Profiles ###

Behat is configured with three profiles, which can be selected with
the -p switch.

The default profile runs test using goutte and phantomjs for tests
that require JavaScript.

The chrome and firefox profiles runs in the named browser using
Selenium. You need to manually start the Selenium server.

## Development ##

### Tags ###

todo @wip, @regression, @no_messages_check

### Test layout ###

Behat features is located in `features`, while tests for major
(possibly optional) components should be located in a subfolder. For
instance, the tests for the personalization modules are located in
`features/p2`.

todo use suites

### Services ###

Provider based services is mocked by creating a mocking
implementation. Most of these is in Connie, the testing provider
module included in ding_provider.

##### Opensearch #####

https://opensource.dbc.dk/services/open-search-web-service

Using http://oss-services.dbc.dk/opensearch/4.0.1/
