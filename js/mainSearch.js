import {Ajax} from "../../Core/js/ajax";
import {ListRenderer} from "../../Core/js/utils/listRenderer";

export class MainSearch {
    constructor(container) {
        this.container = container;
        this.input = this.container.querySelector('input');
        this.input.oninput = this.oninput.bind(this);
        this.renderer = new ListRenderer(container.querySelector('.list'), this.renderLine.bind(this));
    }

    async oninput(e) {
        const start = new Date();
        const result = await Ajax.Search.searchAll(this.input.value);
        if (this.lastResult && this.lastResult.start > start) return;
        this.lastResult = {start, result};
        this.renderer.list = result;
        this.renderer.render();
    }

    renderLine(item) {
        console.log('f')
       return  document.create('a.item', {href:item.link,text: item.name});
    }
}