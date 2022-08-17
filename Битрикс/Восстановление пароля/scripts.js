$(document).ready(function () { // CHANGE PASS FORM

    $('.changepassword-ajax').submit(function (e) {
        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '/ajax/changePass.php',
            data: data,
            dataType: 'html',
            success: function (response) {
                if (response == 1) location.reload;
                else
                {
                    $('.errortext').text(response)
                    $('.alert-warning').css('display', 'block')

                    setTimeout(function () {
                        $('.alert-warning').css('display', 'none')
                    }, 3500)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // Ошибка
                console.log('Error: ' + errorThrown);
            }
        });
        return false;

    });

})

$(document).ready(function () { // FORGOT PASS

    $('.forgot-pass-ajax').submit(function (e) {
        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '/ajax/forgotPass.php',
            data: data,
            dataType: 'html',
            success: function (response) {
                if(response == 1)
                {
                    $('.alert-warning').css('display', 'none')
                    $('.alert-success').css('display', 'block')
                    $('.successtext').text('На указанную почту отправлена ссылка для восстановления. Проверьте папку спам или дождитесь письма' +
                        ', каждый запрос генерирует новый уникальный код')
                }
                else
                {
                    $('.errortext').text(response)
                    $('.alert-warning-forgot').css('display', 'block')

                    setTimeout(function () {
                        $('.alert-warning-forgot').css('display', 'none')
                    }, 3500)
                }


            },
            error: function (jqXHR, textStatus, errorThrown) { // Ошибка
                console.log('Error: ' + errorThrown);
            }
        });
        return false;

    });

})