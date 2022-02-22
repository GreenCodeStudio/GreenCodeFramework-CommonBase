import {ListRenderer} from "../../Core/js/utils/listRenderer";
import {NotificationManager} from "../../Notifications/js/NotificationsManager";

export class NotificationsRenderer extends HTMLElement {
    constructor() {
        super();
        this.listRendered = new ListRenderer(this, this.render.bind(this), NotificationManager.current);
        NotificationManager.onchange(() => this.listRendered.render());
    }

    render(notification, div) {
        if (!div) {
            div = document.create('div');
        }
        div.children.removeAll();
        div.addChild('.message', {text: notification.message})
        div.addChild('button.close span.icon-cancel', {
            onclick() {
                NotificationManager.remove(notification);
            }
        })
        return div;
    }
}

customElements.define('notifications-renderer', NotificationsRenderer);