var deleteModalText = "Czy na pewno chcesz usunąć kategorię ";
var categoryId;
var categoryType;
var buttonType;

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
            $('#deleteModalText').text(deleteModalText + result.name + "?"); //Replace and set the text back
            
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
            currentCategoryRow.hide('slow', function() {currentCategoryRow.remove();});
        },

        error: function(data){
            alert('fail');
        }
    });
}