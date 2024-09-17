import {ActiveListRenderer} from "active-list-renderer";
import {create} from "fast-creator";

export class SelectMultiple extends HTMLElement {
    _options = [];
    _value = [];

    constructor() {
        super();
        this.list = document.createElement('ul');
        this.renderer = new ActiveListRenderer(this.list, (id, rendered) => {
            if (!rendered) {
                rendered = document.createElement('li');
                const item = this._options.find(x => x.id == id);
                rendered.textContent = item.title;
            }
            return rendered;
        }, this._value, x => x, {useWeakMap: false});
        this.appendChild(this.list);
        this.select = create('select', {
            onchange: (callback) => {
                this._value.push(+this.select.value)
                this.renderer.render();
                this.select.value = null;
            }
        });
        this.appendChild(this.select);
        this.optionsRenderer = new ActiveListRenderer(this.select, (item, rendered) => {
            if (!rendered) {
                rendered = document.createElement('option');
            }
            rendered.textContent = item.title;
            rendered.value = item.id
            rendered.disabled = this._value.includes(item.id);
            return rendered;
        });

    }

    set options(value) {
        this._options = value;
        this.optionsRenderer.list = value;
        this.optionsRenderer.render();
    }

    get options() {
        return this._options;
    }

    get name() {
        return this.getAttribute('name');
    }
    get value() {
        return this._value;
    }
    set value(value) {
        if(!value)
            value=[];
        this._value .splice(0, this._value.length, ...value);
        this.renderer.render();
    }
}

customElements.define('select-multiple', SelectMultiple);
