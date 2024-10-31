#!/bin/bash

vendor/bin/php-cs-fixer fix --config=.linter/.php-cs-fixer.dist.php -vvv --allow-risky=yes src || exit 1

vendor/bin/phpcbf --standard=.linter/phpcs.xml.dist -p src || exit 1

vendor/bin/phpcs --standard=.linter/phpcs.xml.dist src || exit 1

vendor/bin/phpmd src text .linter/phpmd.ruleset.xml -vv --color --cache || exit 1

vendor/bin/phpstan analyse --ansi --configuration .linter/phpstan.dist.neon -- src || exit 1

vendor/bin/phpinsights analyse --ansi --config-path=.linter/phpinsights.php -- src