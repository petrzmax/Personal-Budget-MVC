const deleteModalText = "Czy na pewno chcesz usunąć kategorię \"";
const addCategoryModalTitle = "Dodaj nową kategorię";
const editCategoryModalTitle = "Edytuj kategorię";

var categoryId;
var categoryName;
var categoryType;
var buttonType;

const categoryTemplate = ({ newCategoryName, newCategoryId, newCategoryType }) => `
    <div class="row" style="display: none;">
        <div class="col">
            <li>${newCategoryName}</li>
        </div>
        <div class="col-auto" categoryId="${newCategoryId}" categoryType="${newCategoryType}">
        <button class="btn btn-sm btn-primary p-0 editBtn" buttonType="edit">
            <i class="icon-pencil"></i>
        </button>
        
        <button class="btn btn-sm btn-danger p-0 editBtn" buttonType="delete">
            <i class="icon-trash-empty"></i>
        </button>
        </div>
    </div> 
`;


//Edit button handler
$(document).on('click', '.editBtn', function () {

    //Get data from button
    categoryId = $(this).parent().attr('categoryId');
    categoryType = $(this).parent().attr('categoryType');
    buttonType = $(this).attr('buttonType');

    getCategoryData();
    
});

//Add button handler
$(document).on('click', '.addBtn', function () {
    categoryType = $(this).attr('categoryType');
    

    //Set proper modal title
    $('#editModalLabel').text(addCategoryModalTitle);
    //Reset category name input
    $('#categoryName').val('');

    switchLimitForm(categoryType);

    $('#editModal').modal('show');

});

//Show limit form only for expense categories
function switchLimitForm(displayedCategoryType) {
    if(displayedCategoryType == 'expense') {
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

            //Set proper modal title
            $('#editModalLabel').text(editCategoryModalTitle);
            
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
    var currentCategoryRow = $("div").find(`[categoryId='${categoryId}'][categoryType='${categoryType}']`).parent();
    
    currentCategoryRow.slideUp('medium', function() {currentCategoryRow.remove();});
}

//Append new category row to proper div & hide modal
function addCategoryRow(returnedCategoryId) {
    $('#editModal').modal('hide');
    
    var currentCategoryRow = $([
        { newCategoryName: categoryName, newCategoryId: returnedCategoryId, newCategoryType: categoryType }
    ].map(categoryTemplate).join('')).appendTo('#'+categoryType+'CategoriesBody');

    currentCategoryRow.slideDown('slow');
}

//AJAX

//Get category data from db
function getCategoryData() {
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
            addCategoryRow(result);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}