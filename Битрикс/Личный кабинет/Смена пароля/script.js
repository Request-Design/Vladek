$(document).ready(function () { // CHANGE PASSWORD IN PROFILE

    $('.change-pass-ajax').submit(function (e) {

        $.ajax({
            type: 'POST',
            url: '/ajax/personal/pass/changePass.php',
            data: data,
            dataType: 'html',

            success: function (response) {
                if (response == 1)
                {
                    $('.successtext').text('Пароль был успешно изменён!')
                    $('.alert-warning').css('display', 'none')
                    $('.alert-success').css('display', 'block')

                    setTimeout(function () {
                        $('.alert-success').css('display', 'none')
                    }, 3500)
                }
                else
                {
                    $('.alert-warning').css('display', 'block')
                    $('.errortext').text(response)

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