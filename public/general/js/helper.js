// GLOBAL VARIABLE

let localeMapCurrency = {
	USD: {
		symbol: '$',
		pattern: '$ #,##0.00',
		code: 'en-US',
		decimal: 2
	}, // United States Dollar (USD)
	JPY: {
		symbol: '¥',
		pattern: '¥ #,##0',
		code: 'ja-JP',
		decimal: 2
	}, // Japanese Yen (JPY)
	GBP: {
		symbol: '£',
		pattern: '£ #,##0.00',
		code: 'en-GB',
		decimal: 2
	}, // British Pound Sterling (GBP)
	EUR: {
		symbol: '€',
		pattern: '€ #,##0.00',
		code: 'en-GB',
		decimal: 2
	}, // Euro (EUR) - Using en-GB for Euro
	AUD: {
		symbol: 'A$',
		pattern: 'A$ #,##0.00',
		code: 'en-AU',
		decimal: 2
	}, // Australian Dollar (AUD)
	CAD: {
		symbol: 'C$',
		pattern: 'C$ #,##0.00',
		code: 'en-CA',
		decimal: 2
	}, // Canadian Dollar (CAD)
	CHF: {
		symbol: 'CHF',
		pattern: 'CHF #,##0.00',
		code: 'de-CH',
		decimal: 2
	}, // Swiss Franc (CHF)
	CNY: {
		symbol: '¥',
		pattern: '¥ #,##0.00',
		code: 'zh-CN',
		decimal: 2
	}, // Chinese Yuan (CNY)
	SEK: {
		symbol: 'kr',
		pattern: 'kr #,##0.00',
		code: 'sv-SE',
		decimal: 2
	}, // Swedish Krona (SEK)
	MYR: {
		symbol: 'RM',
		pattern: 'RM #,##0.00',
		code: 'ms-MY',
		decimal: 2
	}, // Malaysian Ringgit (MYR)
	SGD: {
		symbol: 'S$',
		pattern: 'S$ #,##0.00',
		code: 'en-SG',
		decimal: 2
	}, // Singapore Dollar (SGD)
	INR: {
		symbol: '₹',
		pattern: '₹ #,##0.00',
		code: 'en-IN',
		decimal: 2
	}, // Indian Rupee (INR)
	IDR: {
		symbol: 'Rp',
		pattern: 'Rp #,##0',
		code: 'id-ID',
		decimal: 0
	}, // Indonesian Rupiah (IDR)
};

// DEBUG & CHECKER HELPER

/**
 * Function: log
 * Description: This function takes in multiple arguments and logs each argument to the console.
 * It iterates through the provided arguments and uses the console.log() function to display each argument's value in the console.
 *
 * @param {...any} args - The arguments to be logged to the console.
 * 
 * @example
 * log("Hello", 42, [1, 2, 3]);
 */
const log = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
}

/**
 * Function: dd
 * Description: This function is similar to the 'log' function, but it additionally throws an error after logging the provided arguments.
 * It is typically used for debugging purposes to terminate program execution and print diagnostic information at a specific point in the code.
 *
 * @param {...any} args - The arguments to be logged to the console before terminating the execution.
 * @throws {Error} - Always throws an error with the message "Execution terminated by dd()".
 * 
 * @example
 * dd("Error occurred", { code: 500 });
 */
const dd = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
	throw new Error("Execution terminated by dd()");
}

/**
 * Function: isUndef
 * Description: Check if a value is undefined or null.
 *
 * @param {*} value - The value to check.
 * @returns {boolean} - True if the value is undefined or null, otherwise false.
 * 
 * @example
 * // Check if a variable is undefined or null.
 * const undefinedOrNull = null;
 * console.log(isUndef(undefinedOrNull)); // Output: true
 */
const isUndef = (value) => {
	return typeof value === 'undefined' || value === null;
}

/**
 * Function: isDef
 * Description: Check if a value is defined and not null.
 *
 * @param {*} value - The value to check.
 * @returns {boolean} - True if the value is defined and not null, otherwise false.
 * 
 * @example
 * // Check if a variable is defined and not null.
 * const definedNotNull = 42;
 * console.log(isDef(definedNotNull)); // Output: true
 */
const isDef = (value) => {
	return typeof value !== 'undefined' && value !== null;
}

/**
 * Function: isTrue
 * Description: Check if a value is true.
 *
 * @param {*} value - The value to check.
 * @returns {boolean} - True if the value is true, otherwise false.
 * 
 * @example
 * // Check if a variable is true.
 * const trueValue = true;
 * console.log(isTrue(trueValue)); // Output: true
 */
const isTrue = (value) => {
	return value === true;
}

/**
 * Function: isFalse
 * Description: Check if a value is false.
 *
 * @param {*} value - The value to check.
 * @returns {boolean} - True if the value is false, otherwise false.
 * 
 * @example
 * // Check if a variable is false.
 * const falseValue = false;
 * console.log(isFalse(falseValue)); // Output: true
 */
const isFalse = (value) => {
	return value === false;
}

// DATA HELPER

/**
 * Function: isset
 * Description: Checks if a variable is defined and not null.
 *
 * @param {*} variable - The variable to be checked.
 * @returns {boolean} - True if the variable is defined and not null, false otherwise.
 * 
 * @example
 * const result = isset(myVar);
 * if (result) {
 *   // myVar is defined and not null
 * } else {
 *   // myVar is undefined or null
 * }
 */
const isset = (variable) => {
	return isDef(variable);
};

/**
 * Function: trimData
 * Description: Trims leading and trailing whitespace from a given string if it's defined, otherwise returns original text.
 *
 * @param {*} text - The text to be potentially trimmed.
 * @param {string} [mode='a'] - The mode of trimming ('a' for both, 'l' for left, 'r' for right).
 * @returns {string | *} - Returns the trimmed string or the original value if input is not a string.
 * 
 * @example
 * const trimmedText = trimData("   Some text   "); // trimmedText now contains "Some text"
 * const nullResult = trimData(null); // nullResult is null
 * const numberResult = trimData(6); // numberResult return as is
 */
const trimData = (text, mode = 'a') => {
	if (typeof text !== 'string') return text;

	switch (mode) {
		case 'a':
			return text.trim();
		case 'l':
			return text.trimStart ? text.trimStart() : text.trimLeft();
		case 'r':
			return text.trimEnd ? text.trimEnd() : text.trimRight();
		default:
			throw new Error('Invalid mode specified. Use "a" for both, "l" for left, "r" for right trimming.');
	}
};

/**
 * Function: hasData
 * Description: Check if data exists and optionally if a nested key exists within the data.
 *
 * @param {any} data - The data to be checked.
 * @param {string} arrKey - A dot-separated string representing the nested keys to check within the data.
 * @param {boolean} returnData - If true, return the data instead of a boolean.
 * @param {any} defaultValue - The value to return if the data or nested key is not found.
 * @returns {boolean | any} - Returns a boolean indicating data existence or the actual data based on `returnData` parameter.
 */
const hasData = (data = null, arrKey = null, returnData = false, defaultValue = null) => {
	// Base case 1: Check if data is not set, empty, or null
	if (!data || data === null) {
		return returnData ? (defaultValue ?? data) : false;
	}

	// Base case 2: If arrKey is not provided, consider data itself as having data
	if (arrKey === null) {
		return returnData ? (defaultValue ?? data) : true;
	}

	// Replace square brackets with dots in arrKey
	arrKey = arrKey.replace(/\[/g, '.').replace(/\]/g, '');

	// Split the keys into an array
	const keys = arrKey.split('.');

	// Helper function to recursively traverse the data
	const traverse = (keys, currentData) => {
		if (keys.length === 0) {
			return returnData ? currentData : true;
		}

		const key = keys.shift();

		// Check if currentData is an object or an array
		if (currentData && typeof currentData === 'object' && key in currentData) {
			return currentData[key] != null ? traverse(keys, currentData[key]) : (returnData ? (defaultValue ?? null) : false);
		} else {
			// If the key doesn't exist, return the default value or false
			return returnData ? defaultValue : false;
		}
	};

	return traverse(keys, data);
};

/**
 * Function: replaceTextWithData
 * Replaces placeholders in a string with corresponding data values.
 * Placeholders are defined using the specified delimiter (default is '%').
 * If a data value for a placeholder is not found, the placeholder remains unchanged.
 *
 * @param {string} string - The input string containing placeholders.
 * @param {object} data - An object containing key-value pairs for replacement.
 * @param {string} [delimiter='%'] - The delimiter used to define placeholders.
 * @returns {string} - The string with placeholders replaced by data values.
 */
const replaceTextWithData = (string = '', data, delimiter = '%') => {
	// Construct regular expression pattern based on the delimiter
	const pattern = new RegExp(`${delimiter}([^${delimiter}]+)${delimiter}`, 'g');

	// Use regular expression to match placeholders
	return string.replace(pattern, (match, key) => {
		// If a data value exists for the key, replace with the value; otherwise, keep the original placeholder
		return data[key] || match;
	});
};

// STRING HELPER

/**
 * Function: ucfirst
 * Description: Converts the first character of a string to uppercase.
 *
 * @param {string} string - The input string.
 * @returns {string} - The input string with the first character capitalized.
 * 
 * @example
 * const result = ucfirst("hello"); // Result is "Hello"
 */
const ucfirst = (string) => {
	try {
		if (typeof string !== 'string') {
			throw new Error(`An error occurred in ucfirst(): Input must be a string`);
		}
		return string.charAt(0).toUpperCase() + string.slice(1);
	} catch (error) {
		console.error(`An error occurred in ucfirst(): ${error.message}`);
		return string;
	}
}

/**
 * Function: ucwords
 * Description: Capitalizes the first character of each word in a string.
 *
 * @param {string} str - The input string.
 * @returns {string} - The input string with the first character of each word capitalized.
 * 
 * @example
 * const result = ucwords("hello world"); // Result is "Hello World"
 */
const ucwords = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in ucwords(): Input must be a string`);
		}
		return str.toLowerCase().split(' ').map(function (word) {
			return word.replace(word[0], word[0].toUpperCase());
		}).join(' ');
	} catch (error) {
		console.error(`An error occurred in ucwords(): ${error.message}`);
		return str;
	}
}

/**
 * Function: strtoupper
 * Description: Converts the value of string to uppercase
 *
 * @param {string} str - The input string.
 * @returns {string} - The input string with the uppercase.
 * 
 * @example
 * const result = strtoupper('hello'); // Result is "HELLO"
 */
const strtoupper = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in strtoupper(): Input must be a string`);
		}
		return str.toUpperCase();
	} catch (error) {
		console.error(`An error occurred in strtoupper(): ${error.message}`);
		return str;
	}
}

