const income = new Finance('income', 'Przychody');
const expense = new Finance('expense', 'Wydatki');
const financeData = [income, expense];

let balance = 0;

$(document).ready(() => {
    // Load google charts
    google.charts.load(50, {
        'packages': ['corechart'],
        'callback': () => getData('currentMonth', $('#currentMonthButton'))
    });
});

//Calculate and display balance & balance message
const setBalance = () => {
    balance = income.sumOfFinance - expense.sumOfFinance;

    $('#financeSummary').html(Messages.getBalanceMessage(balance.toFixed(2)));

    if (balance > 0) {
        $('#message').html(Messages.getBalancePositiveMessage());
    } else if (balance < 0) {
        $('#message').html(Messages.getBalanceNegativeMessage());
    } else {
        $('#message').html('');
    }
}

//Gets data from currently set time period
const getData = (timePeriod, button) => {
    //Clear data array
    financeData.forEach((finance) => {
        finance.clear();
        finance.getSumOfFinanceInCategories(timePeriod);
    });

    setDropdownActive(button);
}

//Get data from custom time period
const getCustomPeriodData = () => {
    const customPeriodDates = $('#customDate').serializeArray();
    const startDate = customPeriodDates[0].value;
    const endDate = customPeriodDates[1].value;

    financeData.forEach((finance) => {
        finance.clear();
        finance.getSumOfFinanceInCategories('customPeriod', startDate, endDate);
    });

    $('#choseTimePeriod').modal('hide');
    $('#dropdownMenu .active').removeClass('active');
    $('#customPeriodButton').addClass('active');
}

//Populate table with finance data
const populateTable = (financeObject) => {

    currentFinanceType = financeObject.financeType;

    $('#' + currentFinanceType + 'Table').text('');

    if (!financeObject.isEmpty()) {
        $.each(financeObject.sumOfFinanceInCategories, (key, value) => {
            $([{ categoryName: value[0], categorySum: value[1].toFixed(2) }
            ].map(Messages.getTableRow).join('')).appendTo('#' + currentFinanceType + 'Table');
        });
    }

    $('#' + currentFinanceType + 'Sum').text(financeObject.sumOfFinance.toFixed(2));
}

const setDropdownActive = (button) => {
    $('#dropdownMenu .active').removeClass('active');
    $(button).addClass('active');
}

const renderCharts = () => {
    financeData.forEach((finance) => finance.drawChart());
    drawColumnChart();
}

//Run after all ajax requests finished
$(document).ajaxStop(() => {
    financeData.forEach((finance) => populateTable(finance));
    setBalance();
    renderCharts();
});

// Redraw chart when window is resized
$(window).resize(() => renderCharts());

const drawColumnChart = () => {
    const data = new google.visualization.arrayToDataTable([
        ['Kategoria', 'Kwota', { role: 'style' }],
        ["Przychody", income.sumOfFinance, '#36b03c'],
        ["Wydatki", expense.sumOfFinance, 'dc3545']
    ]);

    const view = new google.visualization.DataView(data);

    const options = {
        title: 'Balans przychodów i wydatków',
        width: '100%',
        height: '100%',
        bar: { groupWidth: "40%" },
        legend: { position: "none" },
        fontSize: 16,
        fontName: 'Lato',
        vAxis: { gridlines: { count: 3 }, minValue: 0 },
        animation: { startup: true, duration: 1000, easing: 'out' }
    };

    const chart = new google.visualization.ColumnChart(document.getElementById("balanceColumnChart"));
    chart.draw(view, options);
}