import re

#
# https://topaz.github.io/paste/#XQAAAQASCwAAAAAAAAA0m0pnuFI8c/T1eyX1gBYG3sXjjiAFJvHyvLVQmxsWmMZKlWIb3k+KfSgrdo7g+6Ed08ITx+EDqbO8hkMDGKs/y9btsWIcbuUndRHsR5qlBIXYr/KrKk2QjTy38rQoU2Mx9STnCrliL8Favd7+FWCAewjp66ojBdziuNecPrGIe7m2PFPx4mCa8Ch1g+cGrsPt0L7uoSZKzETPh2QVlRSv4uAwKH2XewHnLU65KPUR9jInheY3GkO1I4AJDqa+4k3Dukl2q7eX4760dSWW4vCwJcbIG7vpH3B1umMagaUtYZT+kDzi1Lpo2NyxmVmr8LOB/2oIREkRAPWYTucibOir6GAZiQHSx3vll7lJ1n9jEfSDc1wNonyP5SEr3BzTmgklhfNCkbS5MIy7+GYuLEK+Wiy6xZoBmTkSf0pj4pG5jSjuWMC0dJKWI3gUrRTZOEE/qjvGRdr9qpf6OwZv4guULxLafK7iaYp8lzUVM8mrb95W2Qlz5F6CwOnR8qMwAKR9z8X27XcI3mFMHgT4+C9CgXol7sAFUaqqqt7S12OuMOizmnFyXWvTv7SR6UT4IXh/tDdIeICFYPt+NBdsrAVkiPdQrwCbPTHvFlbjZS0QrhWYAo3W8atvnoTANLwkoirYLb00bOQa0Eyd5yT1r8P+7+DfmNHvwJh6VvCJl5OKmCm7KeMMH6zg6YV13EJcOuEmN/8GkuMTYWVXev4+QkrNt/4k9oRNxKMUkYTp4aaKVnZGXNlp8IlYUniHFv9la8V9FXxXCWymkWXk8dQhMkQQyfG9Kt9dlCaorRWMnwPM2wVaSJl4Qm64oQgunLh8gZMvrwTuTYAFrxgATzceArm1PyupCPrn+gnUvR0XbhEsSvt1N6TBrL2fSuINkEUX95F3soVZEAPPBaDpocaB/KiFOiL3tHwovaysyRam6M4yIkgC6fgk1NRGSbaxeJfxYJmy9mhIAlV/7CBP6CF/jKJVsbbiE3hf6JEKjUnbudiybg10/xngrxGlFtlNB6nEA8q0UC+/LOsGg43Pm1cgFGZJ2gCKu9hk/711I0pWbnDtgCA8wkNK5VodEqWs0CxVrtkc1lLgRij3wh6/97yX6KceQ9MG6Qa25TmsdKNV1ylqV2PHmcr/I53n8Ru9rry3lzi9QSho6nz87UkbPRsoEpHUEAEcuA6JmB+jmi+967XBjFcbdef4npHtWZegHJN6isgfm1JJbJwnxIx91t62zO2jbYRETTUcGRH7ONQLEU6WsN462Zm3Nhvgqt6SfF84eQ6hesw8ABKiRn3kw+dmmvSRAcFAhMjR/GM1IA==
#

rule_dict = { 'A': lambda _: True, 'R': lambda _: False }

def process_rule(rule_code):
    m = re.match(r'^(\w+)\{(.*)\}', rule_code)
    rule_name = m.group(1)
    rule = 'lambda part: '
    for instr in m.group(2).split(','):
        if ':' not in instr:
            rule += "rule_dict['{}'](part)".format(instr)
        else:
            cond, cons = instr.split(':')
            prop, comp, val = cond[0], cond[1], int(cond[2:])
            rule += "rule_dict['{}'](part) if part['{}'] {} {} else ".format(cons, prop, comp, val)
    rule_dict[rule_name] = eval(rule)

def part1():
    with open('input.txt') as f:
        # split on blank line
        data = f.read().split('\n\n')

    for rule in data[0].splitlines():
        process_rule(rule)

    parts = [{p1.split('=')[0]: int(p1.split('=')[1]) \
        for p1 in part[1:-1].split(',')} for part in data[1].splitlines()]

    part1 = sum([sum(part.values()) for part in parts if rule_dict['in'](part)])
    print ("Part 1:",part1)

def lets_go_helper(part, rules, rule):
    rv = 0

    for step in rules[rule]:
        if ':' not in step:
            rv += lets_go(part, rules, step)
            continue
        condition, next_rule = step.split(':')
        x = condition[0]
        xv = part[x]
        lgt = condition[1]
        val = int(condition[2:])

        # Determine if the rule applies 100%
        if (lgt == '<' and xv[0] < val and xv[1] < val) or \
        (lgt == '>' and xv[0] > val and xv[1] > val):
            rv += lets_go(part, rules, next_rule)
            break

        # Check if the rule doesn't apply at all
        if (lgt == '<' and xv[0] >= val and xv[1] >= val) or \
        (lgt == '>' and xv[0] <= val and xv[1] <= val):
            continue

        # Splitting case
        modified_part = part.copy()
        if lgt == '<':
            modified_part[x] = (xv[0], val - 1)
            part[x] = (val, xv[1])
        else: # lgt == '>'
            modified_part[x] = (val + 1, xv[1])
            part[x] = (xv[0], val)

        rv += lets_go(modified_part, rules, next_rule)
    return rv

def lets_go(part, rules, rule):
    match rule:
        case 'A':
            return (part['x'][1]-part['x'][0]+1) * (part['m'][1]-part['m'][0]+1) * \
                (part['a'][1]-part['a'][0]+1) * (part['s'][1]-part['s'][0]+1)
        case 'R':
            return 0
        case _:
            return lets_go_helper(part, rules, rule)

def part2():
    with open('input.txt') as f:
        # split on blank line
        data = f.read().split('\n\n')

    xmas = {'x': (1,4000), 'm': (1,4000), 'a': (1,4000), 's': (1,4000)}
    p2rules = {rule[:rule.index('{')] : rule[rule.index('{')+1:-1].split(',') for rule in data[0].splitlines()}
    print ("Part 2:", lets_go(xmas, p2rules, 'in'))

part1()
part2()
