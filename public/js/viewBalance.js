var sumOfIncomeInCategories = [[], []];

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
    });
    console.log(sumOfIncomeInCategories);
}