package main

import (
	"fmt"
)

func insert(array []byte, index int, element byte) []byte {
	return append(array[:index], append([]byte{element}, array[index:]...)...)
}

func insertPolymer(polymer []byte, insertionRules map[string]byte) []byte {
	for index := 0; index < len(polymer)-1; index++ {
		slice := polymer[index : index+2]

		for pair, insertion := range insertionRules {
			if string(slice) == pair {
				polymer = insert(polymer, index+1, insertion)
				index += 1

				break
			}
		}
	}

	return polymer
}

func countValues(polymer []byte) map[byte]int {
	duplicateFrequency := make(map[byte]int)

	for _, item := range polymer {
		// check if the item/element exists in the duplicateFrequency map
		_, exist := duplicateFrequency[item]

		if exist {
			duplicateFrequency[item] += 1
		} else {
			duplicateFrequency[item] = 1
		}
	}

	return duplicateFrequency
}

func main() {
	debug := false
	numberOfStepsPart1 := 10
	numberOfStepsPart2 := 40

	// example input
	polymerTemplate := []byte{
		'N', 'N', 'C', 'B',
	}

	insertionRules := map[string]byte{
		"CH": 'B',
		"HH": 'N',
		"CB": 'H',
		"NH": 'C',
		"HB": 'C',
		"HC": 'B',
		"HN": 'C',
		"NN": 'C',
		"BH": 'H',
		"NC": 'B',
		"NB": 'B',
		"BN": 'B',
		"BB": 'N',
		"BC": 'B',
		"CC": 'N',
		"CN": 'C',
	}

	// input
	// polymerTemplate := []byte {
	// 	'N', 'N', 'S', 'O', 'F', 'O', 'C', 'N', 'H', 'B', 'V', 'V', 'N', 'O', 'B', 'S', 'B', 'H', 'C', 'B',
	// }

	// insertionRules := map[string]byte {
	// 	"HN": 'S',
	// 	"FK": 'N',
	// 	"CH": 'P',
	// 	"VP": 'P',
	// 	"VV": 'C',
	// 	"PB": 'H',
	// 	"CP": 'F',
	// 	"KO": 'P',
	// 	"KN": 'V',
	// 	"NO": 'K',
	// 	"NF": 'N',
	// 	"CO": 'P',
	// 	"HO": 'H',
	// 	"VH": 'V',
	// 	"OV": 'C',
	// 	"VS": 'F',
	// 	"PK": 'H',
	// 	"OS": 'S',
	// 	"BF": 'S',
	// 	"SN": 'P',
	// 	"NK": 'N',
	// 	"SV": 'O',
	// 	"KB": 'O',
	// 	"ON": 'O',
	// 	"FN": 'H',
	// 	"FO": 'N',
	// 	"KV": 'S',
	// 	"CS": 'C',
	// 	"VO": 'O',
	// 	"SP": 'O',
	// 	"VK": 'H',
	// 	"KP": 'S',
	// 	"SK": 'N',
	// 	"NC": 'B',
	// 	"PN": 'N',
	// 	"HV": 'O',
	// 	"HS": 'C',
	// 	"CN": 'N',
	// 	"OO": 'V',
	// 	"FF": 'B',
	// 	"VC": 'V',
	// 	"HK": 'K',
	// 	"CC": 'H',
	// 	"BO": 'H',
	// 	"SC": 'O',
	// 	"HH": 'C',
	// 	"BV": 'P',
	// 	"OB": 'O',
	// 	"FC": 'H',
	// 	"PO": 'C',
	// 	"FV": 'C',
	// 	"BK": 'F',
	// 	"HB": 'B',
	// 	"NH": 'P',
	// 	"KF": 'N',
	// 	"BP": 'H',
	// 	"KK": 'O',
	// 	"OH": 'K',
	// 	"CB": 'H',
	// 	"CK": 'C',
	// 	"OK": 'H',
	// 	"NN": 'F',
	// 	"VF": 'N',
	// 	"SO": 'K',
	// 	"OP": 'F',
	// 	"NP": 'B',
	// 	"FS": 'S',
	// 	"SH": 'O',
	// 	"FP": 'O',
	// 	"SF": 'V',
	// 	"HF": 'N',
	// 	"KC": 'K',
	// 	"SB": 'V',
	// 	"FH": 'N',
	// 	"SS": 'C',
	// 	"BB": 'C',
	// 	"NV": 'K',
	// 	"OC": 'S',
	// 	"CV": 'N',
	// 	"HC": 'P',
	// 	"BC": 'N',
	// 	"OF": 'K',
	// 	"BH": 'N',
	// 	"NS": 'K',
	// 	"BN": 'F',
	// 	"PC": 'C',
	// 	"CF": 'N',
	// 	"HP": 'F',
	// 	"BS": 'O',
	// 	"PF": 'S',
	// 	"PV": 'B',
	// 	"KH": 'K',
	// 	"VN": 'V',
	// 	"NB": 'N',
	// 	"PH": 'V',
	// 	"KS": 'B',
	// 	"PP": 'V',
	// 	"PS": 'C',
	// 	"VB": 'N',
	// 	"FB": 'N',
	// }

	if debug {
		fmt.Printf("Template:      %s\n", string(polymerTemplate))
	}

	polymer := polymerTemplate
	for step := 1; step <= numberOfStepsPart2; step++ {
		polymer = insertPolymer(polymer, insertionRules)

		fmt.Printf("After step %2d:", step)

		if debug {
			fmt.Printf(" %s", string(polymer))
		}

		fmt.Println()

		if step == numberOfStepsPart1 || step == numberOfStepsPart2 {
			polymerCounts := countValues(polymer)

			index := 0
			leastCommon := 0
			mostCommon := 0
			for _, count := range polymerCounts {
				if index == 0 {
					leastCommon = count
					mostCommon = count
				}

				if count < leastCommon {
					count = leastCommon
				}

				if count > mostCommon {
					mostCommon = count
				}

				index++
			}

			fmt.Printf("Step %d - Most common minus least common: %d\n", step, mostCommon-leastCommon)
		}
	}
}
