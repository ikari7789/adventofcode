<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

/**
 * Approach courtesy: https://topaz.github.io/paste/#XQAAAQDOBgAAAAAAAAAeD8qHAhQB8nz+EazN00NCsmTxjvFDXkt5PAJ7pf6ocRA37OMUY7b9R0iDZmfWyvOspxWvdgYSAE0hGjEx81hqzalYbORDAzRHVMFykQU14jKU80IHdrS34wMZbRGIjaajUwuUkjp5s1IbXqU1EcpkmWBhqikVL03ohAqLzawnrNYIK3gKhxwYjHkiBlu5atE8HXrDX9MCUuAGY1J+/ij+yqFhokzUTMr64br+VJLAzani6YOSG2mRYJX7ShIU7bJEcnoYBjNsloER6lLA/QQzp735FQ2eaX1qxt2iPui7z0Tk665m46p73hHBg19lIvpqQ72kzRVEc0twK8upEQKXrXVnIDanx9g8yt5mTmYaIuJMJx5yVN86z00j+BM6s+oreygTzsl1mSRNaMBOCusrxsN69VbHyfNtoHCN4m2z7p4Br+HuFgKUbLTJoZwURY1QRtxP0LKSlhjtftff1IUY4+VSydYMPQBDc0cyCwWN0FOgCMXOo+ZFaHz/CRhA6JV/s+cZCXsgmAqtp+lQyWYY+GD74/bw6lrao62tiuhZA8r77GTRozCekBo5+GjsUqSyrSoVUidnZ6HQYHlpfI1isWywp7sBhkOyzcRphieAmtYYnmRWkZFratnlbkmhe4q9n9aeSkLn6VXM0+5QNYC2B+Zqnu3KXt2MxQxJYmz1rlPyJPRCj7Jl9IGY1x2Frs2KGBh07/eWcVbB3AnsM2plxRaCO7rGarCZ7DowLJnHy9JrzXgrZsAVaNtRb/OQ0qYtboftzNKx5XMG00GSOV6ysSYuC+6FZ2T+hZg1VDiXaAo1JKpoWf+bPAOjfzMTMhO2NyqlicrUU+NBWVd/F1M0YVgZLKEsMQJzYeYQ+qxwTMz+ky1fWP7i5Ov9JJqEOj6RJBDYPsGoMrbf9ojpbkLv5dP+DG/vjh3N4XchMqvjmWKX4ZliQufleqptxYgmWk3/9ccckg==
 */
function countSpringArrangements(
    string $springs,
    array $groups,
    array $state = [0, 0, 0],
    array &$cache = []
): int {
    $key = implode(':', $state);

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    [
        $springsIndex,
        $groupsIndex,
        $length,
    ] = $state;

    // End of springs
    if ($springsIndex === strlen($springs)) {
        if ($groupsIndex === count($groups) - 1 && $length === $groups[$groupsIndex]) {
            ++$groupsIndex;
            $length = 0;
        }

        return (int) ($groupsIndex === count($groups) && $length === 0);
    }

    $result = 0;

    // Operational spring
    if (str_contains('.?', $springs[$springsIndex])) {
        if ($length === 0) {
            $result += countSpringArrangements($springs, $groups, [$springsIndex + 1, $groupsIndex, 0], $cache);
        } elseif ($groupsIndex < count($groups) && $groups[$groupsIndex] === $length) {
            $result += countSpringArrangements($springs, $groups, [$springsIndex + 1, $groupsIndex + 1, 0], $cache);
        }
    }

    // Damaged spring
    if (str_contains('#?', $springs[$springsIndex])) {
        // Starting or continuing a group
        $result += countSpringArrangements($springs, $groups, [$springsIndex + 1, $groupsIndex, $length + 1], $cache);
    }

    return $cache[$key] = $result;
}

$sum = 0;

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $springs,
        $groups,
    ] = explode(' ', $line);

    $groups = array_map('intval', explode(',', $groups));

    $sum += countSpringArrangements($springs, $groups);
}

printf('Total possible arrangements: %d' . PHP_EOL, $sum);

$sum = 0;

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $springs,
        $groups,
    ] = explode(' ', $line);

    $springs = implode('?', array_fill(0, 5, $springs));
    $groups = implode(',', array_fill(0, 5, $groups));

    $groups = array_map('intval', explode(',', $groups));

    $sum += countSpringArrangements($springs, $groups);
}

printf('Total possible arrangements after unfolding: %d' . PHP_EOL, $sum);
