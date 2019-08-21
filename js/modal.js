export function modal(text, type = 'info', buttons = [{text: 'ok', value: true}]) {
    new Promise((resolve, reject) => {
        console.log(text);
        let modalContainer = document.createElement('div');
        modalContainer.className = 'modalContainer';
        document.body.appendChild(modalContainer);

        let modal = document.createElement('div');
        modal.className = 'modal';
        modal.dataset.type = type;
        modalContainer.appendChild(modal);

        let textElem = document.createElement('div');
        textElem.className = 'modal-text';
        textElem.textContent = text;
        modal.appendChild(textElem);

        for (let button of buttons) {
            let buttonElem = document.create('button.modal-button');
            buttonElem.textContent = button.text;
            buttonElem.onclick = () => {
                modalContainer.classList.add('closing');
                setTimeout(() => modalContainer.remove(), 1000);
                resolve(button.value);
            };
            modal.appendChild(buttonElem);
            buttonElem.focus();
        }
    });
}