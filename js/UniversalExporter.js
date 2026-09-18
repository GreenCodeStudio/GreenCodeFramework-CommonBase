import {DatasourceAjax} from "../../Core/js/datasourceAjax";
import {Ajax} from "../../Core/js/ajax";
import {create} from "fast-creator";

export class UniversalExporter {
    static generateObjectsListsExports(objectsList, url) {
        return () => {
            if (objectsList.datasource instanceof DatasourceAjax) {

                return ['pdf', 'xlsx', 'xml'].map(type => {
                    return {
                        name: type, action: async () => {
                            const form = create('form', {style: 'display:none'});
                            document.body.appendChild(form);
                            const options=objectsList.datasource.generateOptions(objectsList);
                            form.append(create('input', {type: 'hidden', name: 'options', value: JSON.stringify(options)}));
                            form.append(create('input', {type: 'hidden', name: 'type', value: type}));
                            form.method = 'POST';
                            form.action = url;
                            form.submit()
                        }
                    }
                });
            } else {
                return null
            }
        }
    }
}
