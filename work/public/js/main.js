'use strict';

{
    const token = document.querySelector('main').dataset.token;
    const input = document.querySelector('input[name="title"]');
    const ul = document.querySelector('ul');

    input.focus();

    // *** チェックボックス処理、削除処理
    ul.addEventListener('click', e => {
        if (e.target.type === 'checkbox') {
            const url = '?action=toggle';
            const options = {
                method: 'POST',
                body: new URLSearchParams({
                    id: e.target.parentNode.dataset.id,
                    token: token,
                }),
            }
            fetch(url, options)
            .then(response => {
                if(!response.ok) {
                    throw new Error('This todo has been deleted!');
                }

                return response.json();
            })
            .then(json => {
                if(json.is_done !== e.target.checked) {
                    alert('This Todo has been updated. UI is being updated.');
                    e.target.checked = json.is_done;
                }
            })
            .catch(err => {
                alert(err.message);
                location.reload();
            });
        }

        if (e.target.classList.contains('delete')) {
            if (!confirm('Are you sure?')) {
                return;
            }
            const url = '?action=delete';
            const options = {
                method: 'POST',
                body: new URLSearchParams({
                    id: e.target.parentNode.dataset.id,
                    token: token,
                }),
            }
            fetch(url, options);

            e.target.parentNode.remove();
        }
    })

    // *** （Todo追加関数）
    function addTodo(id, titleValue) {
        const li = document.createElement('li');
        li.dataset.id = id;
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        const title = document.createElement('span');
        title.textContent = titleValue;
        const deleteSpan = document.createElement('span');
        deleteSpan.textContent = 'x';
        deleteSpan.classList.add('delete');

        li.appendChild(checkbox);
        li.appendChild(title);
        li.appendChild(deleteSpan);

        ul.insertBefore(li, ul.firstChild);
    }

    // *** Todo追加処理
    document.querySelector('form').addEventListener('submit', e => {
        e.preventDefault();

        const title = input.value;

        const url = '?action=add';
        const options = {
            method: 'POST',
            body: new URLSearchParams({
                title: title,
                token: token,
            }),
        }
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            addTodo(json.id, title);
        });

        input.value = '';
        input.focus();
    });

    // *** Purge処理
    const purge = document.querySelector('.purge');
    purge.addEventListener('click', () => {
        if (!confirm('Are you sure?')) {
            return;
        }
        const url = '?action=purge';
            const options = {
                method: 'POST',
                body: new URLSearchParams({
                    token: token,
                }),
            }
            fetch(url, options);

            const lis = document.querySelectorAll('li');
            lis.forEach(li => {
                if (li.children[0].checked) {
                    li.remove();
                }
            });
    });
}
