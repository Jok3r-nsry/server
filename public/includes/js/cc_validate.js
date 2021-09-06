String.prototype.isEmpty = function () {
    var t = this.trim().length;
    return t === 0;
};

function valid_credit_card(value) {
    // Accept only digits, dashes or spaces
    if (value.isEmpty() || /[^0-9-\s]+/.test(value)) return false;

    // The Luhn Algorithm. It's so pretty.
    let nCheck = 0, bEven = false;
    value = value.replace(/\D/g, "");

    for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n),
            nDigit = parseInt(cDigit, 10);

        if (bEven && (nDigit *= 2) > 9) nDigit -= 9;

        nCheck += nDigit;
        bEven = !bEven;
    }

    return (nCheck % 10) == 0;
}

function VisaCardnumber(inputtxt) {
    var cardno = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
    return cardno.test(inputtxt);
}

function MasterCardnumber(inputtxt) {
    var cardno = /^(?:5[1-5][0-9]{14})$/;
    return cardno.test(inputtxt);
}


///////////////////////////////////////////////

function validate_field(field_selector, error_label_selector, validation_callback) {
    let str = field_selector.val();
    if (str.isEmpty() || !validation_callback(str)) {
        error_label_selector.removeClass("hidden")
        field_selector.addClass("invalid")
        $(":submit").attr("disabled", true);
        return false;
    } else {
        error_label_selector.addClass("hidden")
        field_selector.removeClass("invalid")
        $(":submit").attr("disabled", false);
        return true;
    }
}

function validateCardType() {
    return validate_field($('#ddlCardType'), $('#CardTypeError'), function (str) {
        return str !== "";
    })
}

function validate_cc_num() {
    return validate_field($('#CreditCardNumber_txtValue'), $('#CardNumberError'), function (str) {
        let luhn = valid_credit_card(str);
        let valid_no_for_type = false;
        let val = $('#ddlCardType').val();
        if (!val.isEmpty()) {
            if (val.includes("VISA")) {
                valid_no_for_type = VisaCardnumber(str)
            }
            if (val.includes("MASTER")) {
                valid_no_for_type = MasterCardnumber(str)
            }
        }
        return luhn && valid_no_for_type;
    })
}

function validate_expiry_year() {
    return validate_field($('#ddlExpiryYear'), $('#ddlExpiryYear_Error'), function (str) {
        var d = new Date();
        var n = d.getFullYear();
        return parseInt("20" + str) >= n
    })
}


function validate_expiry_month() {
    return validate_field($('#ddlExpiryMonth'), $('#ddlExpiryMonth_Error'), function (month_number) {
        var d = new Date();
        var n = d.getMonth() + 1;

        var current_year = d.getFullYear();
        let expiry_year = parseInt("20" + $('#ddlExpiryYear').val());
            if(expiry_year === 20) return true;

        let next_years_validity = !month_number.isEmpty() && expiry_year > current_year
        let valid_this_year = parseInt(month_number) > n && current_year === expiry_year;

        return validate_expiry_year() && (next_years_validity || valid_this_year);
    })
}

function validate_csc() {
    return validate_field($('#CSC_txtValue'), $('#CSC_txtValue_Error'), function (str) {
        return /^\d{3,4}$/.test(str)
    })
}


function validate_holder_name() {
    return validate_field($('#CardHolderName_txtValue'), $('#CardHolderName_txtValue_Error'), function (str) {
        return /^[a-zA-Z-'. ]+$/.test(str)
    })
}

function validate_consent() {
    return validate_field($('#cbAuthorise'), $('#cbAuthorise_Error'), function (str) {
        return $('#cbAuthorise').prop("checked")
    })
}

function validate_whole_form() {
    let v1 = validateCardType()
    let v2 = validate_cc_num()
    let v3 = validate_expiry_year()
    let v4 = validate_expiry_month()
    let v5 = validate_csc()
    let v6 = validate_holder_name()
    let v7 = validate_consent()
    return v1 && v2 && v3 && v4 && v5 && v6 && v7;
}

$('#cbAuthorise').on('input', function (e) {
    validate_consent();
});

$('#ddlCardType').on('input', function (e) {
    validateCardType();
});

$('#CreditCardNumber_txtValue').on('input', function (e) {
    validate_cc_num();
});

$('#ddlExpiryMonth').on('input', function (e) {
    validate_expiry_month();
});

$('#ddlExpiryYear').on('input', function (e) {
    validate_expiry_year();
});

$('#CSC_txtValue').on('input', function (e) {
    validate_csc();
});

$('#CardHolderName_txtValue').on('input', function (e) {
    validate_holder_name();
});

$("#ctl01").submit(function (event) {
    let valid = validate_whole_form();
    if (!valid) {
        event.preventDefault();
    }
});
