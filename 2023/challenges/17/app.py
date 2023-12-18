#
# ref: https://topaz.github.io/paste/#XQAAAQBUBAAAAAAAAAAzHIoib6py7i/yVWhl9dSCjkM4GPHrtd8B89i1ferannGa/iSPEhSfwXSQjO97mxh41OLgLuTQT5wZp7bL+YFnrXGCd60/JWIJ5WDSmI0hG6+qMtQOj2QXDJktThQfZ6yIaRD6IZd0P7FmvoN12N2kW/FQ0qpK6T1z6dICqSYqO0/Ksi5YYpqJcNN8deanbLf8wUaZ+5epfmFAoDmvOVTVXvFnKco4Us7YFjds3fPaa954Jf59LhozJtJsgKGwzGe88BZZsMONFj7eorNEm64UlpMkbwm3AX/f5q4PRVDYidntpWQAOacIkW6P5qxfeTyoBlErtzaTP5jf8rGuQiZfpmE5Y+4Rnyq0tOA0sjBsg5VtPNZsYnWssqb1QXz/C9k+GpgJWC7wibMKcO6LzbFeP3L9zi6gzg1cbzGnh0VMsBaat3ydPU977qz/+phzmMAn4TORuFsJneXjbnokMULP7VkIyB3iY9wyPR3Fq8G09fvU/abyU9ZXNGFZvdfeH1RXmRxd638aQUjWEzugbC7VD4vM/iJEkhhJddEKWgl3642Z0Lgb/RrESf8sAicMP7ehT4u8TNRq4ZKPJJp7DvevdrsSJJ8vM+GuqPD+dqa5/Ee3qsJYdjyCm5j1CpYrJSL+KBjeUWho+l5mablrGuP6/R3+dfiEOfJY0p8nxp+l2C3T+nwpw5s3hgOKXYLdTb+HaMceyd+EAz4R32xkCurWxa5x18ym7USc/CFfmlk5M1WTmgYgShgeG39+cMf/gaRH1A==
#

from heapq import heappop, heappush
ll = [[int(y) for y in x] for x in open('input.txt').read().strip().split('\n')]
DIRS = [(0, 1), (1, 0), (0, -1), (-1, 0)]
def inr(pos, arr):
    return pos[0] in range(len(arr)) and pos[1] in range(len(arr[0]))
def run(mindist, maxdist):
    # cost, x, y, disallowedDirection
    q = [(0, 0, 0, -1)]
    seen = set()
    costs = {}
    while q:
        cost, x, y, dd = heappop(q)
        if x == len(ll) - 1 and y == len(ll[0]) - 1: # goal x, goal y
            return cost
        if (x, y, dd) in seen:
            continue
        seen.add((x, y, dd))
        for direction in range(4):
            costincrease = 0
            if direction == dd or (direction + 2) % 4 == dd:
                # can't go this way
                continue
            for distance in range(1, maxdist + 1):
                xx = x + DIRS[direction][0] * distance
                yy = y + DIRS[direction][1] * distance
                if inr((xx, yy), ll):
                    costincrease += ll[xx][yy]
                    if distance < mindist:
                        continue
                    nc = cost + costincrease
                    if costs.get((xx, yy, direction), 1e100) <= nc:
                        continue
                    costs[(xx, yy, direction)] = nc
                    heappush(q, (nc, xx, yy, direction))

print(run(1, 3))
print(run(4, 10))
