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
        url: '/settings/get-limit',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },

        success: function(result) {
            
            $('#categoryName').val(result.name);
        },

        error: function(data){
            alert('fail');
        }
    });

    $('#editModal').modal('show');
});