//Validation
//  Validation Approach:   This is for client-side validation to check for valid inputs characters, length etc.  
//                                      Server side validation is also required to check database for valid inputs, strip tags and so on.
//
//  How to use: Create form.  Inside form create empty wrapper for error tips (usually with the class "validateTips").
//                      On form submit, check each field which needs validation using one of the "validXXXX" functions.
//                      These functions will add the error highlight to the correct field and put the message in the error tips wrapper.
//                      Make sure to clear the wrapper and class before validation, and once validation returns successful.


function updateTips(t, errorElement) {
	errorElement
		.text(t)
		//setTimeout(function() {
		//    errorElement.removeClass( "error", 1500 );
		//}, 500 );
}

function clearTips(errorElement) {
	errorElement.text("");
	errorElement.removeClass("alert alert-danger");
}

function checkLength(o, n, min, max, errorElement) {
	if (o.val().length > max || o.val().length < min) {
		updateTips("The Field " + n + " must be at least  " + min + " chars", errorElement);
		return false;
	} else {
		return true;
	}
}

function checkMaxLength(o, n, max, errorElement) {
	if (o.val().length > max) {
		o.addClass("ui-state-error");
		updateTips("Length of " + n + " can be a maximum of  " + max + " characters.", errorElement);
		return false;
	} else {
		return true;
	}
}


function checkRegexp(element, regexp, message, errorElement) {
	if (!(regexp.test(element.val()))) {
		element.addClass("ui-state-error");
		updateTips(message, errorElement);
		return false;
	} else {
		return true;
	}
}

//must have at least one item selected
function validMultiSelect(multiselect, errorElement) {
	if (multiselect.val() == null) {
		multiselect.addClass("ui-state-error");
		updateTips("You must select at least one item", errorElement);
		return false;
	}

	return true;
}

function validEmail(email, errorElement) {
	return checkRegexp(email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Please enter a valid email eg. user@domain.com", errorElement);
}

function validLongName(name, fieldName, errorElement) {
	return checkLength(name, fieldName, 2, 50, errorElement);
}

function validMaxLength(field, fieldName, maxLength, errorElement) {
	return checkMaxLength(field, fieldName, maxLength, errorElement);
}

function validDate(date, errorElement) {
	var valid = !/Invalid|NaN/.test(new Date(date.val()));
	if (!valid) {
		date.addClass("ui-state-error");
		updateTips("Please enter a valid date eg. 2000-01-01", errorElement);
	}
	return valid;
}

//make sure endDate comes after startDate - use validDate first
function validDateRange(startDate, endDate, errorElement) {
	var sd = new Date(startDate.val());
	var ed = new Date(endDate.val());
	if (ed > sd) return true;
	else if (ed.getTime() == sd.getTime()) return true;
	//else it's an error
	startDate.addClass("ui-state-error");
	endDate.addClass("ui-state-error");
	updateTips("Please make sure the end date is after or the same as the start date", errorElement);
	return false;
}

function validTime(time, errorElement) {
	return checkRegexp(time, /^([01][0-9])|(2[0123]):([0-5])([0-9])$/, "Please enter a valid time eg. 13:49", errorElement);
}

//expects input fields with valid time values (HH:mm) so use validTime first 
function validTimeRange(startTime, endTime, errorElement) {
	var st = startTime.val();
	var et = endTime.val();
	var stArr = st.split(':');
	var etArr = et.split(':');
	if (parseInt(etArr[0]) > parseInt(stArr[0])) return true; // the end time hours are greater than start time hours
	if (parseInt(etArr[0]) == parseInt(stArr[0])) {
		if (parseInt(etArr[1]) > parseInt(stArr[1])) return true; //end time minutes are greater than start time minutes
	}

	//otherwise, it's an error
	startTime.addClass("ui-state-error");
	endTime.addClass("ui-state-error");
	updateTips("Please make sure the end time comes after the start time", errorElement);
	return false;
}

//valid decimal number
function validNumber(number, errorElement) {
	//put a cap on numbers to a reasonable value (999 million should be large enough for now...)
	if (number.val() > 999999999) {
		number.addClass("ui-state-error");
		updateTips("Maximum value is 999,999,999", errorElement);
		return false;
	}

	return checkRegexp(number, /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/, "Please enter a valid decimal number", errorElement);
}

function validDigits(digits, errorElement) {
	//put a cap on numbers to a reasonable value (999 million should be large enough for now...)
	if (digits.val() > 999999999) {
		digits.addClass("ui-state-error");
		updateTips("Maximum value is 999,999,999", errorElement);
		return false;
	}

	return checkRegexp(digits, /^\d+$/, "Please enter digits (0-9) only", errorElement)
}

//use validDigits first
function validDigitsRange(digits, min, max, errorElement) {
	if (digits.val() <= max && digits.val() >= min) return true;

	//otherwise it's an error
	digits.addClass("ui-state-error");
	updateTips("Please enter values within the range " + min + " - " + max, errorElement);
	return false;
}

function validLetters(letters, errorElement) {
	return checkRegexp(letters, /^[a-z]+$/i, "Please enter letters (A-Z) only", errorElement);
}

function validNumeric(alpha, errorElement, message) {
	return checkRegexp(alpha, /^[0-9]+$/i, message, errorElement);
}

function validAlphaNumeric(alpha, errorElement) {
	return checkRegexp(alpha, /^\w+$/i, "Please enter letters (A-Z) or numbers (0-9) only", errorElement);
}

function validLettersWithPunc(letters, errorElement) {
	return checkRegexp(letters, /^[a-z-.,()'\"\s]+$/i, "Please enter letters (A-Z) or punctuation only", errorElement);
}

function validURL(url, errorElement) {
	return checkRegexp(url, /^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/, "Please enter a valid URL", errorElement);
}