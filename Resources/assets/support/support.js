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

var btn = document.querySelector(".offcanvas-link");

btn?.addEventListener("click",
    document.querySelector(".box-hidden")?.scrollIntoView({block: "end"})
);

/** Ответы */
var insert_answer = document.querySelector('#support_message_add_form_insert_asnwer');
var reply_message = document.querySelector('#support_message_add_form_reply_message');
var answers = document.querySelector("#support_message_add_form_reply_answers");
/**
 * Выбор из списка ответов
 */
answers?.addEventListener("change", function()
    {
        /**
         * Отобразить скрыть кнопку
         */
        if(this.value)
        {
            insert_answer.style.display = "inline-block"
        } else
        {
            insert_answer.style.display = "none"
        }
    }
);

/**
 * Добавление варианта ответа в сообщение
 */
insert_answer?.addEventListener("click", function(e)
    {
        e.preventDefault()
        const data_content = answers.options[answers.options.selectedIndex].getAttribute("data-content");
        if(reply_message.value)
        {
            reply_message.value += "\n"
        }

        reply_message.value += data_content
        console.log(reply_message.rows)

        resizeTextAreaHeight(reply_message)
    }
)

/**
 * Измененяет высоту textarea
 * @param textarea
 */
function resizeTextAreaHeight(textarea)
{
    const {style, value} = textarea;

    /**
     * '4' соответствует двум границам по 2 пикселя (верхней и нижней)
     */
    const offset = 4;

    style.height = style.minHeight = 'auto';
    style.minHeight = `${Math.min(textarea.scrollHeight + 4, parseInt(textarea.style.maxHeight))}px`;
    style.height = `${textarea.scrollHeight + offset}px`;
}

/**
 * При редактировании сообщения вручную измненяем высоту textarea
 */
reply_message?.addEventListener('input', () =>
{
    resizeTextAreaHeight(reply_message);
});

/** Сокеты */
executeFunc(function zuxjGRZu()
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

        centrifuge.newSubscription('ticket').on('publication', function(ctx)
        {
            //console.log(ctx.data);

            let identifier = document.getElementById('tiket-' + ctx.data.identifier);

            //console.log(identifier);

            if(identifier)
            {
                /** Меняем ссылку на ответы */
                form.action = '/admin/support/message/add/' + ctx.data.event + '/' + ctx.data.message;

                /** При ответе удаляем из списка тикет */
                document.getElementById(ctx.data.identifier)?.remove();

                let messages = document.getElementById('messages-' + ctx.data.identifier);

                if(messages)
                {
                    messages.innerHTML += ctx.data.support;
                    messages.scrollIntoView({behavior: "smooth", block: "end"});

                    reloadLazy();
                }
            }

        }).subscribe();

        centrifuge.connect();

        /** Делаем отправку формы на AJAX */
        const form = document.forms.support_message_add_form;

        /* Блокируем событие отправки формы */
        form.addEventListener('submit', function(event)
        {
            event.preventDefault();
            submitTiketForm(this);
            return false;
        });

        /** При скрытии - закрываем соединение */

        const offcanvas = document.getElementById('offcanvas');

        offcanvas.addEventListener('hide.bs.offcanvas', event =>
        {
            centrifuge.disconnect();
        });

        return true;
    }

    return false;

});


async function submitTiketForm(forma)
{
    const data = new FormData(forma);

    let indicator = forma.querySelector('.spinner-border');

    if(indicator)
    {
        btn = indicator.closest('button');
        indicator.classList.remove('d-none');
        btn.disabled = true;
        btn.type = 'button';
    }

    const frm = document.forms[forma.name];

    await fetch(frm.action, {
        method: frm.method, // *GET, POST, PUT, DELETE, etc.
        //mode: 'same-origin', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: data // body data type must match "Content-Type" header
    })

        .then((response) =>
        {
            let reply = document.getElementById(forma.name + '_reply_message');
            reply.value = '';
            reply.focus()
            closeProgress();
            btn.type = 'submit';
        });


    return false;


    // .catch((error) => {
    //     console.error('Error:', error);
    // }); // parses JSON response into native JavaScript objects
}
