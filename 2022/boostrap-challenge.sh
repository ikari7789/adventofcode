#!/bin/sh

DAY=${1}

if [ ! -n "${DAY}" ]; then
    echo 'Must supply a day.' 2>&1
    exit 1
fi

CHALLENGES_DIR="./challenges/${DAY}"

mkdir -p "${CHALLENGES_DIR}"
touch "${CHALLENGES_DIR}/example_input.txt"
touch "${CHALLENGES_DIR}/input.txt"
cat > "${CHALLENGES_DIR}/app.php" <<'EOF'
<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {

}
EOF
