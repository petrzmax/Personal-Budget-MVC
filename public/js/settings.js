const deleteModalText = "Czy na pewno chcesz usunąć kategorię \"";
const addCategoryModalTitle = "Dodaj nową kategorię";
const editCategoryModalTitle = "Edytuj kategorię";

var categoryId;
var categoryType;
var buttonType;

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
function addCategoryHandler(newCategoryType) {
    categoryType = newCategoryType;

    //Set proper modal title
    $('#editModalLabel').text(addCategoryModalTitle);
    //Reset category name input
    $('#categoryName').val('');
    //Set proper button function
    $('#submitButton').attr('onclick', "addCategory()");

    switchLimitForm();

    $('#editModal').modal('show');
}

//Show limit form only for expense categories
function switchLimitForm() {
    if(categoryType == 'expense') {
        $('#limitForm').show();
    }
    else {
        $('#limitForm').hide();
    }
}

function showProperModal(result) {
    //Show proper modal
    switch (buttonType) {
        case 'edit':

            switchLimitForm();
            //Set proper modal title
            $('#editModalLabel').text(editCategoryModalTitle);
            //Set proper button function
            $('#submitButton').attr('onclick', "updateCategory()");
            
            $('#categoryName').val(result.name);
            $('#editModal').modal('show');
            break;
        case 'delete':

            $('#deleteModal').modal('show');
            $('#deleteModalText').text(deleteModalText + result.name + "\"?"); //Replace and set the text back
            break;
    }
}

//Remove deleted category row from proper div & hide modal
function removeCategoryRow() {
    $('#deleteModal').modal('hide');
    $('#' + categoryType + categoryId).slideUp('medium', function() {this.remove();});
}

//Append new category row to proper div & hide modal
function addCategoryRow(categoryName, returnedCategoryId) {
    $('#editModal').modal('hide');
    
    var currentCategoryRow = $([
        { newCategoryName: categoryName, newCategoryId: returnedCategoryId, newCategoryType: categoryType }
    ].map(categoryTemplate).join('')).appendTo('#'+categoryType+'CategoriesBody');

    currentCategoryRow.slideDown('slow');
}

//Update edited category row & hide modal
function updateCategoryRow(categoryName) {
    $('#editModal').modal('hide');
    
    $('#' + categoryType + categoryId).slideUp('medium', function() {

        $("li", this).text(categoryName);
        $(this).slideDown('medium');
    });

    
}

//AJAX
// Also edit % delete button action handler
//Get category data from db
function getCategoryData(clickedButtonType, clickedCategoryType, clickedCategoryId) {

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

        success: function(result) {
            showProperModal(result);
        },

        error: function(data){
            alert('fail');
        }
    });

}

//Delete selected category
function deleteCategory() {
    $.ajax({
        type: 'POST',
        url: '/settings/deleteCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },
        success: removeCategoryRow(),

        error: function(data){
            alert('fail');
        }
    });
}

//Add new category to db
function addCategory() {

    categoryName = $('#categoryName').val();
    $.ajax({
        type: 'POST',
        url: '/settings/addCategory',
        dataType: 'json',
        data: {
            postCategoryType: categoryType,
            postCategoryName: categoryName
        },

        success: function(result) {
            addCategoryRow(categoryName, result);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}

//Update category in db
function updateCategory() {

    categoryName = $('#categoryName').val();
    $.ajax({
        type: 'POST',
        url: '/settings/updateCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType,
            postCategoryName: categoryName
        },

        success: function() {
            updateCategoryRow(categoryName);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}