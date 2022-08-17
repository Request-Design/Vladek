$(document).on('click', '.add-to-fav', function (evt) {
    var favorID = $(this).attr('data-item');
    var param = {'favor': favorID}

    $.ajax({
        url: '/ajax/favor.php',
        type: "POST",
        dataType: "html",
        data: param,
        success: function (response) {
            if (response == 1) $(this).addClass('active');
            if (response == 2) $(this).removeClass('active');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ' + errorThrown);
        }
    });
});


/* DELETE FROM FAVOUR */
$(document).on('click', '.remove', function (evt) {

    var favorID = $(this).attr('data-favor');
    var param = {'favordelete': favorID};
    $.ajax({
        url: '/ajax/deleteFavor.php',
        type: "POST",
        dataType: "html",
        data: param,
        success: function (response) {
            if(response == 1) $('.product-'+favorID).remove()
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ' + errorThrown);
        }
    });
});

$('.favorites__search').keyup(function () {

    if($('.available').prop('checked') == true) var available = 1;
    var data = {'INPUT': $(this).val(), 'AVAILABLE': available};
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        $.ajax({
            type: 'POST',
            url: '/ajax/search.php',
            data: data,
            dataType: 'html',
            success: function (response) {
                $('.product-favour').remove();
                $('.favorites__wrapper').append(response);
            },
            error: function (jqXHR, textStatus, errorThrown) { // Ошибка
                console.log('Error: ' + errorThrown);
            }
        });

    }, 500)
    return false;
});