/**
 * Function: strtolower
 * Description: Converts a string to lowercase.
 *
 * @param {string} str - The input string.
 * @returns {string} - The input string converted to lowercase.
 *
 * @example
 * const result = strtolower("Hello World"); // result is "hello world"
 */
const strtolower = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in strtolower(): Input must be a string`);
		}
		return str.toLowerCase();
	} catch (error) {
		console.error(`An error occurred in strtolower(): ${error.message}`);
		return str;
	}
}

/**
 * Function: str_replace
 * Description: Replaces all occurrences of a substring in a string with another substring.
 *
 * @param {string} find - The substring to be replaced.
 * @param {string} replace - The replacement substring.
 * @param {string} string - The input string.
 * @returns {string} - The input string with all occurrences of the search substring replaced by the replace substring.
 *
 * @example
 * const result = str_replace("world", "universe", "Hello world"); // result is "Hello universe"
 */
const str_replace = (find, replace, string) => {
	try {
		if (typeof string !== 'string') {
			throw new Error(`An error occurred in str_replace(): String text must be a string`);
		}

		if (typeof find !== 'string') {
			throw new Error(`An error occurred in str_replace(): Find must be a string`);
		}

		if (typeof replace !== 'string') {
			throw new Error(`An error occurred in str_replace(): Replace must be a string`);
		}

		return string.split(find).join(replace);
	} catch (error) {
		console.error(`An error occurred in str_replace(): ${error.message}`);
		return str;
	}
}

// ARRAY HELPER

/**
 * Function: in_array
 * Description: Checks if a given value exists in the provided array.
 *
 * @param {*} needle - The value to search for in the array.
 * @param {Array} data - The array to search within.
 * @returns {boolean} - True if the value exists in the array, false otherwise.
 * 
 * @example
 * const result = in_array(42, [1, 42, 3]); // result is true
 * const result2 = in_array(45, [1, 42, 3]); // result is false
 */
const in_array = (needle, data) => {
	try {
		if (!Array.isArray(data)) {
			throw new Error("An error occurred in in_array(): data should be an array");
		}

		return data.includes(needle);
	} catch (error) {
		console.error(`An error occurred in in_array(): ${error.message}`);
		return false;
	}
}

/**
 * Function: array_push
 * Description: Adds one or more elements to the end of an array and returns the new length of the array.
 *
 * @param {Array} data - The array to which elements will be added.
 * @param {...*} elements - Elements to be added to the array.
 * @returns {number} - The new length of the array.
 * 
 * @example
 * const myArray = [1, 2];
 * const newLength = array_push(myArray, 3, 4); // myArray is now [1, 2, 3, 4], newLength is 4
 */
const array_push = (data, ...elements) => {
	try {
		if (!Array.isArray(data)) {
			throw new Error("An error occurred in array_push(): data should be an array");
		}

		return data.push(...elements);
	} catch (error) {
		console.error(`An error occurred in array_push(): ${error.message}`);
		return [];
	}
}

/**
 * Function: array_merge
 * Description: Merges multiple arrays into a single array.
 *
 * @param {...Array} arrays - Arrays to be merged.
 * @returns {Array} - The merged array.
 * 
 * @example
 * const mergedArray = array_merge([1, 2], [3, 4], [5, 6]); // mergedArray is [1, 2, 3, 4, 5, 6]
 */
const array_merge = (...arrays) => {
	try {
		for (const array of arrays) {
			if (!Array.isArray(array)) {
				throw new Error("All arguments should be arrays");
			}
		}

		return [].concat(...arrays);
	} catch (error) {
		console.error(`An error occurred in array_merge(): ${error.message}`);
		return [];
	}
}

/**
 * Function: array_key_exists
 * Description: Checks if a specified key exists in an object.
 *
 * @param {*} arrKey - The key to check for existence in the object.
 * @param {Object} data - The object to check for the key's existence.
 * @returns {boolean} - True if the key exists in the object, false otherwise.
 * @throws {Error} - Throws an error if data is not an object.
 * 
 * @example
 * const obj = { name: 'John', age: 30 };
 * const result = array_key_exists('name', obj);
 * // result is true
 */
const array_key_exists = (arrKey, data) => {
	try {

		if (typeof data !== 'object' || data === null) {
			throw new Error("An error occurred in array_key_exists(): data should be an object");
		}

		if (data.hasOwnProperty(arrKey)) {
			return true;
		}

		return false;
	} catch (error) {
		console.error(`An error occurred in array_key_exists(): ${error.message}`);
		return false;
	}
}

/**
 * Function: array_search
 * Description: Searches for a value in an array and returns the corresponding key if found.
 *
 * @param {*} needle - The value to search for in the array.
 * @param {Array} haystack - The array to search in.
 * 
 * @throws Will throw an error if the needle is empty or if the haystack is not an array.
 *
 * @return {number|string|false} - The key of the found element or false if not found.
 *
 * @example
 * const arr = ['apple', 'banana', 'orange'];
 * const result = array_search('banana', arr);
 * // result is 1
 */
const array_search = (needle, haystack) => {
	try {
		if (!Array.isArray(haystack)) {
			throw new Error('The second parameter must be an array.');
		}

		if (needle === '') {
			throw new Error('The search value cannot be empty.');
		}

		for (const [key, value] of Object.entries(haystack)) {
			if (value === needle) {
				return key;
			}
		}

		return false;
	} catch (error) {
		console.error(`An error occurred in array_search(): ${error.message}`);
		return false;
	}
};

/**
 * Function: implode
 * Description: Joins elements of an array into a string using a specified separator.
 *
 * @param {string} separator - The separator string used between array elements.
 * @param {Array} data - The array whose elements will be joined.
 * @returns {string} - The joined string.
 * 
 * @example
 * const result = implode(', ', ['apple', 'banana', 'orange']); // result is "apple, banana, orange"
 */
const implode = (separator = ',', data) => {
	try {
		if (data !== null && !Array.isArray(data)) {
			throw new Error(`An error occurred in implode(): data should be an array`);
		}

		return data.join(separator);
	} catch (error) {
		console.error(`An error occurred in implode(): ${error.message}`);
		return '';
	}
}

/**
 * Function: explode
 * Description: Splits a string into an array of substrings based on a specified delimiter.
 *
 * @param {string} delimiter - The delimiter to use for splitting the string.
 * @param {string} data - The string to be split.
 * @returns {Array} - An array of substrings.
 * 
 * @example
 * const result = explode(' ', 'Hello world'); // result is ["Hello", "world"]
 */
const explode = (delimiter = ',', data) => {
	try {
		if (typeof data !== 'string') {
			throw new Error("An error occurred in explode(): data should be a string");
		}

		return data.split(delimiter);
	} catch (error) {
		console.error(`An error occurred in explode(): ${error.message}`);
		return [];
	}
}

/**
 * Function: remove_item_array
 * Description: Removes a specified item from an array if it exists.
 *
 * @param {Array} data - The array from which the item will be removed.
 * @param {*} item - The item to be removed from the array.
 * @returns {*} - The removed item, or undefined if the item doesn't exist in the array.
 * 
 * @example
 * const myArray = [1, 2, 3, 4];
 * const removedItem = remove_item_array(myArray, 2); // myArray is now [1, 3, 4], removedItem is 2
 */
const remove_item_array = (data, item) => {
	if (!Array.isArray(data)) {
		throw new Error("An error occurred in remove_item_array(): data should be an array");
	}

	const index = data.indexOf(item);
	if (index > -1) {
		try {
			return data.splice(index, 1)[0];
		} catch (error) {
			throw new Error(`An error occurred in remove_item_array(): ${error.message}`);
		}
	}

	return undefined;
};

// CURRENCY HELPER

/**
 * Function: formatCurrencyformatCurrency
 * Description: This function formats a numerical value as currency, based on the provided country code and options.
 *
 * @param {number} value - The numerical value to format as currency.
 * @param {string|null} code - The country code for the currency (e.g., "USD" for US Dollar). If null, the default locale is used.
 * @param {boolean} includeSymbol - A boolean indicating whether to include the currency symbol in the formatted output.
 *
 * @returns {string} - The formatted currency value as a string.
 */
const formatCurrency = (value, code = null, includeSymbol = false) => {
	// Check if the "Intl" object is available in the browser
	if (typeof Intl === 'undefined' || typeof Intl.NumberFormat === 'undefined') {
		return 'Error: The "Intl" object is not available in this browser, which is required for number formatting.';
	}

	if (!localeMapCurrency.hasOwnProperty(code)) {
		return 'Error: Invalid country code.';
	}

	// Validate the includeSymbol parameter
	if (typeof includeSymbol !== 'boolean') {
		return 'Error: includeSymbol parameter must be a boolean value.';
	}

	const currencyData = localeMapCurrency[code];

	const formatter = new Intl.NumberFormat(currencyData.code, {
		style: 'decimal',
		useGrouping: true,
		minimumFractionDigits: currencyData.decimal,
		maximumFractionDigits: currencyData.decimal,
	});

	if (includeSymbol) {
		const symbolFormatter = new Intl.NumberFormat(currencyData.code, {
			style: 'currency',
			currency: code,
			minimumFractionDigits: currencyData.decimal,
			maximumFractionDigits: currencyData.decimal,
		});
		return symbolFormatter.format(value);
	}

	return formatter.format(value);
};

/**
 * Function: currencySymbol
 * Description: Retrieves the currency symbol associated with a given currency code.
 * 
 * @param {string|null} currencyCode - The currency code for which to retrieve the symbol.
 *                                    If not provided or invalid, an error message is returned.
 * @returns {string} The currency symbol corresponding to the provided currency code,
 *                   or an error message if the code is invalid.
 */
const currencySymbol = (currencyCode = null) => {
	if (!localeMapCurrency.hasOwnProperty(currencyCode)) {
		return 'Error: Invalid country code.';
	}

	return localeMapCurrency[currencyCode]['symbol'];
};

// DATE & TIME HELPER

/**
 * Function: getCurrentTime
 * Description: Gets the current time in the specified format.
 *
 * @param {boolean} use12HourFormat - Optional. If true, the time will be in 12-hour format (AM/PM).
 *                                    If false or not provided, the time will be in 24-hour format.
 * @param {boolean} hideSeconds - Optional. If true, the seconds portion will be hidden.
 * @returns {string} The current time in the specified format.
 *
 * @example
 * const result24 = getCurrentTime();                    // result is like "14:30:45"
 * const result12 = getCurrentTime(true);                // result is like "02:30:45 PM"
 * const result12NoSeconds = getCurrentTime(true, true); // result is like "02:30 PM"
 */
const getCurrentTime = (use12HourFormat = false, hideSeconds = false) => {
	try {
		const today = new Date();
		let hh = today.getHours();
		const mm = today.getMinutes().toString().padStart(2, '0');
		let ss = '';

		if (!hideSeconds) {
			ss = `:${today.getSeconds().toString().padStart(2, '0')}`;
		}

		let timeFormat = "24-hour";

		if (use12HourFormat) {
			timeFormat = "12-hour";
			const period = hh >= 12 ? "PM" : "AM";
			hh = hh % 12 || 12; // Convert 0 to 12 for 12-hour format
			return `${hh}:${mm}${ss} ${period}`;
		}

		hh = hh.toString().padStart(2, '0');
		return `${hh}:${mm}${ss}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentTime(): ${error.message}`);
		return "00:00:00";
	}
};

/**
 * Function: getCurrentDate
 * Description: Gets the current date in YYYY-MM-DD format or a specified format.
 *
 * @param {string} format - The format date to return. Default is null.
 * @param {string} lang - The language code, either 'en' (English), 'my' (Malay), or 'id' (Indonesian). Default is 'en'.
 * @returns {string} - The current date.
 * 
 * @example
 * const result = getCurrentDate(); // result is like "2023-08-17"
 */
const getCurrentDate = (format = null, lang = 'en') => {
	try {
		const today = new Date();
		const dd = today.getDate().toString().padStart(2, '0');
		const mm = (today.getMonth() + 1).toString().padStart(2, '0'); // January is 0 so need to add 1
		const yyyy = today.getFullYear();
		return hasData(format) ? formatDate(`${yyyy}-${mm}-${dd}`, format, "1970-01-01", lang) : `${yyyy}-${mm}-${dd}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentDate(): ${error.message}`);
		return "1970-01-01";
	}
}

/**
 * Function: getCurrentTimestamp
 * Description: Gets the current timestamp in the format "YYYY-MM-DD HH:MM:SS".
 * @returns {string} The current timestamp in the format "YYYY-MM-DD HH:MM:SS".
 *
 * @example
 * const timestamp = getCurrentTimestamp(); // Returns something like "2023-08-17 14:30:45"
 */
const getCurrentTimestamp = () => {
	try {
		const now = new Date();
		const yyyy = now.getFullYear();
		const mm = (now.getMonth() + 1).toString().padStart(2, '0'); // January is 0 so need to add 1
		const dd = now.getDate().toString().padStart(2, '0');
		const hh = now.getHours().toString().padStart(2, '0');
		const min = now.getMinutes().toString().padStart(2, '0');
		const ss = now.getSeconds().toString().padStart(2, '0');

		return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentTimestamp(): ${error.message}`);
		return "1970-01-01 00:00:00"; // Return default value in case of error
	}
};

