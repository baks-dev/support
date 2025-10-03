/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

/** Обновить содержимое виджета */
async function updateWidgetList()
{

    await fetch('/admin/supports', {
        method: "GET",
        cache: "no-cache",
        credentials: "same-origin",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        }, redirect: "follow",
        referrerPolicy: "no-referrer",
    })

        .then((response) =>
        {

            if(response.status !== 200)
            {
                return false;
            }

            return response.text();
        }).then((data) =>
        {

            if(data)
            {

                var myCollapse = document.getElementById("collapseChat");

                if(myCollapse === null)
                {
                    console.log('Элемент с идентификатором id="collapseChat" не найден');
                    return;
                }

                let bsCollapse = bootstrap.Collapse.getOrCreateInstance(myCollapse);

                myCollapse.innerHTML = data;

                /** Обновляем Preload */
                let lazy = document.createElement("script");
                lazy.src = "/assets/" + $version + "/js/lazyload.min.js";
                document.head.appendChild(lazy);
            }
        });

    return false;

}

/** Сокеты support widget */
executeFunc(function quxjTRYv()
{
    if(typeof Centrifuge === 'function')
    {

        const dsn = window.centrifugo_dsn; // Получаем значение dsn
        const token = window.centrifugo_token; // Получаем значение token

        if(!dsn || !token)
        {
            return false;
        }

        centrifuge = new Centrifuge("wss://" + dsn + "/connection/websocket",
            {
                token: token,
                getToken: function(ctx)
                {
                    return getToken('/centrifugo/credentials/user', ctx);
                },
                debug: false,
            });

        // "пользовательский" channel
        const user_channel = window.current_profile + '_ticket';

        const subscriptions = [user_channel, 'ticket'];

        for(const subscription of subscriptions)
        {

            centrifuge.newSubscription(subscription).on('publication', function(ctx)
            {
                if(ctx.data.status === 'open')
                {
                    // Отобразить индикатор
                    document.getElementById('widget_notification').classList.remove('d-none');

                    // Обновить список и создать toast-сообщение
                    // updateWidgetList();

                    // Создать Toast сообщение
                    const header = 'Сообщение поддержки';
                    const warningMessageHandler = '{ "type":"notice" , ' +
                        '"header":"' + header + '"  , ' +
                        '"message" : "Добавлено новое сообщение." }';

                    createToast(JSON.parse(warningMessageHandler));

                }

            }).subscribe();
        }

        centrifuge.connect();


        /** При скрытии - закрываем соединение */

        const collapse = document.querySelector('.collapse-link-content');

        collapse.addEventListener('hide.bs.collapse', event =>
        {
            centrifuge.disconnect();
        });

        return true;

    }

    return false;

});

// Скрыть индикатор
document.querySelector('.collapse-link-content').addEventListener('click', function()
{
    document.getElementById('widget_notification').classList.add('d-none');
})