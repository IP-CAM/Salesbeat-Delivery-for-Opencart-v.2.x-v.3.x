SalesbeatAdminDelivery = {
    init: function(params) {
        this.params = params;

        this.elementBlock = document.querySelector('#sb-cart-widget');
        this.elementResultBlock = document.querySelector('#sb-cart-widget-result');

        if (this.elementBlock !== null)
            this.loadWidget();
    },
    loadWidget: function () {
        const me = this;

        SB.init_cart({
            token: this.params.token || '',
            city_code: this.params.city_code || '',
            products: this.params.products || [],
            callback: function (data) {
                me.requestWrapper(me.params.url, {method: 'POST', data: data});
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
            + '<p><a href="" class="sb-reshow-cart-widget">Изменить данные доставки</a></p>'
            + '<p><a href="" class="sb-clear-data">Отменить действие</a></p>');

        const button = this.elementResultBlock.querySelector('.sb-reshow-cart-widget');
        button.addEventListener('click', function (e) {
            e.preventDefault();
            me.reshowCardWidget();
        });

        const buttonClear = this.elementResultBlock.querySelector('.sb-clear-data');
        buttonClear.addEventListener('click', function(e) {
            e.preventDefault();
            me.clearData();
        });
    },
    reshowCardWidget: function () {
        SB.reinit_cart(true);
        this.clearResultBlock();
    },
    clearData: function() {
        this.requestWrapper(this.params.url, {method: 'POST', data: ''});
        this.reshowCardWidget();
    },
    clearResultBlock: function () {
        this.elementResultBlock.innerHTML = '';
    },
    suffixToNumber: function (number, suffix) {
        let cases = [2, 0, 1, 1, 1, 2];
        return number + ' ' + suffix[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
    },
    numberWithCommas: function (string) {
        return string.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },

    // SyncPaySystem
    initSyncSystem: function(params) {
        this.params = params;

        const me = this;

        const button = document.querySelector('[data-action="sync_pay_systems"]');
        button.addEventListener('click', function (e) {
            e.preventDefault();

            me.syncPaySystem();
        });
    },
    syncPaySystem: function()
    {
        const me = this;

        this.resultBlock = document.querySelector('[data-result="sync_pay_systems"]');
        this.hiddenInput = document.querySelector('[data-input="pay_systems_last_sync"]');

        this.resultBlock.innerHTML = this.params.message.last_sync;

        const promise = this.requestWrapper(this.params.url, {method: 'POST', data: {action: 'sync_pay_systems'}});
        promise.then(function(data) {
            me.resultPaySystem(JSON.parse(data));
        }).catch(function(data) {
            console.log(data);
        });
    },
    resultPaySystem: function(data) {
        if (data.status === 'success') {
            this.hiddenInput.value = data.time;

            alert(this.params.message.success);
            this.resultBlock.innerHTML = data.message;
        } else {
            alert(this.params.message.error);
            this.resultBlock.innerHTML = data.message;
        }
    },

    // Send Order
    initOrders: function(params) {
        this.params = params;

        this.actionSendOrder();
    },
    actionSendOrder: function () {
        const me = this;

        const buttons = document.querySelectorAll('[data-send-order]');
        for (let button of buttons) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const orderId = button.getAttribute('data-order-id');
                me.sendOrder(orderId);
            });
        }
    },
    sendOrder: function(orderId)
    {
        const me = this;

        const promise = this.requestWrapper(this.params.url, {method: 'POST', data: {order_id: orderId}});
        promise.then(function(data) {
            let result = JSON.parse(data);
            result['order_id'] = orderId;

            me.resultOrder(result);
        }).catch(function(data) {
            console.log(data);
        });
    },
    resultOrder: function(result) {
        if (result.status === 'success') {
            const order = document.querySelector('[data-order][data-order-id="' + result.order_id+ '"]');
            const trackCode = order.querySelector('[data-track-code]');
            trackCode.innerHTML = result.data.track_code;

            alert(result.message);
        } else {
            let message = 'Ошибка:\n';
            if (result.error_list) {
                for (let err of result.error_list)
                    message += err.message + '\n'
            } else {
                message += 'Нет информации об ошибке'
            }

            alert(message);
        }
    }
};