/**
 * Function: getClock
 * Description: Returns a formatted current time along with the day name and date in the specified language.
 *
 * @param {string} format - The time format, either '12' (12-hour) or '24' (24-hour). Default is '24'.
 * @param {string} lang - The language code, either 'en' (English), 'my' (Malay), or 'id' (Indonesian). Default is 'en'.
 * @param {boolean} showSeconds - Whether to include seconds in the formatted time string. Default is true.
 * @returns {string} - The formatted time string.
 * 
 * @example
 * // const time = getClock('24', 'en', true); // Returns a 24-hour time string with seconds in English.
 */
const getClock = (format = '24', lang = 'en', showSeconds = true) => {
	try {
		// Define day names in English, Malay, and Indonesian
		const dayNames = {
			en: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			my: ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'],
			id: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
		};

		// Validate the format parameter
		if (format !== '12' && format !== '24') {
			throw new Error("An error occurred in getClock(): Invalid format parameter. Use '12' or '24'.");
		}

		// Validate the lang parameter
		if (!dayNames[lang]) {
			throw new Error("An error occurred in getClock(): Invalid lang parameter. Use 'en', 'my', or 'id'.");
		}

		// Get the current date and time
		const currentTime = new Date();
		const currentDayIndex = currentTime.getDay(); // Get the day index (0-6)

		// Get the appropriate day name based on the current day index and language
		const dayName = dayNames[lang][currentDayIndex];

		// Get hours, minutes, and seconds
		let hours = currentTime.getHours();
		const minutes = currentTime.getMinutes();
		const seconds = currentTime.getSeconds();

		// Convert to 12-hour format and determine AM/PM if format is '12'
		let ampm = '';
		if (format === '12') {
			ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12 || 12; // Convert 0 to 12
		}

		// Add leading zeros to hours, minutes, and seconds if necessary
		hours = hours < 10 ? '0' + hours : hours;
		const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
		const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;

		// Build the time string
		let createTime = `${hours}:${formattedMinutes}`;

		if (showSeconds) {
			createTime += `:${formattedSeconds}`;
		}

		// Build the formatted time string
		let displayTime = format === '24'
			? createTime
			: `${createTime} ${ampm}`;

		return `${dayName}, ${displayTime}`;
	} catch (error) {
		console.error(`An error occurred in getClock(): ${error.message}`);
		return ''; // Return an empty string in case of an error
	}
};

/**
 * Function: isWeekend
 * Description: Checks if the given date falls on a weekend based on the specified weekend days.
 *
 * @param {Date|string} date - The date to check. Defaults to the current date if not provided.
 * @param {string[]} weekendDays - An optional array specifying weekend days ('SUN', 'MON', ..., 'SAT').
 * @returns {boolean} - Returns true if the date is a weekend, otherwise false.
 * 
 * @example
 * const result = isWeekend(new Date(2023, 8, 17)); // result is false
 * const result2 = isWeekend('2023-08-17'); // result is false
 * const customWeekendResult = isWeekend(new Date(2023, 8, 17), ['FRI', 'SAT']); // result is true, as Friday is considered a weekend day
 * const customWeekendResult2 = isWeekend('2023-08-17', ['FRI', 'SAT']); // result is true, as Friday is considered a weekend day
 */
const isWeekend = (date = new Date(), weekendDays = ['SUN', 'SAT']) => {
	try {
		const dateData = typeof date === 'string' ? new Date(date) : date;

		if (!(dateData instanceof Date) || isNaN(dateData)) {
			throw new Error("Invalid date input");
		}

		if (!Array.isArray(weekendDays) || weekendDays.some(day => typeof day !== 'string')) {
			throw new Error("Invalid weekendDays input");
		}

		const dayAbbreviation = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
		const day = dayAbbreviation[dateData.getDay()].toUpperCase();

		return weekendDays.map(d => d.toUpperCase()).includes(day);
	} catch (error) {
		console.error(`An error occurred in isWeekend(): ${error.message}`);
		return false;
	}
};

/**
 * Function: isWeekday
 * Description: Checks if the given date is a weekday (Monday to Friday).
 *
 * @param {Date} date - The date to be checked. Default is the current date.
 * @param {string[]} weekendDays - An optional array specifying weekend days ('SUN', 'MON', ..., 'SAT').
 * @returns {boolean} True if the date is a weekday, otherwise false.
 *
 * @example
 * const result = isWeekday(new Date('2023-08-19')); // Returns true if '2023-08-19' is a weekday.
 * const result2 = isWeekday('2023-08-19'); // Returns true if '2023-08-19' is a weekday.
 * const customWeekendResult = isWeekday('2023-08-19', ['FRI', 'SAT']); // Returns false if '2023-08-19' is a Friday.
 */
const isWeekday = (date = new Date(), weekendDays = ['SUN', 'SAT']) => {
	try {
		const dateData = typeof date === 'string' ? new Date(date) : date;

		if (!(dateData instanceof Date) || isNaN(dateData)) {
			throw new Error("Invalid date input");
		}

		if (!Array.isArray(weekendDays) || weekendDays.some(day => typeof day !== 'string')) {
			throw new Error("Invalid weekendDays input");
		}

		const dayAbbreviation = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
		const day = dayAbbreviation[dateData.getDay()].toUpperCase();

		return !weekendDays.map(d => d.toUpperCase()).includes(day);
	} catch (error) {
		console.error(`An error occurred in isWeekday(): ${error.message}`);
		return false;
	}
};

/**
 * Function: date
 * Description: Returns the current date and time formatted according to the specified format.
 *
 * @param {string} - formatted (optional) The format string used to format the date and time. If not provided, the function will use the default format.
 * @param {string | number | Date} [timestamp=null] - The timestamp to format. Defaults to the current date and time.
 * 
 * @return {string} Returns a formatted date string.
 * 
 * @example
 * const date1 = date('Y-m-d H:i:s'); // Outputs something like "2024-02-01 15:30:00"
 * const date2 = date('l, F j, Y');   // Outputs something like "Wednesday, February 1, 2024"
 * 
 * @throws {Error} Throws an error if there is an issue during date formatting.
 */
