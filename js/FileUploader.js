import {Ajax} from "../../Core/js/ajax";
import {ActiveListRenderer} from "active-list-renderer"
export default class FileUploader extends HTMLElement {
    constructor() {
        super();
        this.maxFiles = null;
        this.files = [];
        this.activeElementList = new ActiveListRenderer(this.addChild('div'),null, this.files);
        this.activeElementList.renderFunction = (x, old) => {
            console.log(x);
            if (old) return old;
            else if (x.status === 'pending')
                return document.create('div.pendingFile', {text: 'pending'})
            else
                return document.create('div.uploadedFile', {
                    children: [{text: x.name || 'Bez nazwy'}, {text: 'Typ: ' + x.mime}, {text: 'Rozmiar: ' + this.bytesToHumanReadable(x.size)}, {
                        tagName: 'div',
                        className: 'button',
                        text: 'usuń',
                        onclick: () => {
                            let index = this.files.indexOf(x);
                            if (index >= 0)
                                this.files.splice(index, 1);
                            this.activeElementList.render();
                            this.dispatchEvent(new Event('change', {"bubbles": true, "cancelable": false}));
                        }
                    }]
                })
        };
        this.addButton = this.addChild('.button', {text: 'Dodaj'});
        this.addButton.onclick = () => this.openFileDialog();
    }

    openFileDialog() {
        const input = document.create('input', {type: 'file'});
        input.onchange = (e) => {
            Array.from(input.files).map(f => this.uploadFile(f));
        }
        input.click();
    }

    async uploadFile(file) {
        let placeholder = {'status': 'pending'}
        this.files.push(placeholder);
        this.refreshAddButtonVisibility();
        this.activeElementList.render();
        let data = await Ajax.File.upload(file, {});
        this.files.splice(this.files.indexOf(placeholder), 1, data);
        this.activeElementList.render();
        this.dispatchEvent(new Event('change', {"bubbles": true, "cancelable": false}));
    }

    get value() {
        return this.files;
    }

    set value(value) {
        this.files = value;
        this.activeElementList.list = value;
        this.activeElementList.render();
    }

    refreshAddButtonVisibility() {
        this.addButton.style.display = (this.maxFiles === null || this.files.length < this.maxFiles) ? 'block' : 'none;'
    }

    bytesToHumanReadable(bytes) {
        if (bytes > 1024 ** 3)
            return this.numberHumanRund(bytes / 1024 ** 3) + 'GB';
        else if (bytes > 1024 ** 2)
            return this.numberHumanRund(bytes / 1024 ** 2) + 'MB';
        else if (bytes > 1024)
            return this.numberHumanRund(bytes / 1024) + 'kB';
        else
            return bytes + 'B';
    }

    numberHumanRund(num) {
        if (num > 100) return num.toString();
        else if (num > 10) return num.toFixed(1);
        else return num.toFixed(2);
    }
}
customElements.define('file-uploader', FileUploader);