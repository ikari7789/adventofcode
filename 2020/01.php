<?php

// Find two numbers from the list that when summed equal the target and multiply those numbers together
// Find three numbers from the list that when summed equal the target and multiply those numbers together

$expenses = [
    1975,
    1446,
    1902,
    1261,
    1783,
    1535,
    1807,
    1606,
    1685,
    1933,
    1930,
    1813,
    1331,
    1986,
    1379,
    1649,
    1342,
    1206,
    1832,
    1464,
    1840,
    1139,
    1316,
    1366,
    593,
    1932,
    1553,
    1065,
    2004,
    1151,
    1345,
    1026,
    1958,
    1778,
    1987,
    1425,
    1170,
    1927,
    1487,
    1116,
    1612,
    2005,
    1977,
    1691,
    1964,
    398,
    1621,
    1542,
    1929,
    1102,
    1993,
    1426,
    1349,
    1280,
    1775,
    849,
    1344,
    1940,
    1707,
    1562,
    1979,
    1325,
    1610,
    559,
    1812,
    1938,
    1572,
    1949,
    1136,
    161,
    1893,
    1207,
    1363,
    1551,
    1333,
    1904,
    1332,
    1450,
    1773,
    1216,
    1185,
    1881,
    1835,
    1460,
    1277,
    1374,
    1568,
    1731,
    1365,
    1719,
    1749,
    1371,
    1602,
    1108,
    1030,
    1859,
    1875,
    1976,
    1837,
    1768,
    1873,
    1226,
    1533,
    1601,
    1394,
    1422,
    1219,
    1269,
    1793,
    1195,
    1234,
    1575,
    1882,
    1223,
    1826,
    521,
    1161,
    1738,
    1506,
    1574,
    1337,
    1509,
    1430,
    1496,
    1318,
    1400,
    1852,
    1670,
    1898,
    1858,
    1950,
    1870,
    1920,
    868,
    1814,
    1853,
    1911,
    1907,
    1713,
    1281,
    1759,
    1210,
    1350,
    1035,
    1585,
    1765,
    1220,
    1125,
    1714,
    1810,
    1002,
    1356,
    1192,
    1452,
    1236,
    1482,
    1716,
    1681,
    1323,
    1923,
    1876,
    1792,
    1346,
    1891,
    1721,
    1056,
    1675,
    1518,
    1540,
    1068,
    1563,
    1942,
    1668,
    1653,
    1357,
    1632,
    1128,
    1726,
    1586,
    1998,
    1138,
    1510,
    1022,
    1480,
    1434,
    1305,
    1861,
    1623,
    1009,
    1339,
    1159,
    1085,
    1578,
    1689,
    1091,
    1874,
    1043,
    1737,
    1704,
    1515,
];
$targetExpense = 2020;

// Addition pattern

// 2 items
// 0 + 1, 2, 3, 4, 5, 6
// 1 + 2, 3, 4, 5, 6
// 2 + 3, 4, 5, 6
// 3 + 4, 5, 6
// 4 + 5, 6
// 5 + 6

// 3 items
// 0 + 1 + 2, 3, 4, 5 ,6
// 0 + 2 + 3, 4, 5, 6
// 0 + 3 + 4, 5, 6
// 0 + 4 + 5, 6
// 0 + 5 + 6
// 1 + 2 + 3, 4, 5, 6
// 1 + 3 + 4, 5, 6
// 1 + 4 + 5, 6
// 1 + 5 + 6
// 2 + 3 + 4, 5, 6
// 2 + 3 + 5, 6
// 2 + 3 + 6
// 3 + 4 + 5, 6
// 3 + 5 + 6
// 4 + 5 + 6

// Two numbers
$totalExpenses = count($expenses);
$expenseCombos = [];
for ($index1 = 0; $index1 < $totalExpenses; ++$index1) {
    $expense1 = $expenses[$index1];

    for ($index2 = $index1 + 1; $index2 < $totalExpenses; ++$index2) {
        $expense2 = $expenses[$index2];

        if ($expense1 + $expense2 === $targetExpense) {
            $expenseCombos[] = [$index1, $index2];
        }
    }
}

foreach ($expenseCombos as $expenseCombo) {
    $expense1 = $expenses[$expenseCombo[0]];
    $expense2 = $expenses[$expenseCombo[1]];

    printf('%4d x %4d        = %10d'.PHP_EOL, $expense1, $expense2, $expense1 * $expense2);
}

// Three numbers
$totalExpenses = count($expenses);
$expenseCombos = [];
for ($index1 = 0; $index1 < $totalExpenses; ++$index1) {
    $expense1 = $expenses[$index1];

    for ($index2 = $index1 + 1; $index2 < $totalExpenses; ++$index2) {
        $expense2 = $expenses[$index2];

        for ($index3 = $index2 + 1; $index3 < $totalExpenses; ++$index3) {
            $expense3 = $expenses[$index3];

            if ($expense1 + $expense2 + $expense3 === $targetExpense) {
                $expenseCombos[] = [$index1, $index2, $index3];
            }
        }
    }
}

foreach ($expenseCombos as $expenseCombo) {
    $expense1 = $expenses[$expenseCombo[0]];
    $expense2 = $expenses[$expenseCombo[1]];
    $expense3 = $expenses[$expenseCombo[2]];

    printf('%4d x %4d x %4d = %10d'.PHP_EOL, $expense1, $expense2, $expense3, $expense1 * $expense2 * $expense3);
}