const date = (formatted = null, timestamp = null) => {
	try {
		const format = formatted === null ? 'Y-m-d' : formatted;

		// Convert the timestamp to a Date object if it is provided
		const currentDate = timestamp === null ? new Date() : (timestamp instanceof Date ? timestamp : new Date(timestamp));

		// Get various date components
		const year = currentDate.getFullYear().toString();
		const month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
		const day = currentDate.getDate().toString().padStart(2, '0');
		const hours24 = currentDate.getHours().toString().padStart(2, '0');
		const hours12 = ((hours24 % 12) || 12).toString().padStart(2, '0');
		const minutes = currentDate.getMinutes().toString().padStart(2, '0');
		const seconds = currentDate.getSeconds().toString().padStart(2, '0');
		const ampm = hours24 >= 12 ? 'PM' : 'AM';

		// Define arrays for days of the week and months
		const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

		// Replace placeholders in the format string
		return format.replace(/[a-zA-Z]/g, (match) => {
			switch (match) {
				case 'd': return day; // Day of the month, two digits with leading zeros (01 to 31)
				case 'D': return daysOfWeek[currentDate.getDay()].slice(0, 3); // A textual representation of a day, three letters (Mon through Sun)
				case 'j': return currentDate.getDate().toString(); // Day of the month without leading zeros (1 to 31)
				case 'l': return daysOfWeek[currentDate.getDay()]; // A full textual representation of the day of the week (Sunday through Saturday)
				case 'F': return months[currentDate.getMonth()]; // A full textual representation of a month (January through December)
				case 'm': return month; // Numeric representation of a month, with leading zeros (01 to 12)
				case 'M': return months[currentDate.getMonth()].slice(0, 3); // A short textual representation of a month, three letters (Jan through Dec)
				case 'n': return (currentDate.getMonth() + 1).toString(); // Numeric representation of a month, without leading zeros (1 to 12)
				case 'Y': return year; //  A four-digit representation of a year (e.g., 2024)
				case 'y': return year.slice(-2); // A two-digit representation of a year (e.g., 24)
				case 'H': return hours24; // 24-hour format of an hour with leading zeros (00 to 23)
				case 'h': return hours12; // 12-hour format of an hour with leading zeros (01 to 12)
				case 'i': return minutes; // Minutes with leading zeros (00 to 59)
				case 's': return seconds; // Seconds with leading zeros (00 to 59)
				case 'a': return ampm.toLowerCase(); // Lowercase Ante meridiem and Post meridiem (am or pm)
				case 'A': return ampm; // Uppercase Ante meridiem and Post meridiem (AM or PM)
				default: return match;
			}
		});

	} catch (error) {
		console.error(`An error occurred in date() while formatting date: ${error.message}`);
		return ''; // Return an empty string in case of an error
	}
};

/**
 * Function:  formatDate
 * Description: Formats a given date string according to the specified format.
 * 
 * @param {string} dateToFormat - The date string to be formatted.
 * @param {string} format - The desired format for the output date string. Defaults to 'd.m.Y'.
 * @param {*} defaultValue - The value to return if the input date is empty. Defaults to '1970-01-01'.
 * @param {string} lang - The language code, either 'en' (English), 'my' (Malay), or 'id' (Indonesian). Default is 'en'.
 * @returns {string|null} - The formatted date string or the defaultValue if the input date is empty.
 */
const formatDate = (dateToFormat, format = 'd.m.Y', defaultValue = '1970-01-01', lang = 'en') => {
	// Check if the date is empty
	if (!dateToFormat) {
		return defaultValue;
	}

	// Arrays to map format indicators to their corresponding values
	const _list = {
		// English
		en: {
			name: 'English',
			longMonth: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			shortMonth: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			longDay: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			shortDay: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
		},
		// Malay
		my: {
			name: 'Malay',
			longMonth: ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'],
			shortMonth: ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sep', 'Okt', 'Nov', 'Dis'],
			longDay: ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'],
			shortDay: ['Ahad', 'Isnin', 'Sel', 'Rabu', 'Khamis', 'Jum', 'Sab']
		},
		// Indonesian
		id: {
			name: 'Indonesian',
			longMonth: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
			shortMonth: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
			longDay: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
			shortDay: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
		},
		// Thai
		th: {
			name: 'Thai',
			longMonth: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
			shortMonth: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			longDay: ['วันอาทิตย์', 'วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์'],
			shortDay: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส']
		},
	};

	// Extract date components
	const d = new Date(dateToFormat);
	const day = d.getDate();
	const month = d.getMonth() + 1;
	const year = d.getFullYear();
	const dayOfWeek = d.getDay();

	// Mapping of format indicators to their values
	const replaceFormats = {
		d: day.toString().padStart(2, '0'),
		D: _list[lang]['shortDay'][dayOfWeek],
		j: day.toString(),
		l: _list[lang]['longDay'][dayOfWeek],
		F: _list[lang]['longMonth'][month - 1],
		m: month.toString().padStart(2, '0'),
		M: _list[lang]['shortMonth'][month - 1],
		n: month.toString(),
		Y: year.toString(),
		y: year.toString().slice(-2)
	};

	// Replace format indicators with their corresponding values
	const formattedDate = format.replace(/(d|D|j|l|F|m|M|n|Y|y)/g, match => replaceFormats[match]);

	return formattedDate;
};

/**
 * Function: dateDiff
 * Description: Calculate the number of days between two date strings or date objects, excluding specified dates or days.
 *
 * @param {Date|string} date1 - The first date (as a Date object or date string).
 * @param {Date|string} date2 - The second date (as a Date object or date string).
 * @param {Object} options - An object for customizing specific operations.
 * @returns {Object} Return the object with keys count, max_date, min_date, and exception date.
 *
 * @example
 * const result = dateDiff('2022-01-10', '2023-04-21'); // Return the result WITHOUT counting from the start date.
 * const result2 = dateDiff('2022-01-10', '2023-04-21', { 'startDate': true }); // Return the result counting from the start date.
 * const result3 = dateDiff('2022-01-10', '2023-04-21', { 'exception': ['FRI', 'SAT'] }); // Returns the number of days between the two dates excluding all Fridays and Saturdays.
 * const result4 = dateDiff('2022-01-10', '2023-04-21', { 'exception': ['2022-11-10', '2022-11-23', 'FRI'] }); // Returns the number of days between the two dates excluding specific dates and all Fridays.
 * 
 */
const dateDiff = (date1, date2, option = {}) => {
	try {
		// Convert date strings to Date objects
		const date1Obj = typeof date1 === 'string' ? new Date(date1) : date1;
		const date2Obj = typeof date2 === 'string' ? new Date(date2) : date2;

		// Check if both parameters are valid dates
		if (!(date1Obj instanceof Date) || isNaN(date1Obj) || !(date2Obj instanceof Date) || isNaN(date2Obj)) {
			throw new Error("Invalid date input");
		}

		// Check if the dates are the same
		if (date1Obj.getTime() === date2Obj.getTime()) {
			// If the dates are the same, checking whether the start date needs to be included will return 1; otherwise, it will return 0 for no days of difference.
			return {
				'count': hasData(option, 'startDate', true) === true ? 1 : 0,
				'min_date': formatDate(date1Obj, 'Y-m-d'),
				'max_date': formatDate(date1Obj, 'Y-m-d'),
				'list_date': getAllDatesBetween(formatDate(date1Obj, 'Y-m-d'), formatDate(date1Obj, 'Y-m-d'), true),
				'exception': {}
			};
		}

		// Determine the maximum and minimum dates
		const maxDate = date1Obj > date2Obj ? date1Obj : date2Obj;
		const minDate = date1Obj > date2Obj ? date2Obj : date1Obj;

		// Calculate the difference in days
		const timeDifference = maxDate.getTime() - minDate.getTime();
		let daysDifference = Math.floor(timeDifference / (1000 * 3600 * 24));

		let exceptionDate = [];

		// Remove specified dates or days
		if (hasData(option, 'exception')) {
			const exception = option.exception;
			exception.forEach(excludeItem => {
				if (excludeItem instanceof Date || !isNaN(new Date(excludeItem))) {
					// Exclude specific dates
					const excludeDate = new Date(excludeItem);
					if (excludeDate >= minDate && excludeDate <= maxDate) {
						exceptionDate.push(formatDate(excludeDate, 'Y-m-d'));
						daysDifference--;
					}
				} else if (typeof excludeItem === 'string') {
					const excludedDays = getDatesByDay(minDate, maxDate, excludeItem.toUpperCase().substring(0, 3));
					exceptionDate = exceptionDate.concat(excludedDays.map(date => formatDate(date, 'Y-m-d')));
					daysDifference -= excludedDays.length;
				}
			});
		}

		// Get all dates between minDate and maxDate
		let listDates = getAllDatesBetween(minDate, maxDate, hasData(option, 'startDate', true, false));
		let dateFilter = listDates.filter(date => !exceptionDate.includes(date));

		return {
			'count': dateFilter.length,
			'min_date': formatDate(minDate, 'Y-m-d'),
			'max_date': formatDate(maxDate, 'Y-m-d'),
			'list_date': dateFilter,
			'exception': exceptionDate
		};
	} catch (error) {
		console.error(`An error occurred in dateDiff(): ${error.message}`);
		return false;
	}
};

/**
 * Function: getAllDatesBetween
 * Description: Generates an array of dates between the specified start and end dates.
 * 
 * @param {Date|string} startDate - The start date (as a Date object or a string in YYYY-MM-DD format).
 * @param {Date|string} endDate - The end date (as a Date object or a string in YYYY-MM-DD format).
 * @param {boolean} includeStart - Whether to include the start date in the array (default: false).
 * @returns {string[]} An array of date strings between the start and end dates.
 */
const getAllDatesBetween = (startDate, endDate, includeStart = false) => {
	const dates = [];

	// Convert startDate and endDate to Date objects if they are provided as strings
	const startDateObj = typeof startDate === 'string' ? new Date(startDate) : startDate;
	const endDateObj = typeof endDate === 'string' ? new Date(endDate) : endDate;

	let currentDate = startDateObj;

	// Include start date if specified
	if (includeStart) {
		dates.push(currentDate.toISOString().split('T')[0]); // Add start date string to array
	}

	// Iterate through dates until reaching the end date
	while (currentDate < endDateObj) {
		currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
		dates.push(currentDate.toISOString().split('T')[0]); // Add date string to array
	}

	return dates;
};

/**
 * Function: getDatesByDay
 * Description: Get dates within a specific date range that match the specified day of the week.
 *
 * @param {Date|string} startDate - The start date (as a Date object or date string).
 * @param {Date|string} endDate - The end date (as a Date object or date string).
 * @param {string} dayOfWeek - The day of the week to match (e.g., 'MON', 'TUE').
 * @returns {Array} Array of dates (in 'Y-m-d' format) matching the specified day of the week within the date range.
 *
 * @example
 * const result = getDatesByDay('2024-01-01', '2024-01-31', 'TUE');
 * // Returns an array of all Tuesdays between January 1, 2024, and January 31, 2024.
 */
