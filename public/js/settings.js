const deleteModalText = "Czy na pewno chcesz usunąć kategorię \"";
const addCategoryModalTitle = "Dodaj nową kategorię";
const editCategoryModalTitle = "Edytuj kategorię";

let categoryId;
let categoryType;
let buttonType;

const categoryTemplate = ({ newCategoryName, newCategoryId, newCategoryType }) => `
    <div class="row" style="display: none;" id="${newCategoryType}${newCategoryId}">
        <div class="col">
            <li>${newCategoryName}</li>
        </div>
        <div class="col-auto">
        <button class="btn btn-sm btn-primary p-0" onclick="getCategoryData('edit', \'${newCategoryType}\', ${newCategoryId})">
            <i class="icon-pencil"></i>
        </button>
        
        <button class="btn btn-sm btn-danger p-0" onclick="getCategoryData('delete', \'${newCategoryType}\', ${newCategoryId})">
            <i class="icon-trash-empty"></i>
        </button>
        </div>
    </div> 
`;

//Add button handler
const addCategoryHandler = (newCategoryType) => {
    categoryType = newCategoryType;

    //Set proper modal title
    $('#editModalLabel').text(addCategoryModalTitle);
    //Reset category name & limit input
    $('#categoryName').val('');
    $('#limit').val(parseFloat(0).toFixed(2));
    $('#limitCheck').prop( "checked", false );
    //Set proper button function
    $('#editForm').attr('action', "javascript:addCategory()");

    switchLimitForm();

    $('#editModal').modal('show');
}

//Show limit form only for expense categories
const switchLimitForm = () => {
    if(categoryType == 'expense') {
        $('#limitForm').show();
    }
    else {
        $('#limitForm').hide();
    }
};

const showProperModal = result => {
    //Show proper modal
    switch (buttonType) {
        case 'edit':

            switchLimitForm();
            //Set proper modal title
            $('#editModalLabel').text(editCategoryModalTitle);
            //Set proper button function
            $('#editForm').attr('action', "javascript:updateCategory()");
            
            $('#categoryName').val(result.name);
            $('#limit').val(result.expense_limit);

            $('#limitCheck').prop( "checked", result.limit_active == 1 ? true : false);
            $('#editModal').modal('show');
            break;
        case 'delete':

            $('#deleteModal').modal('show');
            $('#deleteModalText').text(deleteModalText + result.name + "\"?"); //Replace and set the text back
            break;
    }
};

//Remove deleted category row from proper div & hide modal
const removeCategoryRow = () => {
    $('#deleteModal').modal('hide');
    $('#' + categoryType + categoryId).slideUp('medium', () => this.remove());
};

//Append new category row to proper div & hide modal
const addCategoryRow = (categoryName, returnedCategoryId) => {
    $('#editModal').modal('hide');
    
    var currentCategoryRow = $([
        { newCategoryName: categoryName, newCategoryId: returnedCategoryId, newCategoryType: categoryType }
    ].map(categoryTemplate).join('')).appendTo('#'+categoryType+'CategoriesBody');

    currentCategoryRow.slideDown('slow');
};

//Update edited category row & hide modal
const updateCategoryRow = categoryName => {
    $('#editModal').modal('hide');
    
    $('#' + categoryType + categoryId).slideUp('medium', () => {
        $("li", this).text(categoryName);
        $(this).slideDown('medium');
    });
};

//AJAX
// Also edit & delete button action handler
//Get category data from db
const getCategoryData = (clickedButtonType, clickedCategoryType, clickedCategoryId) => {

    buttonType = clickedButtonType;
    categoryId = clickedCategoryId;
    categoryType = clickedCategoryType;

    $.ajax({
        type: 'POST',
        url: '/settings/getCategoryData',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },

        success: (result) => showProperModal(result),
        error: () => alert('fail')
    });

};

//Delete selected category
const deleteCategory = () => {
    $.ajax({
        type: 'POST',
        url: '/settings/deleteCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },
        success: removeCategoryRow(),
        error: () => alert('fail')
    });
};

//Add new category to db
const addCategory = () => {

    categoryName = $('#categoryName').val();
    categoryLimit = $('#limit').val();
    categoryLimitState = $('#limitCheck').is(':checked');

    $.ajax({
        type: 'POST',
        url: '/settings/addCategory',
        dataType: 'json',
        data: {
            postCategoryType: categoryType,
            postCategoryName: categoryName,
            postCategoryLimitState: categoryLimitState,
            postCategoryLimit: categoryLimit
        },

        success: (result) => addCategoryRow(categoryName, result),
        error: (xhr) => alert(xhr.status)
    });
};

//Update category in db
const updateCategory = () => {

    categoryName = $('#categoryName').val();
    categoryLimit = $('#limit').val();
    categoryLimitState = $('#limitCheck').is(':checked');

    $.ajax({
        type: 'POST',
        url: '/settings/updateCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType,
            postCategoryName: categoryName,
            postCategoryLimitState: categoryLimitState,
            postCategoryLimit: categoryLimit
        },

        success: () => updateCategoryRow(categoryName),
        error: (xhr) => alert(xhr.status)
    });
};