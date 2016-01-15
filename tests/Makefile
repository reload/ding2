MOCK_HOST=localhost

test-setup: setup-modules prepare-mock setup-config setup-phantomjs

# Install a test site.
install-site:
	drush site-install ding2

# Setup test modules on site.
setup-modules:
	# Enable P2.
	drush en -y ding_p2_installer

	# We use the connie test provider for testing. Enable it an
	# disable the default alma.
	drush en -y connie
	drush dis -y alma

# Prepare mock server.
prepare-mock:
	git clone -b close-connection git@github.com:reload/mock-http-server.git
	cd mock-http-server && npm install

# 
setup-config:
	# Configure ting.module
	drush vset -y ting_agency 100200
	drush vset -y ting_search_url 'http://oss-services.dbc.dk/opensearch/4.0.1/'
	drush vset -y ting_search_profile test

	# Configure openlist.
	drush vset -y ting_search_profileting_openlist_prefix test
	drush vset -y ting_openlist_wsdl 'http://v2_test.openlist.server003.b14cms.dk/?wsdl'

	# Activate consent and set config.
	drush vset -y user_consent_activate true
	drush vset -y loan_history_store_title_first_time ''
	drush vset -y loan_history_store_title ''
	drush php-eval 'variable_set("loan_history_store_description_first_time", array("value" => ""));'
	drush php-eval 'variable_set("loan_history_store_description", array("value" => ""));'

setup-phantomjs:
	phantomjs --webdriver=4444 &

# Start mock server in recording mode.
mock-record:
	mkdir -p fixtures/opensearch fixtures/openlist
	./mock-http-server/bin/mock-http-server 9000 --record=opensearch.addi.dk --fixtures=fixtures/opensearch &
	./mock-http-server/bin/mock-http-server 9001 --record=v2_test.openlist.server003.b14cms.dk --fixtures=fixtures/openlist &

# Start mock server in playback mode.
mock-playback:
	./mock-http-server/bin/mock-http-server 9000 --fixtures=fixtures/opensearch &
	./mock-http-server/bin/mock-http-server 9001 --fixtures=fixtures/openlist &

# Clean out recorded mocks.
mock-clean:
	rm -rf fixtures/*

# Stop mock server.
mock-stop:
	pkill -f ./mock-http-server/bin/mock-http-server

# Configure site to use mock services.
mock-use:
	drush vset -y ting_search_url 'http://$(MOCK_HOST):9000/4.0.1/'
	drush vset -y ting_openlist_wsdl 'http://$(MOCK_HOST):9001/?wsdl'

# Configure site to use test services.
mock-unuse:
	drush vset -y ting_search_url 'http://oss-services.dbc.dk/opensearch/4.0.1'
	drush vset -y ting_openlist_wsdl 'http://v2_test.openlist.server003.b14cms.dk/?wsdl'
