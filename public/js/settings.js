//Edit button handler
$(document).on('click', '.editBtn', function () {

    var categoryId = $(this).parent().attr('categoryId');
    var categoryType = $(this).parent().attr('categoryType');

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