const getDatesByDay = (startDate, endDate, dayOfWeek) => {
	try {
		const result = [];

		// Convert date strings to Date objects
		const startDateObj = typeof startDate === 'string' ? new Date(startDate) : startDate;
		const endDateObj = typeof endDate === 'string' ? new Date(endDate) : endDate;

		// Check if both parameters are valid dates
		if (!(startDateObj instanceof Date) || isNaN(startDateObj) || !(endDateObj instanceof Date) || isNaN(endDateObj)) {
			throw new Error("Invalid date input");
		}

		// Determine the maximum and minimum dates
		const maxDate = startDateObj > endDateObj ? startDateObj : endDateObj;
		const minDate = startDateObj > endDateObj ? endDateObj : startDateObj;

		// Find the first occurrence of the specified day of the week within the date range
		let currentDate = new Date(minDate);
		while (currentDate <= maxDate) {
			if (currentDate.getDay() === getDayIndex(dayOfWeek)) {
				result.push(formatDate(currentDate, 'Y-m-d'));
			}
			currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
		}

		return result;
	} catch (error) {
		console.error(`An error occurred in getDatesByDay(): ${error.message}`);
		return false;
	}
};

/**
 * Function: getDayIndex
 * Description: Get the index of the specified day of the week (0 for Sunday, 1 for Monday, etc.).
 *
 * @param {string} dayOfWeek - The day of the week (case-insensitive, abbreviated to three letters).
 * @returns {number} The index of the specified day of the week.
 * 
 * @example
 * const index = getDayIndex('Mon'); // Returns 1
 * const index2 = getDayIndex('saturday'); // Returns 6
 */
const getDayIndex = (dayOfWeek) => {
	const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
	const upperCaseDay = dayOfWeek.toUpperCase().substring(0, 3);
	return days.indexOf(upperCaseDay);
};

// CUSTOM HELPER

/**
 * Function: random
 * Description: Generate a random integer between the specified minimum (inclusive) and maximum (exclusive) values.
 *
 * @param {number} min - The minimum value (inclusive).
 * @param {number} max - The maximum value (exclusive).
 * @returns {number} - A random integer between min (inclusive) and max (exclusive).
 * 
 * @example
 * // Generate a random number between 1 and 10.
 * const randomNumber = random(1, 11);
 */
const random = (min, max) => {
	return Math.floor(Math.random() * (max - min)) + min;
};

/**
 * Function: is_object
 * Description: Check if a value is an object (excluding null).
 *
 * @param {*} obj - The value to check.
 * @returns {boolean} - True if the value is an object (excluding null), otherwise false.
 * 
 * @example
 * // Check if a variable is an object (excluding null).
 * const objValue = { key: 'value' };
 * console.log(is_object(objValue)); // Output: true
 */
const is_object = (obj) => {
	return obj !== null && typeof obj === 'object';
}

/**
 * Function: is_array
 * Description: Check if a value is an array.
 *
 * @param {*} val - The value to check.
 * @returns {boolean} - True if the value is an array, otherwise false.
 * 
 * @example
 * // Check if a variable is an array.
 * const arr = [1, 2, 3];
 * console.log(is_array(arr)); // Output: true
 */
const is_array = (val) => {
	return Array.isArray(val);
}

/**
 * Function: isMobileJs
 * Description: Check if the current device is a mobile device based on the user agent string.
 *
 * @returns {boolean} - True if the device is a mobile device, otherwise false.
 * 
 * @example
 * // Check if the current device is a mobile device.
 * console.log(isMobileJs()); // Output: true (if accessed from a mobile device)
 */
const isMobileJs = () => {
	const toMatch = [
		/Android/i,
		/webOS/i,
		/iPhone/i,
		/iPad/i,
		/iPod/i,
		/BlackBerry/i,
		/Windows Phone/i
	];

	return toMatch.some((toMatchItem) => {
		return navigator.userAgent.match(toMatchItem);
	});
}

/**
 * Function: disableBtn
 * Description: Enable or disable a button element.
 *
 * @param {string} id - The ID of the button element.
 * @param {boolean} [display=true] - Flag indicating whether to disable (true) or enable (false) the button.
 * @returns {void}
 * 
 * @example
 * // Disable a button with ID 'myButton'.
 * disableBtn("myButton");
 */
const disableBtn = (id, display = true) => {
	document.getElementById(id).disabled = display;
}

/**
 * Function: loadingBtn
 * Description: Toggle loading state of a button by showing a spinner and disabling it or restoring its original text and enabling it.
 *
 * @param {string} id - The ID of the button element.
 * @param {boolean} [display=false] - Flag indicating whether to display the loading state or not.
 * @param {string | null} [text=null] - The text/html content to be displayed on the button when not in loading state. If null, button's current text will be used.
 * @returns {void}
 * 
 * @example
 * // Display loading state
 * loadingBtn("myButtonId", true);
 * 
 * // Restore original state
 * loadingBtn("myButtonId", false);
 */
const loadingBtn = (id, display = false, text = null) => {
	const button = document.getElementById(id);
	const buttonText = text !== null ? text : button.textContent;
	if (display) {
		button.innerHTML = 'Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>';
		button.disabled = true;
	} else {
		button.innerHTML = buttonText;
		button.disabled = false;
	}
}

/**
 * Function: printDiv
 * Description: Print the content of a specified div using the printThis.js library.
 *
 * @param {string} idToPrint - The ID of the div element to print.
 * @param {string} [printBtnID='printBtn'] - The ID of the button element used for triggering printing.
 * @param {string} [printBtnText="<i class='ti ti-device-floppy ti-xs mb-1'></i> Save"] - The text/html content to be displayed on the print button when not in loading state.
 * @param {string} [pageTitlePrint='Print'] - The title to be used for the printed page.
 * @returns {void}
 * 
 * @example
 * // Print the content of a div with ID 'contentDiv'
 * printDiv("contentDiv");
 */
const printDiv = (idToPrint, printBtnID = 'printBtn', printBtnText = "<i class='ti ti-device-floppy ti-xs mb-1'></i> Save", pageTitlePrint = 'Print') => {
	$("#" + idToPrint).printThis({
		// header: $('#headerPrint').html(),
		// footer: $('#tablePrint').html(), 
		importCSS: false,
		pageTitle: pageTitlePrint,
		beforePrint: loadingBtn(printBtnID, true),
	});

	setTimeout(function () {
		loadingBtn(printBtnID, false, printBtnText);
		$('#' + idToPrint).empty(); // reset
	}, 800);
}

/**
 * Function: loading
 * Description: Show or hide a loading spinner overlay.
 *
 * @param {string} [id=null] - The ID of the element to display the loading spinner overlay on.
 * @param {boolean} [display=false] - Flag indicating whether to display (true) or hide (false) the loading spinner overlay.
 * @returns {void}
 * 
 * @example
 * // Display a loading spinner overlay.
 * loading("#loadingOverlay", true);
 */
const loading = (id = null, display = false) => {
	if (display) {
		$(id).block({
			message: '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
			css: {
				backgroundColor: 'transparent',
				color: '#fff',
				border: '0'
			},
			overlayCSS: {
				opacity: 0.15
			}
		});
	} else {
		setTimeout(function () {
			$(id).unblock();
		}, 80);
	}
}

// URL & ASSET HELPER

/**
 * Function: base_url
 * Description: Get the base URL of the current website.
 *
 * @returns {string} - The base URL.
 * 
 * @example
 * // Get the base URL.
 * const baseUrl = base_url();
 */
const base_url = () => {
	return document.querySelector('meta[name="base_url"]').getAttribute('content');
}

/**
 * Function: urls
 * Description: Convert a relative path to an absolute URL using the base URL.
 *
 * @param {string} path - The relative path.
 * @returns {string} - The absolute URL.
 * 
 * @example
 * // Convert a relative path to an absolute URL.
 * const absoluteUrl = urls("/path");
 */
const urls = (path) => {
	const basePath = base_url();
	const newPath = new URL(path, basePath);
	return newPath.href;
}

/**
 * Function: redirect
 * Description: Redirect to a specified URL.
 *
 * @param {string} url - The URL to redirect to.
 * @returns {void}
 * 
 * @example
 * // Redirect to a specific URL.
 * redirect("/new-page");
 */
const redirect = (url) => {
	const pathUrl = base_url() + url;
	window.location.replace(pathUrl);
}

/**
 * Function: refreshPage
 * Description: Refresh the current page.
 *
 * @returns {void}
 * 
 * @example
 * // Refresh the current page.
 * refreshPage();
 */
const refreshPage = () => {
	location.reload();
}

/**
 * Function: asset
 * Description: Get the absolute URL of an asset file.
 *
 * @param {string} path - The path to the asset file.
 * @param {boolean} [isPublic=true] - Flag indicating whether the asset is in the public folder.
 * @returns {string} - The absolute URL of the asset file.
 * 
 * @example
 * // Get the absolute URL of an asset file.
 * const assetUrl = asset("css/style.css");
 */
const asset = (path, isPublic = true) => {
	const publicFolder = isPublic ? 'public/' : '';
	return urls(publicFolder + path);
}

// UPLOAD HELPER

/**
 * Function: sizeToText
 * Description: Convert file size in bytes to a human-readable format.
 *
 * @param {number} size - File size in bytes.
 * @returns {string} - Human-readable file size.
 * 
 * @example
 * // Convert file size in bytes to human-readable format.
 * const fileSize = 1024; // 1KB
 * console.log(sizeToText(fileSize)); // Output: "1 KB"
 */
const sizeToText = (size) => {
	const sizeContext = ["B", "KB", "MB", "GB", "TB"];
	let atCont = 0;
	while (size / 1024 > 1) {
		size /= 1024;
		++atCont;
	}
	return Math.round(size * 100) / 100 + ' ' + sizeContext[atCont];
}

// INPUT (NUMBER) HELPER

/**
 * Function: isNumberKey
 * Description: Check if the pressed key is a number key.
 *
 * @param {Event} evt - The keypress event.
 * @returns {boolean} - True if the pressed key is a number key, otherwise false.
 * 
 * @example
 * // Check if the pressed key is a number key.
 * document.addEventListener('keypress', function(evt) {
 *     if (isNumberKey(evt)) {
 *         console.log('Key is a number key.');
 *     } else {
 *         console.log('Key is not a number key.');
 *     }
 * });
 */
const isNumberKey = (evt) => {
	const charCode = (evt.which) ? evt.which : evt.keyCode;
	return charCode >= 48 && charCode <= 57;
}

/**
 * Function: maxLengthCheck
 * Description: Limit the maximum length of the input value.
 *
 * @param {HTMLInputElement} object - The input element.
 * @returns {void}
 * 
 * @example
 * // Limit the maximum length of an input element to 10 characters.
 * <input type="text" oninput="maxLengthCheck(this)" maxlength="10">
 */
const maxLengthCheck = (object) => {
	if (object.value.length > object.maxLength)
		object.value = object.value.slice(0, object.maxLength);
}

