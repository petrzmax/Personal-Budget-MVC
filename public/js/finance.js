class Finance {
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

    isEmpty = () => !this.sumOfFinanceInCategories.length > 0;

    //Populate array with data from AJAX request in format ready for google chart & calculate sum of finance 
    setFinanceSumArray(result) {
        $.each(result, (key, value) => {
            this.sumOfFinanceInCategories[key] = [value.name, parseFloat(value.categorySum)];
            this.sumOfFinance += parseFloat(value.categorySum);
        });
    }

    //Get Categories & finance Sum in them - array
    getSumOfFinanceInCategories(timePeriod, startDate = null, endDate = null) {

        $.ajax({
            type: 'POST',
            url: '/view-balance/get-sum-of-finance-in-categories',
            dataType: 'json',
            data: {
                activeTimePeriod: timePeriod,
                postFinanceType: this.financeType,
                postStartDate: startDate,
                postEndDate: endDate
            },

            success: (result) => this.setFinanceSumArray(result),
            error: (xhr) => alert(xhr.status)
        });
    }

    // Draw the chart and set the chart values
    drawChart() {
        const data = new google.visualization.DataTable();
        data.addColumn('string', 'Kategoria');
        data.addColumn('number', 'Kwota');

        if (!income.isEmpty()) {
            data.addRows(this.sumOfFinanceInCategories);
        }

        const options = {
            title: this.chartTitle + ' z wybranego okresu',
            width: '100%',
            height: '100%',
            chartArea: { left: 0, top: 30, width: '100%', height: '100%' },
            fontSize: 16,
            fontName: 'Lato',
            is3D: true,
        };
        // Display the chart inside the <div> element with id="incomePiechart"
        let chart = new google.visualization.PieChart(document.getElementById(this.financeType + 'Piechart'));
        chart.draw(data, options);
    }
}
