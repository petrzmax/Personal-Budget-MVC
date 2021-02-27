class finance {
    constructor(type, chartTitle) {
        this.financeType = type;
        this.chartTitle = chartTitle;
    }
    sumOfFinanceInCategories = [];
    sumOfFinance = 0;
    financeType = '';
    chartTitle = '';

    clear() {
        this.sumOfFinanceInCategories = [];
        this.sumOfFinance = 0;
    }

    isEmpty() {
        if(this.sumOfFinanceInCategories.length > 0) {
            return false;
        } else {
            return true;
        }
    }

    // Draw the chart and set the chart values
    drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Kategoria');
        data.addColumn('number', 'Kwota');

        if(!income.isEmpty()) {
            data.addRows(this.sumOfFinanceInCategories);
        }

        var options = {
            title: this.chartTitle + ' z wybranego okresu',
            width: '100%',
            height: '100%',
                chartArea:{left:0,top:30,width:'100%',height:'100%'},
                fontSize: 16,
                fontName: 'Lato',
                is3D: true,
        };
        // Display the chart inside the <div> element with id="incomePiechart"
        var chart = new google.visualization.PieChart(document.getElementById(this.financeType + 'Piechart'));
        chart.draw(data, options);
    }
}

const negativeMessage = '<h2 class="text-danger my-2">Uwaga! Wpadasz w długi!</h2>';
const positiveMessage = '<h2 class="text-success my-2">Brawo! Wspaniale zarządzasz finansami!</h2>';
const balanceMessageStart = '<h2>Twój bilans z wybranego przedziału czasu: ';
const balanceMessageEnd = ' złotych</h2>';

const tableRowTemplate = ({ categoryName, categorySum}) => `
    <tr>
        <td>${categoryName}</td>
        <td>${categorySum}</td>
    </tr>
`;

var income = new finance('income', 'Przychody');
var expense = new finance('expense', 'Wydatki');
var balance = 0;

$(document).ready(function () {
    // Load google charts
    google.charts.load('current', {'packages':['corechart'], 'callback': function () {
        getData('currentMonth', $('#currentMonthButton'));
    }});
});

// Redraw chart when window is resized
$(window).resize(function(){
    income.drawChart();
    expense.drawChart();
});

//Populate array with data from AJAX request in format ready for google chart & calculate sum of finance 
function setFinanceSumArray(result, financeObject) {
    $.each(result, function( key, value ) {
        financeObject.sumOfFinanceInCategories[key] = [value.name, parseFloat(value.categorySum)];
        financeObject.sumOfFinance += parseFloat(value.categorySum);
    });
}

//Calculate and display balance & balance message
function setBalance() {
    balance = income.sumOfFinance - expense.sumOfFinance;

    $('#financeSummary').replaceWith(balanceMessageStart + balance.toFixed(2) + balanceMessageEnd);

    if(balance < 0) {
        $('#message').replaceWith(negativeMessage);
    } else if (balance > 0) {
        $('#message').replaceWith(positiveMessage);
    }
}

function getData(timePeriod, button) {
    //Clear data array
    income.clear();
    expense.clear();

    getSumOfFinanceInCategories(timePeriod, income);
    getSumOfFinanceInCategories(timePeriod, expense);

    setDropdownActive(button);
}

function getCustomPeriodData() {

    timePeriod = 'customPeriod';

    //Clear data array
    income.clear();
    expense.clear();

    var customPeriodDates = $('#customDate').serializeArray();

    var startDate = customPeriodDates[0].value;
    var endDate = customPeriodDates[1].value;

    getSumOfFinanceInCategories(timePeriod, income, startDate, endDate);
    getSumOfFinanceInCategories(timePeriod, expense, startDate, endDate);

    $('#choseTimePeriod').modal('hide');
    $('#dropdownMenu .active').removeClass('active');

    $('#customPeriodButton').addClass('active');
}

//Populate table with finance data
function populateTable(financeObject) {
    
    currentFinanceType = financeObject.financeType;

    $('#' + currentFinanceType + 'Table').text('');

    if(!financeObject.isEmpty()) {
        $.each(financeObject.sumOfFinanceInCategories, function(key, value) {
            $([{ categoryName: value[0], categorySum: value[1].toFixed(2) }
            ].map(tableRowTemplate).join('')).appendTo('#' + currentFinanceType + 'Table');
        });
    }

    $('#' + currentFinanceType + 'Sum').text(financeObject.sumOfFinance.toFixed(2));
    
}

function setDropdownActive(button) {
    $('#dropdownMenu .active').removeClass('active');

    $(button).addClass('active');
}

//Run after all ajax requests finished
$(document).ajaxStop(function() {
    populateTable(income);
    populateTable(expense);
    setBalance();
    income.drawChart();
    expense.drawChart();
});

//AJAX
//Get Categories & finance Sum in them - array
function getSumOfFinanceInCategories(timePeriod, financeObject, startDate = null, endDate = null) {

    $.ajax({
        type: 'POST',
        url: '/view-balance/get-sum-of-finance-in-categories',
        dataType: 'json',
        data: {
            activeTimePeriod: timePeriod,
            postFinanceType: financeObject.financeType,
            postStartDate: startDate,
            postEndDate: endDate
        },

        success: function(result) {
            setFinanceSumArray(result, financeObject);    
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}