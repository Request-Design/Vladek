let timeOut;
$('.global-search-ajax').keyup(function () {
    var data = {'INPUT': $(this).val()};

    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        $.ajax({
            type: 'POST',
            url: '/ajax/globalSearch.php',
            data: data,
            dataType: 'html',
            success: function (response) {
                $('.global-search-content-ajax').remove();
                $('.header__search-block-lists').append(response);
            },
            error: function (jqXHR, textStatus, errorThrown) { // Ошибка
                console.log('Error: ' + errorThrown);
            }
        });
    }, 500)

    return false;
});