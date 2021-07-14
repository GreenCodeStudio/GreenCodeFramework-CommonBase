import {Ajax} from "../../Core/js/ajax";
import {ListRenderer} from "../../Core/js/utils/listRenderer";

export class MainSearch {
    constructor(container) {
        this.container = container;
        this.input = this.container.querySelector('input');
        this.input.oninput = this.oninput.bind(this);
        this.input.onkeydown = this.onkeydown.bind(this);
        this.input.onblur = this.onblur.bind(this);
        this.renderer = new ListRenderer(container.querySelector('.list'), this.renderLine.bind(this));
    }

    async oninput(e) {
        const start = new Date();
        const result = await Ajax.Search.searchAll(this.input.value);
        if (this.lastResult && this.lastResult.start > start) return;
        this.lastResult = {start, result};
        const more = {link: `/Search/index/${this.input.value}`, name: 'Wyświetl wszystko'};
        this.renderer.list = [...result, more];
        this.renderer.render();
    }

    onkeydown(e) {
        if (e.code === 'Enter') {
            this.getSelected().click();
        } else if (e.code === 'ArrowDown') {
            let current = this.getSelected();
            let next = current.nextElementSibling ? current.nextElementSibling : this.container.querySelector('a.item');
            this.setSelected(next);
        } else if (e.code === 'ArrowUp') {
            let current = this.getSelected();
            let next = current.previousElementSibling ? current.previousElementSibling : this.container.querySelector('a.item:last-of-type');
            this.setSelected(next);
        }
    }

    onblur() {
        this.deselectAll();
    }

    deselectAll() {

        this.container.querySelectorAll('a.item.active').forEach(x => x.classList.remove('active'));
    }

    renderLine(item) {
        const line = document.create('a.item', {href: item.link, text: item.name});
        line.onmousemove = () => this.setSelected(line);
        return line;
    }

    getSelected() {
        let selected = this.container.querySelector('a.item.active');
        if (!selected) {
            selected = this.container.querySelector('a.item:last-of-type')
        }
        return selected;
    }

    setSelected(item) {
        this.deselectAll();
        item.classList.add('active');
    }
}