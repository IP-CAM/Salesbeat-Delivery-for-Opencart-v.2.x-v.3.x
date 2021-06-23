SalesbeatCatalogDelivery = {
    init: function(params) {
        this.params = params;

        this.elementBlock = document.querySelector('#sb-cart-widget');
        this.elementResultBlock = document.querySelector('#sb-cart-widget-result');

        if (document.querySelector('.simplecheckout')) {
            this.shippingBlock = document.querySelector('label[for="salesbeat.salesbeat"]');
        } else {
            this.shippingBlock = this.elementBlock.closest('.radio');
        }

        this.shippingMethodInput = this.shippingBlock.querySelector('input[name="shipping_method"]');

        if (this.elementBlock !== null)
            this.loadWidget();
    },
    reshow: function(params) {
        const me = this;

        const elementResultBlock = document.querySelector('#sb-cart-widget-result');
        const button = elementResultBlock.querySelector('.sb-reshow-cart-widget');
        button.addEventListener('click', function (e) {
            e.preventDefault();
            me.init(params);
        });
    },
    loadWidget: function () {
        const me = this;

        SB.init_cart({
            token: this.params.token || '',
            city_code: this.params.city_code || '',
            products: this.params.products || [],
            callback: function (data) {
                data['delivery_id'] = me.params.name;

                me.checkedMethodDelivery();
                me.requestWrapper(me.params.url, {method: 'POST', data: data})
                    .then(res => me.accessOrder(true));
                me.callbackWidget(data);
            }
        });

        this.clearResultBlock();
    },
    requestWrapper(url, options) {
        return new Promise((resolve, reject) => {
            let xhr = new XMLHttpRequest();
            xhr.open(options.method || 'GET', url, false);
            xhr.onreadystatechange = function () {
                if (xhr.status !== 200)
                    reject('Ошибка сервера: ' + this.status);
            };
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.onload = () => {
                resolve('response' in xhr ? xhr.response : xhr.responseText);
            };
            xhr.onerror = (xhr, err) => {
                reject(err);
            };
            xhr.send(JSON.stringify(options.data));
        });
    },
    callbackWidget: function (data) {
        const me = this;
        const methodName = data['delivery_method_name'] || 'Не известно';

        let address = '';
        if (data['pvz_address']) {
            address = 'Самовывоз: ' + data['pvz_address']
        } else {
            address = 'Адрес: ';

            if (data['street']) address += 'ул. ' + data['street'];
            if (data['house']) address += ', д. ' + data['house'];
            if (data['house_block']) address += ' корпус ' + data['house_block'];
            if (data['flat']) address += ', кв. ' + data['flat'];
        }

        let deliveryDays = '';
        if (data['delivery_days']) {
            const dataDeliveryDays = parseInt(data['delivery_days']);

            if (dataDeliveryDays === 0) {
                deliveryDays = 'Сегодня';
            } else if (dataDeliveryDays === 1) {
                deliveryDays = 'Завтра';
            } else {
                deliveryDays = this.suffixToNumber(data['delivery_days'], ['день', 'дня', 'дней']);
            }
        } else {
            deliveryDays = 'Не известно';
        }

        let deliveryPrice = '';
        if (data['delivery_price']) {
            deliveryPrice = data['delivery_price'] > 0 ?
                this.numberWithCommas(data['delivery_price']) + ' руб.' :
                'Бесплатно';
        } else {
            deliveryPrice = 'Не известно';
        }

        const comment = data['comment'] ? '<p> Комментарий: ' + data['comment'] + '</p>' : '';
        this.elementResultBlock.innerHTML += ('<p><span class="salesbeat-summary-label">Способ доставки:</span> ' + methodName + '</p>'
            + '<p><span class="salesbeat-summary-label">Стоимость доставки:</span> ' + deliveryPrice + '</p>'
            + '<p><span class="salesbeat-summary-label">Срок доставки:</span> ' + deliveryDays + '</p>'
            + '<p>' + address + '</p>' + comment
            + '<p><a href="" class="sb-reshow-cart-widget">Изменить данные доставки</a></p>');

        const button = this.elementResultBlock.querySelector('.sb-reshow-cart-widget');
        button.addEventListener('click', function (e) {
            e.preventDefault();
            me.reshowCardWidget();
        });
    },
    reshowCardWidget: function () {
        SB.reinit_cart(true);
        this.clearResultBlock();
    },
    clearResultBlock: function () {
        this.elementResultBlock.innerHTML = '';
        this.accessOrder(false);
    },
    checkedMethodDelivery: function () {
        this.shippingMethodInput.checked = false;
        this.shippingMethodInput.click();
    },
    accessOrder: function (action) {
        let timerId = setTimeout(() => {
            const btnSelectors = '#button-confirm, #simplecheckout_button_confirm, [data-payment-button]';
            let elements = document.querySelectorAll(btnSelectors);
            if (!elements.length) return;

            clearTimeout(timerId);

            for (let i in elements) {
                if (!elements.hasOwnProperty(i)) continue;

                if (action) {
                    elements[i].removeAttribute('disabled');
                } else {
                    elements[i].setAttribute('disabled', 'true');
                }
            }
        }, 500);
    },
    suffixToNumber: function (number, suffix) {
        let cases = [2, 0, 1, 1, 1, 2];
        return number + ' ' + suffix[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
    },
    numberWithCommas: function (string) {
        return string.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
};