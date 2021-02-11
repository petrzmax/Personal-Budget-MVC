const deleteModalText = "Czy na pewno chcesz usunąć kategorię \"";
const addCategoryModalTitle = "Dodaj nową kategorię";
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

    $.ajax({
        type: 'POST',
        url: '/settings/getCategoryData',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },

        success: function(result) {
            
            $('#categoryName').val(result.name);

            var label_text = $('#deleteModalText').text(); //Get the text
            $('#deleteModalText').text(deleteModalText + result.name + "\"?"); //Replace and set the text back
            
        },

        error: function(data){
            alert('fail');
        }
    });

    //Show proper modal
    if(buttonType == 'edit') {
        $('#editModal').modal('show');
    }

    if(buttonType == 'delete') {
        $('#deleteModal').modal('show');
    }
    
});

//Delete selected category - activated by button on delete modal
function deleteCategory() {
    $.ajax({
        type: 'POST',
        url: '/settings/deleteCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },

        success: function(result) {
            $('#deleteModal').modal('hide');
            var currentCategoryRow = $("div").find(`[categoryId='${categoryId}'][categoryType='${categoryType}']`).parent();
            currentCategoryRow.slideUp('medium', function() {currentCategoryRow.remove();});
        },

        error: function(data){
            alert('fail');
        }
    });
}

//Add new category - activated by button on edit modal
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
            $('#editModal').modal('hide');

            $('#incomeCategoriesBody').append([
                { newCategoryName: categoryName, newCategoryId: 69, newCategoryType: categoryType }
              ].map(categoryTemplate).join('')); 
            
            //PROTOTYPE
            var currentCategoryRow = $("div").find(`[categoryId='69'][categoryType='${categoryType}']`).parent();
            currentCategoryRow.slideDown('slow');
        },

        error: function(data){
            alert('fail');
        }
    });


    

}

//Add button handler
$(document).on('click', '.addBtn', function () {
    categoryType = $(this).attr('categoryType');
    $('#editModalLabel').text(addCategoryModalTitle); //Set proper modal title

    //Show limit form
    if(categoryType == 'expense') {
        $('#limitForm').show();
    }

    $('#editModal').modal('show');

});