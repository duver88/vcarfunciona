// document.addEventListener("turbo:load", loadPhoneNumberCountryCodeData);
document.addEventListener("DOMContentLoaded", loadPhoneNumberCountryCodeData);

function loadPhoneNumberCountryCodeData() {
    loadPhoneNumberCountryCode();
    userCreateForm();
    userEditForm();
    vcardEditForm();
    createSetting();
    loadAlternativePhoneNumber();
}

function loadPhoneNumberCountryCode() {
    if (!$("#phoneNumber").length) {
        return false;
    }

    let input = document.querySelector("#phoneNumber"),
        errorMsg = document.querySelector("#error-msg"),
        validMsg = document.querySelector("#valid-msg");

    let errorMap = [
        Lang.get("js.invalid_number"),
        Lang.get("js.invalid_country_number"),
        Lang.get("js.too_short"),
        Lang.get("js.too_long"),
        Lang.get("js.invalid_number"),
        Lang.get("js.invalid_number"),
    ];

    // initialise plugin
    let intl = window.intlTelInput(input, {
        initialCountry: defaultCountryCodeValue,
        separateDialCode: true,
        geoIpLookup: function (success, failure) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(
                function (resp) {
                    var countryCode = resp && resp.country ? resp.country : "";
                    success(countryCode);
                }
            );
        },
        utilsScript: "../../public/assets/js/inttel/js/utils.min.js",
    });

    let reset = function () {
        input.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("d-none");
        validMsg.classList.add("d-none");
    };

    if (mobileValidation == 1) {
        input.addEventListener("blur", function () {
            reset();
            if (input.value.trim()) {
                if (intl.isValidNumber()) {
                    validMsg.classList.remove("d-none");
                } else {
                    input.classList.add("error");
                    var errorCode = intl.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("d-none");
                }
            }
        });
    }

    // on keyup / change flag: reset
    input.addEventListener("change", reset);
    input.addEventListener("keyup", reset);

    if (typeof phoneNo != "undefined" && phoneNo !== "") {
        setTimeout(function () {
            $("#phoneNumber").trigger("change");
        }, 500);
    }

    $("#phoneNumber").on("blur keyup change countrychange", function () {
        if (typeof phoneNo != "undefined" && phoneNo !== "") {
            intl.setNumber("+" + phoneNo);
            phoneNo = "";
        }
        let getCode = intl.selectedCountryData["dialCode"];
        $("#prefix_code").val(getCode);

        let phoneNumber = $(this).val();
        phoneNumber = phoneNumber.replace(/-/g, "");
        $(this).val(phoneNumber);
    });

    let getCode = intl.selectedCountryData["dialCode"];
    $("#prefix_code").val(getCode);

    let getPhoneNumber = $("#phoneNumber").val();
    let removeSpacePhoneNumber = getPhoneNumber.replace(/\s/g, "");
    $("#phoneNumber").val(removeSpacePhoneNumber);

    $("#phoneNumber").focus();
    $("#phoneNumber").trigger("blur");
}
function loadAlternativePhoneNumber() {
    if (!$("#alternativePhone").length) {
        return false;
    }

    let input = document.querySelector("#alternativePhone"),
        errorMsg = document.querySelector("#alter-error-msg"),
        validMsg = document.querySelector("#alter-valid-msg");

    let errorMap = [
        Lang.get("js.invalid_number"),
        Lang.get("js.invalid_country_number"),
        Lang.get("js.too_short"),
        Lang.get("js.too_long"),
        Lang.get("js.invalid_number"),
    ];

    // initialise plugin
    let intl = window.intlTelInput(input, {
        initialCountry: defaultCountryCodeValue,
        separateDialCode: true,
        geoIpLookup: function (success, failure) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(
                function (resp) {
                    var countryCode = resp && resp.country ? resp.country : "";
                    success(countryCode);
                }
            );
        },
        utilsScript: "../../public/assets/js/inttel/js/utils.min.js",
    });

    let reset = function () {
        input.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("d-none");
        validMsg.classList.add("d-none");
    };

    if (mobileValidation == 1) {
        input.addEventListener("blur", function () {
            reset();
            if (input.value.trim()) {
                if (intl.isValidNumber()) {
                    validMsg.classList.remove("d-none");
                } else {
                    input.classList.add("error");
                    var errorCode = intl.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("d-none");
                }
            }
        });
    }

    // on keyup / change flag: reset
    input.addEventListener("change", reset);
    input.addEventListener("keyup", reset);

    if (typeof phoneNo != "undefined" && phoneNo !== "") {
        setTimeout(function () {
            $("#alternativePhone").trigger("change");
        }, 500);
    }

    $("#alternativePhone").on("blur keyup change countrychange", function () {
        if (typeof phoneNo != "undefined" && phoneNo !== "") {
            intl.setNumber("+" + phoneNo);
            phoneNo = "";
        }
        let getCode = intl.selectedCountryData["dialCode"];
        $("#alternative_prefix_code").val(getCode);

        let alterphoneNumber = $(this).val();
        alterphoneNumber = alterphoneNumber.replace(/-/g, "");
        $(this).val(alterphoneNumber);
    });

    let getCode = intl.selectedCountryData["dialCode"];
    $("#alternative_prefix_code").val(getCode);

    let getPhoneNumber = $("#alternativePhone").val();
    let removeSpacePhoneNumber = getPhoneNumber.replace(/\s/g, "");
    $("#alternativePhone").val(removeSpacePhoneNumber);

    $("#alternativePhone").focus();
    $("#alternativePhone").trigger("blur");
}

function userCreateForm() {
    if (!$("#userCreateForm").length) {
        return false;
    }
    if (mobileValidation == 1) {
        $("#userCreateForm").submit(function () {
            if ($("#error-msg").text() !== "") {
                $("#phoneNumber").focus();
                return false;
            }
        });
    }
}

function vcardEditForm() {
    if (!$("#editForm").length) {
        return false;
    }
    if (mobileValidation == 1) {
        $("#editForm").submit(function () {
            if ($("#error-msg").text() !== "") {
                $("#phoneNumber").focus();
                $("#alternativePhone").focus();
                return false;
            }
        });
    }
}

function createSetting() {
    if (!$("#createSetting").length) {
        return false;
    }
    if (mobileValidation == 1) {
        $("#createSetting").submit(function () {
            if ($("#error-msg").text() !== "") {
                $("#phoneNumber").focus();
                return false;
            }
        });
    }
}

function userEditForm() {
    if (!$("#userEditForm").length) {
        return false;
    }
    if (mobileValidation == 1) {
        $("#userEditForm").submit(function () {
            if ($("#error-msg").text() !== "") {
                $("#phoneNumber").focus();
                return false;
            }
        });
    }
}
