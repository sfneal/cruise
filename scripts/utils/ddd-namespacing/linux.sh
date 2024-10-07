#!/usr/bin/env bash

set -e

sed -i'' '/"autoload": {/,/},/c\
\    "autoload": {\
\        "psr-4": {\
\            "App\\\\": "app/App",\
\            "Domain\\\\": "app/Domain",\
\            "Support\\\\": "app/Support",\
\            "Database\\\\Factories\\\\": "database/factories/",\
\            "Database\\\\Seeders\\\\": "database/seeders/"\
\        }\
\    },\
' composer.json
