    $(document).on('click', '.plus', function(evt) {
        evt.preventDefault();
        var inp = $(this).siblings('input')
        var val = inp.val();
        var max = inp.attr('max-quantity')
        $(this).siblings('.minus').removeClass('disabled-btn')

        if(Number(max) > Number(val)) inp.val(++val)
        else alert('Вы добавили максимум товаров')
    });
    $(document).on('click', '.minus', function(evt) {
        evt.preventDefault();
        var inp = $(this).siblings('input')
        var val = inp.val();
        if (val > 1) inp.val(--val);
        else $(this).addClass('disabled-btn')
    });
    

    $(document).on('click', '.plus-ajax', function (evt) { //PLUS
        //array data
        var inp     = $(this).siblings('input') //Инпут, в котором все атрибуты и кол-во товара
        var id      = inp.attr('data-quant'); //ID товара в ИБ
        var count   = inp.val(); //Кол-во товара в инпуте, актуальное
        var uniqID  = inp.attr('data-pos'); //ID товара в корзине
        var address = inp.attr('data-address'); //Страница, с которой получаем данные
        //array data
        $(this).attr('first-quant', count); //Обновляем первоначальное кол-во товара
        var result = {'id': id, 'count': count, 'type': 'plus', 'address': address, 'uid': uniqID, 'countExists': inp.attr('data-firstquant')}; //Массив с данными для сервера

        clearTimeout(timeOut);
        timeOut = setTimeout(function() { //Устанавливаем задержку для отправки ajax
            $.ajax({
                url: '/ajax/price/changePrice.php',
                type: "POST",
                dataType: "html",
                data: result,
                success: function (response) {
                    if (address == 'catalog') $('.price-' + id).html(response); //Устанавливаем цену конкретного товара
                    else if (address == 'basket') {
                        var res = JSON.parse(response); //Получаем массив с данными с сервера
                        $('.price-' + id).html(res.product) //Устанавливаем цену конкретного товара
                        $('.price-all').html(res.basket); //Устанавливаем общую цену корзины
                        $('.count-products').text(res.countCart); //Устанавливаем общее кол-во товара в корзине
                        $('.count-cart-ajax').text(res.countCart); //Устанавливаем общее кол-во товара в хедере
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                }
            });
        }, 500 )
        return false
    });

    $(document).on('click', '.minus-ajax', function (evt) {  //MINUS
        //array data
        var inp     = $(this).siblings('input'); //Инпут, в котором все атрибуты и кол-во товара
        var id      = inp.attr('data-quant'); //ID товара в ИБ
        var count   = inp.val(); //Кол-во товара в инпуте, актуальное
        var uniqID  = inp.attr('data-pos'); //ID товара в корзине
        var address = inp.attr('data-address'); //Страница, с которой получаем данные
        //array data
        $(this).attr('first-quant', count); //Обновляем первоначальное кол-во товара
        var result = {'id': id, 'count': count, 'type': 'minus', 'address': address, 'uid': uniqID}; //Массив с данными для сервера
        clearTimeout(timeOut);
        timeOut = setTimeout(function() { //Устанавливаем задержку для отправки ajax
            $.ajax({
                url: '/ajax/price/changePrice.php',
                type: "POST",
                dataType: "html",
                data: result,
                success: function (response) {
                    if (address == 'catalog') $('.price-' + id).html(response); //Устанавливаем цену конкретного товара
                    else if (address == 'basket') {
                        var res = JSON.parse(response); //Получаем массив с данными с сервера
                        $('.price-' + id).html(res.product) //Устанавливаем цену конкретного товара
                        $('.price-all').html(res.basket); //Устанавливаем общую цену корзины
                        $('.count-products').text(res.countCart); //Устанавливаем общее кол-во товара в корзине
                        $('.count-cart-ajax').text(res.countCart); //Устанавливаем общее кол-во товара в хедере
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                }
            });
        }, 500 )
        return false
    });

    $(".quant-data").change(function () {  //CHANGE
        //array data
        var count   = $(this).val(); //Кол-во товара в инпуте, актуальное
        var id      = $(this).attr('data-quant'); //ID товара в ИБ
        var address = $(this).attr('data-address'); //Страница, с которой получаем данные
        var uniqID  = $(this).attr('data-pos'); //ID товара в корзине
        var max     = $(this).attr('max-quantity'); //Макс. кол-во товара на складе
        var firstQuant = $(this).attr('first-quant'); //Получаем изначальное кол-во конкретного товара в корзине
        //array data
        if(count <= 0) //Возвращаем единицу, если пользователь пытается указать число равное или меньше нулю(я)
        {
            count = 1;
            $(this).val(1);
        }
        $(this).siblings('.minus').removeClass('disabled-btn'); //Включаем кнопку убавления кол-ва товара

        if(Number(max) < Number(count))  //Выводим ошибку, когда пользователь пробует добавить товара больше, чем есть на складе
        {
            $(this).val(firstQuant);
            alert('Вы добавили максимум товаров');
        }
        else {
            $(this).attr('first-quant', count) //Обновляем первоначальное кол-во товара
            var result = {'id': id, 'count': count, 'type': 'change', 'address': address, 'uid': uniqID}; //Массив с данными для сервера

            $.ajax({
                url: '/ajax/price/changePrice.php',
                type: "POST",
                dataType: "html",
                data: result,
                success: function (response) {
                    if (address == 'catalog') $('.price-' + id).html(response); //Устанавливаем цену конкретного товара
                    else if (address == 'basket') {
                        var res = JSON.parse(response); //Получаем массив с данными с сервера
                        $('.price-' + id).html(res.product) //Устанавливаем цену конкретного товара
                        $('.price-all').html(res.basket); //Устанавливаем общую цену корзины
                        $('.count-products').text(res.countCart); //Устанавливаем общее кол-во товара в корзине
                        $('.count-cart-ajax').text(res.countCart); //Устанавливаем общее кол-во товара в хедере
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                }
            });
        }
        return false;

    });


