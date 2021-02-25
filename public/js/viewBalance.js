var sumOfIncomeInCategories = [[], []];
var sumOfExpenseInCategories = [[], []];
var sumOfIncome = 0;
var balance = 0;

const negativeMessage = 'Uwaga! Wpadasz w długi!';
const positiveMessage = 'Brawo! Wspaniale zarządzasz finansami!';

const tableRowTemplate = ({ categoryName, categorySum}) => `
    <tr>
        <td>${categoryName}</td>
        <td>${categorySum}</td>
    </tr>
`;

class finance {
    constructor(type) {
        this.financeType = type;
    }
    sumOfFinanceInCategories = [[], []];
    sumOfFinance = 0;
    financeType = '';

    clear() {
        this.sumOfFinanceInCategories = [[], []];
        this.sumOfFinance = 0;
    }
}

var income = new finance('income');
var expense = new finance('expense');


// Load google charts
google.charts.load('current', {'packages':['corechart']});

$(document).ready(function () {
    google.charts.setOnLoadCallback(function () {drawIncomeChart();});
    google.charts.setOnLoadCallback(function () {drawExpenseChart();});
    getData();
});



// Draw the chart and set the chart values
function drawIncomeChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Kategoria');
    data.addColumn('number', 'Kwota');
    data.addRows(sumOfIncomeInCategories);

    var options = {
        title: 'Przychody z wybranego okresu',
        width: '100%',
        height: '100%',
            chartArea:{left:0,top:30,width:'100%',height:'100%'},
            fontSize: 16,
            fontName: 'Lato',
            is3D: true,
    };

    // Display the chart inside the <div> element with id="incomePiechart"
    var chart = new google.visualization.PieChart(document.getElementById('incomePiechart'));
    chart.draw(data, options);
}

// Draw the chart and set the chart values
function drawExpenseChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Kategoria');
    data.addColumn('number', 'Kwota');
    data.addRows(sumOfIncomeInCategories);

    var options = {
        title: 'Wydatki z wybranego okresu',
        width: '100%',
        height: '100%',
            chartArea:{left:0,top:30,width:'100%',height:'100%'},
            fontSize: 16,
            fontName: 'Lato',
            is3D: true,
    };
    // Display the chart inside the <div> element with id="incomePiechart"
    var chart = new google.visualization.PieChart(document.getElementById('expensePiechart'));
    chart.draw(data, options);
}

// Redraw chart when window is resized
$(window).resize(function(){
    drawIncomeChart();
    drawExpenseChart();
});



//Populate array with data from AJAX request in format ready for google chart & calculate sum of finance 
function setFinanceSumArray(result, financeObject) {
    $.each(result, function( key, value ) {
        financeObject.sumOfFinanceInCategories[key] = [value.name, parseFloat(value.categorySum)];
        financeObject.sumOfFinance += parseFloat(value.categorySum);
    });
}

function getData(timePeriod, button) {
    //Clear data array
    income.clear();
    expense.clear();

    getSumOfFinanceInCategories(timePeriod, income);
    getSumOfFinanceInCategories(timePeriod, expense);

    setDropdownActive(button);
}

//Populate table with finance data
function populateTable(financeObject) {
    
    currentFinanceType = financeObject.financeType;

    $('#' + currentFinanceType + 'Table').text('');

    $.each(financeObject.sumOfFinanceInCategories, function(key, value) {
        $([{ categoryName: value[0], categorySum: value[1].toFixed(2) }
        ].map(tableRowTemplate).join('')).appendTo('#' + currentFinanceType + 'Table');
    });
    $('#' + currentFinanceType + 'Sum').text(financeObject.sumOfFinance.toFixed(2));
    
}


//Run after all ajax requests finished
$(document).ajaxStop(function() {
    drawIncomeChart();
    populateTable(income);
    populateTable(expense);
});

//AJAX
//Get Categories & finance Sum in them - array
function getSumOfFinanceInCategories(timePeriod, financeObject) {

    $.ajax({
        type: 'POST',
        url: '/view-balance/get-sum-of-finance-in-categories',
        dataType: 'json',
        data: {
            activeTimePeriod: timePeriod,
            postFinanceType: financeObject.financeType
        },

        success: function(result) {
            setFinanceSumArray(result, financeObject);    
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}