/**
 * Function: isNumeric
 * Description: Check if the key pressed is a numeric key.
 *
 * @param {KeyboardEvent} evt - The keydown event.
 * @returns {void}
 * 
 * @example
 * // Allow only numeric input.
 * <input type="text" onkeypress="isNumeric(event)">
 */
const isNumeric = (evt) => {
	const key = String.fromCharCode(evt.keyCode || evt.which);
	const regex = /[0-9]|\./;
	if (!regex.test(key)) {
		evt.preventDefault ? evt.preventDefault() : (evt.returnValue = false);
	}
}

/**
 * Function: isDigit
 * Description: Check if the given string contains only digits.
 *
 * @param {string} str - The input string.
 * @returns {boolean} - True if the string contains only digits, otherwise false.
 * 
 * @example
 * // Check if a string contains only digits.
 * console.log(isDigit("123")); // Output: true
 * console.log(isDigit("abc")); // Output: false
 */
const isDigit = (str) => {
	const regex = /^[0-9]*$/;
	return regex.test(str);
}

// MODAL (BOOTSTRAP) HELPER

/**
 * Function: showModal
 * Description: Show a modal by its ID after a specified delay.
 *
 * @param {string} id - The ID of the modal element.
 * @param {number} [timeSet=0] - The delay in milliseconds before showing the modal. Defaults to 0.
 * @returns {void}
 * 
 * @example
 * // Show a modal with ID 'myModal' after 500 milliseconds.
 * showModal('#myModal', 500);
 */
const showModal = (id, timeSet = 0) => {
	setTimeout(() => {
		$(id).modal('show');
	}, timeSet);
}

/**
 * Function: closeModal
 * Description: Hide a modal by its ID after a specified delay.
 *
 * @param {string} id - The ID of the modal element.
 * @param {number} [timeSet=250] - The delay in milliseconds before hiding the modal. Defaults to 250.
 * @returns {void}
 * 
 * @example
 * // Hide a modal with ID 'myModal' after 300 milliseconds.
 * closeModal('#myModal', 300);
 */
const closeModal = (id, timeSet = 250) => {
	setTimeout(() => {
		$(id).modal('hide');
	}, timeSet);
}

/**
 * Function: closeOffcanvas
 * Description: Toggle the state of an offcanvas element by its ID after a specified delay.
 *
 * @param {string} id - The ID of the offcanvas element.
 * @param {number} [timeSet=250] - The delay in milliseconds before toggling the offcanvas. Defaults to 250.
 * @returns {void}
 * 
 * @example
 * // Toggle an offcanvas with ID 'myOffcanvas' after 400 milliseconds.
 * closeOffcanvas('#myOffcanvas', 400);
 */
const closeOffcanvas = (id, timeSet = 250) => {
	setTimeout(() => {
		$(id).offcanvas('toggle');
	}, timeSet);
}





// API CALLBACK HELPER 

const loginApi = async (url, formID = null) => {
	const submitBtnText = $('#loginBtn').html();

	var btnSubmitIDs = $('#' + formID + ' button[type=submit]').attr("id");
	var inputSubmitIDs = $('#' + formID + ' input[type=submit]').attr("id");
	var submitIdBtn = isDef(btnSubmitIDs) ? btnSubmitIDs : isDef(inputSubmitIDs) ? inputSubmitIDs : null;

	loadingBtn(submitIdBtn, true, submitBtnText);

	url = urls(`app/controllers/${url}`);
	try {
		var frm = $('#' + formID);
		const dataArr = new FormData(frm[0]);

		return axios({
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'content-type': 'application/x-www-form-urlencoded',
			},
			url: url,
			data: dataArr
		})
			.then(result => {
				loadingBtn(submitIdBtn, false, submitBtnText);
				return result;
			})
			.catch(error => {

				log('ERROR 1 LOGIN');
				let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

				if (isError(error.response.status)) {
					noti(error.response.status, textMessage);
				} else if (isUnauthorized(error.response.status)) {
					noti(error.response.status, "Unauthorized: Access is denied");
				}

				loadingBtn(submitIdBtn, false, submitBtnText);

				return error.response;

			});
	} catch (e) {
		const res = e.response;
		log(res, 'ERROR 2 LOGIN');

		loadingBtn(submitIdBtn, false, submitBtnText);

		if (isUnauthorized(res.status)) {
			noti(res.status, "Unauthorized: Access is denied");
		} else {
			if (isError(res.status)) {
				var error_count = 0;
				for (var error in res.data.errors) {
					if (error_count == 0) {
						noti(res.status, res.data.errors[error][0]);
					}
					error_count++;
				}
			} else {
				noti(res.status, 'Something went wrong');
			}
			return res;
		}
	}

	loadingBtn(submitIdBtn, false, submitBtnText);

}

const submitApi = async (url, dataObj, formID = null, reloadFunction = null, closedModal = true) => {
	const submitBtnText = $('#submitBtn').html();

	var btnSubmitIDs = $('#' + formID + ' button[type=submit]').attr("id");
	var inputSubmitIDs = $('#' + formID + ' input[type=submit]').attr("id");
	var submitIdBtn = isDef(btnSubmitIDs) ? btnSubmitIDs : isDef(inputSubmitIDs) ? inputSubmitIDs : null;

	loadingBtn(submitIdBtn, true, submitBtnText);

	if (dataObj != null) {
		url = urls(`app/controllers/${url}`);

		try {
			var frm = $(`#${formID}`);
			const dataArr = new FormData(frm[0]);

			// Check if the form has file inputs
			const hasFileInputs = frm.find('input[type=file]').length > 0;

			// Set headers dynamically based on whether the form has file inputs
			const headers = {
				'X-Requested-With': 'XMLHttpRequest',
				'content-Type': hasFileInputs ? 'multipart/form-data' : 'application/x-www-form-urlencoded',
			};

			return axios({
				method: 'POST',
				headers: headers,
				url: url,
				data: dataArr
			})
				.then(result => {

					if (isSuccess(result.status) && reloadFunction != null) {
						reloadFunction();
					}

					if (formID != null) {
						if (closedModal) {
							var modalID = $('#' + formID).attr('data-modal');
							setTimeout(function () {
								if (modalID == '#generaloffcanvas-right') {
									$(modalID).offcanvas('toggle');
								} else {
									// $('#' + modalID).modal('hide');
									$(modalID).modal('hide');
								}
							}, 350);
						}
					}

					loadingBtn(submitIdBtn, false, submitBtnText);
					return result;
				})
				.catch(error => {

					log('ERROR SubmitApi 1');
					let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

					if (isError(error.response.status)) {
						noti(error.response.status, textMessage);
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					} else {
						log(error, 'Response Submit Api 1');
					}

					loadingBtn(submitIdBtn, false, submitBtnText);

					return error.response;

				});
		} catch (e) {
			const res = e.response;
			log(res, 'ERROR 2 Submit');

			loadingBtn(submitIdBtn, false, submitBtnText);

			if (isUnauthorized(res.status)) {
				noti(res.status, "Unauthorized: Access is denied");
			} else {
				if (isError(res.status)) {
					var error_count = 0;
					for (var error in res.data.errors) {
						if (error_count == 0) {
							noti(res.status, res.data.errors[error][0]);
						}
						error_count++;
					}
				} else {
					noti(res.status, 'Something went wrong');
				}
				return res;
			}
		}
	} else {
		noti(400, "No data to insert!");
		loadingBtn(submitIdBtn, false, submitBtnText);

	}
}

const deleteApi = async (id, url, reloadFunction = null) => {
	if (id != '') {
		url = urls(`app/controllers/${url}/${id}`);
		try {
			return axios({
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'content-type': 'application/x-www-form-urlencoded',
				},
				url: url,
			})
				.then(result => {
					if (isSuccess(result.status) && reloadFunction != null) {
						reloadFunction();
					}
					noti(result.status, 'Remove');
					return result;
				})
				.catch(error => {

					log('ERROR DeleteApi 1');
					let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

					if (isError(error.response.status)) {
						noti(error.response.status, textMessage);
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					} else {
						log(error, 'Response Delete Api 1');
					}

					return error.response;

				});
		} catch (e) {
			const res = e.response;
			log(e, 'Response Delete Api 2');

			if (isUnauthorized(res.status)) {
				noti(res.status, "Unauthorized: Access is denied");
			} else {
				if (isError(res.status)) {
					var error_count = 0;
					for (var error in res.data.errors) {
						if (error_count == 0) {
							noti(res.status, res.data.errors[error][0]);
						}
						error_count++;
					}
				} else {
					noti(500, 'Something went wrong');
				}
				return res;
			}
		}
	} else {
		noti(400);
	}
}

const callApi = async (method = 'POST', url, dataObj = null, option = {}) => {
	url = urls(`app/controllers/${url}`);
	let dataSent = null;

	if (method == 'post' || method == 'put') {
		dataSent = new URLSearchParams(dataObj);
	}

	try {
		return axios({
			method: method,
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'content-type': 'application/x-www-form-urlencoded',
			},
			url: url,
			data: dataSent,
		},
			option
		).then(result => {
			return result;
		})
			.catch(error => {
				log('ERROR CallApi 1');
				let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

				if (isError(error.response.status)) {
					noti(error.response.status, textMessage);
				} else if (isUnauthorized(error.response.status)) {
					noti(error.response.status, "Unauthorized: Access is denied");
				} else {
					log(error, 'ERROR CallApi 1');
				}

				return error.response;
			});
	} catch (e) {
		log('ERROR CallApi 2');
		const res = e.response;
		if (isUnauthorized(res.status)) {
			noti(res.status, "Unauthorized: Access is denied");
		} else {
			if (isError(res.status)) {
				// var error_count = 0;
				// for (var error in res.data.errors) {
				// 	if (error_count == 0) {
				// 		noti(500, res.data.errors[error][0]);
				// 	}
				// 	error_count++;
				// }
				noti(res.response.status, res.response.data.message);
			} else {
				noti(500, 'Something went wrong');
			}
			return res;
		}
	}
}

