import {Ajax} from "../../Core/js/ajax";

export class MainSearch{
    constructor(container){
        this.container=container;
        this.input=this.container.querySelector('input');
        this.input.oninput=this.oninput.bind(this);
    }
    oninput(e){
        Ajax.Search.main(this.input.value);
    }
}