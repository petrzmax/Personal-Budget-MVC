let categoryId = 0;
let currentExpenseLimit = 0;
let currentMonthSum = 0;
let balance = 0;
let valueInput = 0;
let spentAndValue = 0;

const radioClickHandler = clickedRadio => {
    categoryId = clickedRadio.value;

    $('#limitSection').slideDown('slow');
    getLimitData();
    getCategoryCurrentMonthSumById();
};

//Hides limit section
const hideLimitSection = () => {
    $('#limitSection').slideUp('slow');
};

//AJAX
//
//Get category limit from db
const getLimitData = () => {
    $.ajax({
        type: 'POST',
        url: '/add-expense/get-limit-data',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: (result) => setCurrentExpenseLimit(result.expense_limit),
        error: (xhr) => alert(xhr.status)
    });
};

//AJAX
//
//Get category expense Sum from current month from db
const getCategoryCurrentMonthSumById = () => {
    $.ajax({
        type: 'POST',
        url: '/add-expense/get-category-current-month-sum-by-id',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: (result) => setCurrentMonthSum(result),
        error: (xhr) => alert(xhr.status)
    });
};

//Get data from value input, calculate spendAndValue & set in in label
const updateInput = (valueInputBox = 0) => {
    if (valueInputBox) {
        valueInput = parseFloat(valueInputBox.value);
    }

    spentAndValue = currentMonthSum + valueInput;
    $('#spentAndValueInput').text(spentAndValue.toFixed(2));

    if($('#limitSection').is(':visible')) {
        setLimitSectionStyle();
    }
};

//Set currentExpenseLimit value and set it in label
const setCurrentExpenseLimit = value => {
    currentExpenseLimit = parseFloat(value);
    $("#limit").text(currentExpenseLimit.toFixed(2));
};

//Set currentMonthSum value and set it in label
const setCurrentMonthSum = value => {
    currentMonthSum = parseFloat(value);
    $("#spent").text(currentMonthSum.toFixed(2));
};

//Calculate balance & set it in label
const setBalance = () => {
    balance = currentExpenseLimit - currentMonthSum;
    $("#balance").text(balance.toFixed(2));
};

//Change limit section color & set message depending on limit & value balance
const setLimitSectionStyle = () => {
    if (currentExpenseLimit >= spentAndValue) {
        $('#limitSection').removeClass('alert-danger').addClass('alert-success');
        $("#limitMessage").text(Messages.getLimitPositiveMessage(balance.toFixed(2)));
    } else {
        $('#limitSection').removeClass('alert-success').addClass('alert-danger');
        $("#limitMessage").text(Messages.getLimitNegativeMessage());
    }
};

//Run after all ajax requests finished
$(document).ajaxStop(function () {
    setBalance();
    updateInput();
    setLimitSectionStyle();
});