const noti = (code = 400, text = 'Something went wrong') => {

	const apiStatus = {
		200: 'OK',
		201: 'Created', // POST/PUT resulted in a new resource, MUST include Location header
		202: 'Accepted', // request accepted for processing but not yet completed, might be disallowed later
		204: 'No Content', // DELETE/PUT fulfilled, MUST NOT include message-body
		301: 'Moved Permanently', // The URL of the requested resource has been changed permanently
		304: 'Not Modified', // If-Modified-Since, MUST include Date header
		400: 'Bad Request', // malformed syntax
		401: 'Unauthorized', // Indicates that the request requires user authentication information. The client MAY repeat the request with a suitable Authorization header field
		403: 'Forbidden', // unauthorized
		404: 'Not Found', // request URI does not exist
		405: 'Method Not Allowed', // HTTP method unavailable for URI, MUST include Allow header
		415: 'Unsupported Media Type', // unacceptable request payload format for resource and/or method
		426: 'Upgrade Required',
		429: 'Too Many Requests',
		451: 'Unavailable For Legal Reasons', // REDACTED
		500: 'Internal Server Error', // all other errors
		501: 'Not Implemented', // (currently) unsupported request method
		503: 'Service Unavailable' // The server is not ready to handle the request.
	};

	var resCode = typeof code === 'number' ? code : code.status;

	var messages = Array.isArray(text) ? text : [text]; // Convert to array if not already

	messages.forEach((message) => {
		var messageText = isSuccess(resCode) ? ucfirst(message) + ' successfully' : isUnauthorized(resCode) ? 'Unauthorized: Access is denied' : isError(resCode) ? message : 'Something went wrong';
		var type = isSuccess(code) ? 'success' : 'error';
		var title = isSuccess(code) ? 'Great!' : 'Ops!';

		toastr.options = {
			"debug": false,
			"closeButton": !isMobileJs(),
			"newestOnTop": true,
			"progressBar": !isMobileJs(),
			"positionClass": !isMobileJs() ? "toast-top-right" : "toast-bottom-full-width",
			"preventDuplicates": isMobileJs(),
			"onclick": null,
			"showDuration": "500",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}

		Command: toastr[type](messageText, title)
	});
}

const isSuccess = (res) => {
	const successStatus = [200, 201, 302];
	const status = typeof res === 'number' ? res : res.status;
	return successStatus.includes(status);
}

const isError = (res) => {
	const errorStatus = [400, 404, 422, 429, 500];
	const status = typeof res === 'number' ? res : res.status;
	return errorStatus.includes(status);
}

const isUnauthorized = (res) => {
	const unauthorizedStatus = [401, 403];
	const status = typeof res === 'number' ? res : res.status;
	return unauthorizedStatus.includes(status);
}

//  BASE64-ENCODING HELPER

const getImageSizeBase64 = (base64, type = 'b') => {

	var decodedData = atob(base64.split(',')[1]);
	var dataSizeInBytes = decodedData.length;
	var dataSizeInKB = (dataSizeInBytes / 1024).toFixed(2);
	var dataSizeInMB = (dataSizeInKB / 1024).toFixed(2);

	if (type == 'b' || type == 'B')
		return dataSizeInBytes;
	else if (type == 'kb' || type == 'KB')
		return dataSizeInKB;
	else if (type == 'mb' || type == 'MB')
		return dataSizeInMB;
}

// PROJECT BASED HELPER

const noSelectDataLeft = (text = 'Type', filesName = '5.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/general/images/nodata/' + filesName;

	return "<div id='nodataSelect' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='38%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                	<strong> NO " + text.toUpperCase() + " SELECTED </strong>\
                </h3>\
				<h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;'> \
					Select any " + text + " on the left\
				</h6>\
			</center>\
            </div>";
}

const nodata = (text = true, filesName = '4.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/general/images/nodata/' + filesName;
	var showText = (text) ? '' : 'style="display:none"';
	var suggestion = (text) ? '' : '"display:none!important"';

	return "<div id='nodata' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='38%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                <strong> NO INFORMATION FOUND </strong>\
                </h3>\
                <h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;" + suggestion + "'> \
                    Here are some action suggestions for you to try :- \
                </h6>\
            </center>\
            <div class='row d-flex justify-content-center w-100' " + showText + ">\
                <div class='col-lg m-1 text-left' style='max-width: 350px !important;letter-spacing :1px; font-family: Quicksand, sans-serif !important;font-size: 12px;'>\
                    1. Try the registrar function (if any).<br>\
                    2. Change your word or search selection.<br>\
                    3. Contact the system support immediately.<br>\
                </div>\
            </div>\
            </div>";
}

const nodataAccess = (filesName = '403.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/general/images/nodata/' + filesName;
	return "<div id='nodataAccess' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='30%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                <strong> NO INFORMATION FOUND </strong>\
                </h3>\
            </center>\
            </div>";
}

const skeletonTableOnly = (totalData = 3) => {

	let body = '';
	for (let index = 0; index < totalData; index++) {
		body += '<tr>\
					<td width="5%" class="skeleton"> </td>\
					<td width="31%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="14%" class="skeleton"> </td>\
				</tr>';
	}

	return '<div class="col-xl-12 mt-2">\
				<button type="button" class="btn btn-default btn-sm skeleton">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </button>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton mb-3">\
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
				</button>\
				<table class="table">\
					<tbody>' + body + '</tbody>\
				</table>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>\
			</div>';
}

const skeletonTable = (hasFilter = null, buttonRefresh = true) => {

	let totalData = 3;
	let body = '';

	for (let index = 0; index < totalData; index++) {
		body += '<tr>\
					<td width="5%" class="skeleton"> </td>\
					<td width="31%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="14%" class="skeleton"> </td>\
				</tr>';
	}

	let filters = '';
	if (hasData(hasFilter)) {
		for (let index = 0; index < hasFilter; index++) {
			filters += '<select class="form-control form-control-sm float-end me-2 skeleton" style="width: 12%!important;"></select>';
		}
	}

	let buttonShow = buttonRefresh ? '<div class="col-xl-12 mb-4">\
										<button type="button" class="btn btn-default btn-sm float-end skeleton">  &nbsp;&nbsp;&nbsp; </button>\
										<button type="button" class="btn btn-default btn-sm float-end me-2 skeleton">\
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
										</button>\
										' + filters + '\
										</div><br><br><br>' : '';

	return buttonShow + '<div class="col-xl-12 mt-2">\
				<button type="button" class="btn btn-default btn-sm skeleton">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </button>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton mb-3">\
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
				</button>\
				<table class="table">\
					<tbody>' + body + '</tbody>\
				</table>\
			</div>';
}

const skeletonTableCard = (hasFilter = null, buttonRefresh = true) => {

	let totalData = random(5, 20);
	let body = '';

	for (let index = 0; index < totalData; index++) {
		body += '<tr>\
					<td width="5%" class="skeleton"> </td>\
					<td width="31%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="14%" class="skeleton"> </td>\
				</tr>';
	}

	let filters = '';
	if (hasData(hasFilter)) {
		for (let index = 0; index < hasFilter; index++) {
			filters += '<select class="form-control form-control-sm float-end me-2 skeleton" style="width: 12%!important;"></select>';
		}
	}

	let buttonShow = buttonRefresh ? '<div class="col-xl-12 mb-4">\
										<button type="button" class="btn btn-default btn-sm float-end skeleton">  &nbsp;&nbsp;&nbsp; </button>\
										<button type="button" class="btn btn-default btn-sm float-end me-2 skeleton">\
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
										</button>\
										' + filters + '\
										</div><br><br>' : '';

	return '<div class="row mt-2">\
				<div class="col-md-12 col-lg-12">\
					<div class="card" id="bodyDiv">\
						<div class="card-body">\
							' + buttonShow + '\
							<div class="col-xl-12 mt-2">\
								<table class="table table-bordered">\
									<tbody>' + body + '</tbody>\
								</table>\
							</div>\
						</div>\
					</div>\
				</div>\
			</div>';
}

const getImageDefault = (imageName, path = 'public/upload/default/') => {
	return urls(path + imageName);
}

const generateDatatableServer = (id, url = null, nodatadiv = 'nodatadiv', dataObj = null, options = [], screenLoadID = null) => {

	const tableID = $('#' + id);
	var table = tableID.DataTable().clear().destroy();

	$.ajaxSetup({
		data: {

		}
	});

	if (dataObj != null) {
		dataSent = dataObj;
	} else {
		dataSent = null;
	}

	if (screenLoadID != null) {
		loading('#' + screenLoadID, true);
	}

	if (dataSent == null) {
		return tableID.DataTable({
			// "pagingType": "full_numbers",
			"processing": true,
			"serverSide": true,
			"responsive": true,
			"iDisplayLength": 10,
			"bLengthChange": true,
			"searching": true,
			"autoWidth": false,
			"ajax": {
				type: 'POST',
				url: $('meta[name="base_url"]').attr('content') + url,
				dataType: "JSON",
				// data: dataSent,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'content-type': 'application/x-www-form-urlencoded',
				},
				"error": function (xhr, error, exception) {
					if (exception) {
						if (isError(xhr.status))
							noti(xhr.status, exception);
					}
				}
			},
			"language": {
				"searchPlaceholder": 'Search...',
				"sSearch": '',
				// "lengthMenu": '_MENU_ item / page',
				// "paginate": {
				// 	"first": "First",
				// 	"last": "The End",
				// 	"previous": "Previous",
				// 	"next": "Next"
				// },
				// "info": "Showing _START_ to _END_ of _TOTAL_ items",
				// "emptyTable": "No data is available in the table",
				// "info": "Showing _START_ to _END_ of _TOTAL_ items",
				// "infoEmpty": "Showing 0 to 0 of 0 items",
				// "infoFiltered": "(filtered from _MAX_ number of items)",
				// "zeroRecords": "No matching records",
				// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
				// "loadingRecords": "Loading...",
				// "infoPostFix": "",
				// "thousands": ",",
			},
			"columnDefs": hasData(options, 'column', true, []),
			"order": hasData(options, 'order', true, [[0, 'asc']]),
			initComplete: function () {

				var totalData = this.api().data().length;

				if (totalData > 0) {
					$('#' + nodatadiv).hide();
					$('#' + id + 'Div').show();
				} else {
					tableID.DataTable().clear().destroy();
					$('#' + id + 'Div').hide();
					$('#' + nodatadiv).show();
				}

				if (screenLoadID != null) {
					setTimeout(function () {
						loading('#' + screenLoadID, false);
					}, 100);
				}
			}
		});
	} else {
		return tableID.DataTable({
			// "pagingType": "full_numbers",
			"processing": true,
			"serverSide": true,
			"responsive": true,
			"iDisplayLength": 10,
			"bLengthChange": true,
			"searching": true,
			"ajax": {
				type: 'POST',
				url: $('meta[name="base_url"]').attr('content') + url,
				dataType: "JSON",
				data: dataSent,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'content-type': 'application/x-www-form-urlencoded',
				},
				"error": function (xhr, error, exception) {
					if (exception) {
						if (isError(xhr.status))
							noti(xhr.status, exception);
					}
				}
			},
			"language": {
				"searchPlaceholder": 'Search...',
				"sSearch": '',
				// "lengthMenu": '_MENU_ item / page',
				// "paginate": {
				// 	"first": "First",
				// 	"last": "The End",
				// 	"previous": "Previous",
				// 	"next": "Next"
				// },
				// "info": "Showing _START_ to _END_ of _TOTAL_ items",
				// "emptyTable": "No data is available in the table",
				// "info": "Showing _START_ to _END_ of _TOTAL_ items",
				// "infoEmpty": "Showing 0 to 0 of 0 items",
				// "infoFiltered": "(filtered from _MAX_ number of items)",
				// "zeroRecords": "No matching records",
				"processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
				"loadingRecords": "Loading...",
				// "infoPostFix": "",
				// "thousands": ",",
			},
			"columnDefs": hasData(options, 'column', true, []),
			"order": hasData(options, 'order', true, [[0, 'asc']]),
			initComplete: function () {

				var totalData = this.api().data().length;

				if (totalData > 0) {
					$('#' + nodatadiv).hide();
					$('#' + id + 'Div').show();
				} else {
					tableID.DataTable().clear().destroy();
					$('#' + id + 'Div').hide();
					$('#' + nodatadiv).show();
				}

				if (screenLoadID != null) {
					setTimeout(function () {
						loading('#' + screenLoadID, false);
					}, 100);
				}

			}
		});
	}
}

