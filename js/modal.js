export function modal(text, type = 'info', buttons = [{text: 'ok', value: true}]) {
    return new Promise((resolve, reject) => {
        console.log(text);
        let modalContainer=document.body.add('div.modalContainer');

        let modal = modalContainer.add('div.modal');
        modal.dataset.type = type;

        let textElem = modal.add('div.modal-text');
        textElem.textContent = text;

        for (let button of buttons) {
            let buttonElem = modal.add('button.modal-button');
            buttonElem.textContent = button.text;
            buttonElem.onclick = () => {
                modalContainer.classList.add('closing');
                setTimeout(() => modalContainer.remove(), 1000);
                resolve(button.value);
            };
            buttonElem.focus();
        }
    });
}