/**
 * Add jQuery Validation plugin method for a valid password
 * 
 * Valid passwords contain at least one letter and one number.
 */
$.validator.addMethod('validPassword',
    function(value, element, param) {

        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }

        return true;
    },
    'Password must contain at least one letter and number'
);


/*
 * Add jQuery Validation plugin method for a valid name
 *
 * Valid name has no special characters
 */
$.validator.addMethod('noSpecialChars',
    function(value, element, param) {
        if (value.match(/[^a-ząćęłńóśźżĄĘŁŃÓŚŹŻ\s]+/i)) {
            return false;
        }
        return true;
    },
    'Value contains special characters'
);

/*
 * Add jQuery Validation plugin method for a valid name
 *
 * Valid name has no white characters
 */
$.validator.addMethod('noSpaces',
    function(value, element, param) {
        if (value.match(/\s/)) {
            return false;
        }
        return true;
    },
    'Value contains spaces'
);