const generateDatatableClient = async (id, url = null, dataObj = null, options = [], nodatadiv = 'nodatadiv', screenLoadID = 'nodata') => {

	const tableID = $('#' + id);
	var table = tableID.DataTable().clear().destroy();

	$.ajaxSetup({
		data: {}
	});

	loading('#' + screenLoadID, true);

	const res = await callApi('post', url, dataObj);

	if (isSuccess(res)) {
		if (hasData(res.data)) {
			table = tableID.DataTable({
				"data": res.data,
				"deferRender": true,
				"processing": true,
				"serverSide": false,
				'paging': true,
				'ordering': true,
				'info': true,
				'responsive': true,
				'iDisplayLength': 10,
				'bLengthChange': true,
				'searching': true,
				'autoWidth': false,
				'language': {
					"searchPlaceholder": 'Search...',
					"sSearch": '',
					// "lengthMenu": '_MENU_ item / page',
					// "paginate": {
					// 	"first": "First",
					// 	"last": "The End",
					// 	"previous": "Previous",
					// 	"next": "Next"
					// },
					// "info": "Showing _START_ to _END_ of _TOTAL_ items",
					// "emptyTable": "No data is available in the table",
					// "info": "Showing _START_ to  _END_ of  _TOTAL_ items",
					// "infoEmpty": "Showing 0 to 0 of 0 items",
					// "infoFiltered": "(filtered from _MAX_ number of items)",
					// "zeroRecords": "No matching records",
					// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment..",
					// "loadingRecords": "Loading...",
					// "infoPostFix": "",
					// "thousands": ",",
				},
				"columnDefs": hasData(options, 'column', true, []),
				"order": hasData(options, 'order', true, [[0, 'asc']]),
			});

			$('#' + nodatadiv).hide();
			$('#' + id + 'Div').show();
		} else {
			$('#' + nodatadiv).empty(); // reset
			$('#' + nodatadiv).html(nodata());
			$('#' + nodatadiv).show();
			$('#' + id + 'Div').hide();
		}
	}

	loading('#' + screenLoadID, false);

	return table;
}

const loadFileContent = (fileName, sizeModal = 'lg', title = 'Default Title', dataArray = null, typeModal = 'modal') => {

	const idContent = typeModal == 'modal' ? "generalContent-" + sizeModal : "offCanvasContent-right";
	$('#' + idContent).empty(); // reset

	const listSize = ['xs', 'sm', 'md', 'lg', 'xl', 'fullscreen'];
	listSize.forEach(size => {
		const idModalContent = 'generalContent-' + size;
		if (document.getElementById(idModalContent)) {
			$('#' + idModalContent).empty(); // reset
		}
	});

	return $.ajax({
		type: "POST",
		url: $('meta[name="base_url"]').attr('content') + 'init',
		data: {
			action: 'modal',
			baseUrl: $('meta[name="base_url"]').attr('content'),
			fileName: `app/views/${fileName}`,
			dataArray: dataArray,
		},
		dataType: "html",
		success: function (data) {

			$('#' + idContent).append(data);

			setTimeout(function () {
				if (typeof getPassData == 'function') {
					getPassData($('meta[name="base_url"]').attr('content'), dataArray);
				} else {
					console.log('function getPassData not initialize!');
				}
			}, 80);

			if (typeModal == 'modal') {
				$('#generalTitle-' + sizeModal).text(title);
				$('#generalModal-' + sizeModal).modal('show');
			} else {
				// reset
				$('.custom-width').css('width', '400px');

				$('#offCanvasTitle-right').text(title);
				$('#generaloffcanvas-right').offcanvas('toggle');
				$('.custom-width').css('width', sizeModal);
			}
		}
	});
}

const loadFormContent = (fileName, sizeModal = 'lg', urlFunc = null, title = 'Default Title', dataArray = null, typeModal = 'modal') => {
	const idContent = typeModal == 'modal' ? "generalContent-" + sizeModal : "offCanvasContent-right";
	$('#' + idContent).empty(); // reset

	const listSize = ['xs', 'sm', 'md', 'lg', 'xl', 'fullscreen'];
	listSize.forEach(size => {
		const idModalContent = 'generalContent-' + size;
		if (document.getElementById(idModalContent)) {
			$('#' + idModalContent).empty(); // reset
		}
	});

	return $.ajax({
		type: "POST",
		url: $('meta[name="base_url"]').attr('content') + 'init',
		data: {
			action: 'modal',
			baseUrl: $('meta[name="base_url"]').attr('content'),
			fileName: `app/views/${fileName}`,
			dataArray: dataArray,
		},
		dataType: "html",
		success: function (response) {
			$('#' + idContent).append(response);

			setTimeout(function () {
				if (typeof getPassData == 'function') {
					getPassData($('meta[name="base_url"]').attr('content'), dataArray);
				} else {
					console.log('function getPassData not initialize!');
				}
			}, 80);

			// get form id
			var formID = $('#' + idContent + ' > form').attr('id');
			// > div:first-child

			$("#" + formID)[0].reset(); // reset form
			document.getElementById(formID).reset(); // reset form
			$("#" + formID).attr('action', urlFunc); // set url

			if (typeModal == 'modal') {
				$('#generalTitle-' + sizeModal).text(title);
				$('#generalModal-' + sizeModal).modal('show');
				$("#" + formID).attr("data-modal", '#generalModal-' + sizeModal);
			} else {
				// reset
				$('.custom-width').css('width', '400px');

				$('#offCanvasTitle-right').text(title);
				$('#generaloffcanvas-right').offcanvas('toggle');
				$("#" + formID).attr("data-modal", '#generaloffcanvas-right');
				$('.custom-width').css('width', sizeModal);
			}

			if (dataArray != null) {
				$.each($('input, select ,textarea', "#" + formID), function (k) {
					var type = $(this).prop('type');
					var name = $(this).attr('name');

					if (type == 'radio' || type == 'checkbox') {
						$("input[name=" + name + "][value='" + dataArray[name] + "']").prop(
							"checked", true);
					} else {
						$('#' + name).val(dataArray[name]);
					}

				});
			}

		}
	});
}

/**
 * Generate pagination view using Bootstrap classes with rounded page numbers.
 *
 * @param {Object} paginationData Pagination data including current page, total pages, etc.
 * @param {function|null} callback Callback function to be called when a page link is clicked.
 * @return {string} HTML markup for pagination view.
 */
const generateBsPagination = (paginationData, callback = null) => {

	let html = '';
	if (hasData(paginationData, 'total')) {
		const currentPage = paginationData.current_page;
		const totalPages = paginationData.last_page;

		html = '<nav aria-label="Page navigation"><ul class="pagination">';

		// Previous page button (always shown, disabled if on the first page)
		html += `<li class="page-item ${currentPage == 1 ? 'disabled' : ''}">`;
		html += `<a class="page-link" ${currentPage != 1 ? `onclick="${callback}(1)"` : ''} aria-label="First"><span aria-hidden="true">&laquo;&laquo;</span></a></li>`;
		html += `<li class="page-item ${currentPage == 1 ? 'disabled' : ''}">`;
		html += `<a class="page-link" ${currentPage != 1 ? `onclick="${callback}(${currentPage - 1})"` : ''} aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>`;

		// Previous page numbers
		for (let i = Math.max(1, Math.round(currentPage) - 2); i < currentPage; i++) {
			html += `<li class="page-item"><a class="page-link" ${callback !== null ? `onclick="${callback}(${i})"` : `href="?page=${i}"`}>${i}</a></li>`;
		}

		// Current page with active class
		html += `<li class="page-item active"><span class="page-link">${Math.round(currentPage)}</span></li>`;

		// Next page numbers
		for (let i = Math.round(currentPage) + 1; i <= Math.min(Math.round(currentPage) + 2, totalPages); i++) {
			html += `<li class="page-item"><a class="page-link" ${callback !== null ? `onclick="${callback}(${i})"` : `href="?page=${i}"`}>${i}</a></li>`;
		}

		// Next page button (always shown, disabled if on the last page)
		html += `<li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">`;
		html += `<a class="page-link" ${callback != null ? `onclick="${callback}(${Math.round(currentPage) + 1})"` : `href="?page=${Math.round(currentPage) + 1}"`} aria-label="Next" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}><span aria-hidden="true">&raquo;</span></a></li>`;
		html += `<li class="page-item ${currentPage == totalPages ? 'disabled' : ''}">`;
		html += `<a class="page-link" ${callback != null ? `onclick="${callback}(${totalPages})"` : `href="?page=${totalPages}"`} aria-label="Next" ${currentPage === totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}><span aria-hidden="true">&raquo;&raquo;</span></a></li>`;

		html += '</ul></nav>';
	}

	return html;
};
