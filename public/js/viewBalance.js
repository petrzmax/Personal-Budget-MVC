var sumOfIncomeInCategories = [[], []];
var sumOfIncome = 0;

const tableRowTemplate = ({ categoryName, categorySum}) => `
    <tr>
        <td>${categoryName}</td>
        <td>${categorySum}</td>
    </tr>
`;

// Load google charts
google.charts.load('current', {'packages':['corechart']});


google.charts.setOnLoadCallback(function () {drawIncomeChart();});
google.charts.setOnLoadCallback(function () {drawExpenseChart();});

getData();



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

//AJAX
//Get Categories & income Sum in them - array
function getSumOfIncomeInCategories(timePeriod) {

    $.ajax({
        type: 'POST',
        url: '/view-balance/get-sum-of-income-in-categories',
        dataType: 'json',
        data: {
            activeTimePeriod: timePeriod
        },

        success: function(result) {
            setIncomeSumArray(result);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}

//Populate array with data from AJAX request in format ready for google chart 
function setIncomeSumArray(result) {
    $.each(result, function( key, value ) {
        sumOfIncomeInCategories[key] = [value.name, parseFloat(value.categorySum)];
function populateTable() {
    $('#incomeTable').text('');
    
    $.each(sumOfIncomeInCategories, function(key, value) {
        $([{ categoryName: value[0], categorySum: value[1].toFixed(2) }
        ].map(tableRowTemplate).join('')).appendTo('#incomeTable');
    });
    $('#sumOfIncome').text(sumOfIncome.toFixed(2));
    
}
