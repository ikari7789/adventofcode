
#include<stdio.h>

int determineLeaves(int currentIndex, int (*joltages)[], int size)
{
    if (currentIndex + 1 >= size) {
        return 1;
    }

    int current = (*joltages)[currentIndex];
    int next = (*joltages)[currentIndex + 1];
    int diff = next - current;

    ++currentIndex;

    int sum = 0;

    while (diff >= 1 && diff <= 3) {
        sum += determineLeaves(currentIndex, joltages, size);

        if (currentIndex + 1 >= size) {
            break;
        }
        
        next = (*joltages)[currentIndex + 1];
        diff = next - current;

        ++currentIndex;
    }

    return sum;
}

int main()
{
    // int joltages[] = {
    //      0,  1,  4,  5,  6,  7,  10,  11,  12,  15,
    //     16, 19, 22,
    // };
    // int size = 13;

    // int joltages[] = {
    //      0,  1,  2,  3,  4,  7,  8,  9, 10, 11,
    //     14, 17, 18, 19, 20, 23, 24, 25, 28, 31,
    //     32, 33, 34, 35, 38, 39, 42, 45, 46, 47,
    //     48, 49,
    // };
    // int size = 32;

    int joltages[] = {
          0,   1,   2,   3,   6,   7,   8,   9,  12,  13,
         14,  15,  16,  19,  20,  21,  22,  23,  26,  27,
         28,  29,  30,  33,  34,  35,  38,  39,  40,  41,
         42,  45,  46,  47,  48,  49,  52,  53,  54,  57,
         60,  61,  62,  63,  64,  67,  70,  71,  74,  77,
         78,  79,  80,  83,  84,  85,  86,  87,  90,  91,
         92,  93,  94,  97,  98, 101, 102, 105, 106, 107,
        108, 111, 112, 113, 114, 115, 118, 121, 124, 125,
        126, 127, 130, 131, 132, 133, 134, 137, 140, 141,
        142, 145, 146, 147, 148, 151, 154, 155, 158, 159,
        160, 161, 162, 165, 166, 167, 168, 169, 172,
    };
    int size = 119;

    int count = determineLeaves(0, &joltages, size);
    printf("Count %d\n", count);

    return 0;
}