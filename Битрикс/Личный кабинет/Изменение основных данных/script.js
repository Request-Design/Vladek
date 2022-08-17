$(document).ready(function () { // CHANGE PERSONAL DATA

    $('.personal-data-change-ajax').submit(function () {
        var that = $(this);
        var data = that.serialize();

        $.ajax({
            type: 'post',
            url: '/ajax/personal/changeData.php',
            data: data,
            dataType: 'html',

            success: function (response) {
                if (response == 1) location.reload();
                else
                {
                    $('.alert-warning-data').css('display', 'block')
                    $('.errortext-data').text(response)

                    setTimeout(function () {
                        $('.alert-warning-data').css('display', 'none')
                    }, 3500)
                }
            },

        });
        return false;

    });
})