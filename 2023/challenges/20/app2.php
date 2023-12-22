<?php

/**
 * https://topaz.github.io/paste/#XQAAAQBnCAAAAAAAAAAeD8qHAhQB8nz+EazN00NCsmTxjvFDXkt5PAJ7pf6ocRA37OMUY7b9R0iDZmfWyvOspxWvdgYSAE0hGjEx81hZm7L1TSf8FOxdriwtMNRvJM+oQrJq9mPk9182mFDzxH6MNMqn6JITQaBvYm2EvwP21OFqw15pwIC6MKTOmDir/gLh/zj1ztj/67bSWFSNvhZqIDNbbXpY6Gcu6hZGY0U3n6JnHXpa7vpJHuZHLtghN+U4gSL2QzVhPlugwLOXmkJnNlaL9tn4o67n8LBjrBQqhNIaLjbOkHbYSaBZ/pROlODPY093D0c6fVRENPIBbwbXB+4EJy7r6JJviW9QNOul1PikuJvdrkiVjBhhzNJFQdIMDZ9daI4wGKk08GLAi2GKiMPbkuSYB77U/DnOM5OcShXcnYzHY8OTkF2mcLb/T7aameOdqL9zn7Sgnfm0r19TcBqZmLTrUlq1JBLJ/TS+UY5gUz+IhphZaBH9qp+lKdHx4Dw7SaofhpopSmMs3VyJm3kfOG2ubrWpaV3jcenJCBgS0qFWaX3mQH+1SVuf5nwDaKyGZQtk0n+QS9RyL+iG050biyuKkpKLGVjzQQcTCOjvLrVSaahOdCI1eXH72908tJqLtAGZOLCaUlaqS4CJdVNZKJqoMMgG0LNOYMcsvPZ5BqHfDOGONEgI9AC1ATVvpBuJGaqru80x1vzS+b/u3WSEkZvHKJs7LgkrMMCGXKB9cx+S9FuN1Zj0VYwE3moQ1sbhf6LOWbf8MWVUDDMjBCJIoJrvZacji0RgLeIveGiyslTd6eHfZF478xjqlBYtezfmbvufSdZxPOYXEXy9lUM7vLIg/UyDDYtMJdvnygAQMtT0bw7Jojg0rYddcMOY8tjCza1YrDZxGovvaukitrHOSDe5D2pMCY3lhF9RIc65L8LkXlXbtCniSV6GoszcW5lprttR+rwVahVKjmqfMu928tdLjmzoBm76Wpzdj9vj24gonLsyGU2BOluXB6wijX2h9oT4ii1LNDjMphwj9N67cRs936gJfEz3hE9VWSPct7j2BuHGtRG+4+IoqFMdAsUHF2RBacMPRcTOpEol3RR+sdczFrpP4zu734u5C6ppz4yCs5GjmXvxHUxMHegvC/FrZZsRqrnSz4v8PUb5JdUxL/qzdeigSdYEfih/EtcXkYwf1V9X2PJA0A0jPB/m0tXwpJax1SVGt/IX3uGnuf1c3H8=
 */

memory_reset_peak_usage();
$start_time = microtime(true);

$_fp = fopen($argv[1] ?? "input.txt", "r");

const TYPE = 0, TO = 1, DATA = 2;

$M = [];
while (!feof($_fp) && $line = trim(fgets($_fp))) {
    [$id, $to] = explode(" -> ", $line);
    $type = $id[0];
    if ($type != "b") {
        $id = substr($id, 1);
    }
    $M[$id] = [$type, explode(", ", $to), ($type == "&" ? [] : 0)];
}
fclose($_fp);

foreach ($M as $id => [, $to,]) {
    foreach ($to as $d) {
        if (!isset($M[$d])) {
            $M[$d] = [$d, [], $id];
        }
        if ($M[$d][TYPE] == "&") {
            $M[$d][DATA][$id] = 0;
        }
    }
}

$part1 = $part2 = 0;
$count = [0, 0];
$CYCLES = $M[$M["rx"][DATA]][DATA];
$FOUND = [];

for ($i = 0;; $i++) {
    if ($i == 1000) {
        $part1 = $count[0] * $count[1];
    }

    $Q = [["broadcaster", "button", 0]];
    while (count($Q)) {
        [$id, $from, $pulse] = array_shift($Q);
        // echo "{$from} -".["low","high"][$pulse]."-> {$id}\n";

        $count[$pulse]++;

        if ($M[$id][TYPE] == "%") {
            if ($pulse) {
                continue;
            }
            $pulse = $M[$id][DATA] = (int)!$M[$id][DATA];
        } elseif ($M[$id][TYPE] == "&") {
            if (!$pulse && isset($CYCLES[$id])) {
                if ($CYCLES[$id] > 0 && !isset($FOUND[$id])) {
                    $FOUND[$id] = $i - $CYCLES[$id];
                    //echo "{$id} CYCLE FOUND at {$i} length {$FOUND[$id]}\n";
                    if (count($FOUND) == count($CYCLES)) {
                        $part2 = array_shift($FOUND);
                        foreach ($FOUND as $n) {
                            $part2 = gmp_lcm($part2, $n);
                        }
                        break 2;
                    }
                } else {
                    $CYCLES[$id] = $i;
                }
            }
            $M[$id][DATA][$from] = $pulse;
            $pulse = (int)!(array_sum($M[$id][DATA]) == count($M[$id][DATA]));
        }

        foreach ($M[$id][TO] as $to) {
            $Q[] = [$to, $id, $pulse];
        }
    }
}

echo "part 1: {$part1}\n";
echo "part 2: {$part2}\n";

echo "Execution time: " . round(microtime(true) - $start_time, 4) . " seconds\n";
echo "   Peak memory: " . round(memory_get_peak_usage() / pow(2, 20), 4), " MiB\n\n";
