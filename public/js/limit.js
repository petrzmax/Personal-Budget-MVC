var categoryId;
var currentExpenseLimit;
var currentMonthSum = 0;
var balance = 0;
var valueInput = 0;
var spentAndValue = 0;

function radioClickHandler(clickedRadio) {
    categoryId = clickedRadio.value;

    $('#limitSection').slideDown('slow');

    getLimitData();
    getCategoryCurrentMonthSumById();
}

//Hides limit section
function hideLimitSection() {
    $('#limitSection').slideUp('slow');
}

//AJAX
//
//Get category limit from db
function getLimitData() {
    
    $.ajax({
        type: 'POST',
        url: '/add-expense/get-limit-data',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: function(result) {
            setCurrentExpenseLimit(result.expense_limit);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}

//AJAX
//
//Get category expense Sum from current month from db
function getCategoryCurrentMonthSumById() {

    $.ajax({
        type: 'POST',
        url: '/add-expense/get-category-current-month-sum-by-id',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: function(result) {
            setCurrentMonthSum(result);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}


$("#valueInput").change(function(){
    alert($(this).val());
});

function updateInput() {


    
}

//Set currentExpenseLimit value and set it in label
function setCurrentExpenseLimit(value) {
    currentExpenseLimit = parseFloat(value);

    $("#limit").text(currentExpenseLimit.toFixed(2));
}

//Set currentMonthSum value and set it in label
function setCurrentMonthSum(value) {
    currentMonthSum = parseFloat(value);

    $("#spent").text(currentMonthSum.toFixed(2));
}

//Calculate balance and set it in label
function setBalance() {
    balance = currentExpenseLimit - currentMonthSum;
    $(".balance").text(balance.toFixed(2));
}


function setLimitSectionStyle() {
    if(currentExpenseLimit > spentAndValue) {
        $('#limitSection').removeClass('alert-danger').addClass('alert-success');
    } else {
        $('#limitSection').removeClass('alert-success').addClass('alert-danger');
    }
    
}



/*
PLAN działania:
1#. Sprawdź czy kliknięty przycisk ma włączony limit. Jeśli nie nic nie rób.
2#. Jeśli tak pobierz wartość limitu oraz sumę wydatków danej kategorii z danego miesiąca
3. Wyświetl te dane na ekranie w odpowiednich miejscach
*/