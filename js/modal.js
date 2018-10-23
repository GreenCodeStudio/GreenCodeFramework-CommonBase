export function modal(text, type = 'info', buttons = [{text: 'ok', value: true}]) {
    console.log(text);
    let modalContainer=document.createElement('div');
    modalContainer.className='modalContainer';
    document.body.appendChild(modalContainer);

    let modal=document.createElement('div');
    modal.className='modal';
    modal.dataset.type=type;
    modalContainer.appendChild(modal);

    let textElem=document.createElement('div');
    textElem.textContent=text;
    modal.appendChild(textElem);
}