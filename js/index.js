import {Ajax} from "../../Core/js/ajax";
import {TableManager} from "../../Core/js/table";
import {pageManager} from "../../Core/js/pageManager";
import {MainSearch} from "./mainSearch";

document.querySelectorAll('.logoutMyselfBtn').forEach(b => b.onclick = async () => {
    await Ajax.Authorization.logout();
    document.location.reload()
});
const login = {
    get currentToken() {
        let groups = /login=([0-9a-fA-F]*)/.exec(document.cookie);
        if (groups)
            return groups[1];
        else
            return null;
    },
    check() {
        if (this.startToken !== this.currentToken)
            document.location.reload();
    }
};
login.startToken = login.currentToken;

setInterval(login.check.bind(login), 1000);

TableManager.prototype.calcSize = function () {
    let rowHeight = 40;
    let totalHeight = window.innerHeight - this.table.offsetTopFull - 40;
    this.limit = Math.floor(totalHeight / rowHeight);
    if (this.limit < 1 || !isFinite((this.limit)))
        this.limit = 1;
};
addEventListener('resize', () => {
    document.querySelectorAll('.dataTable').forEach(table => {
        if (!table.datatable) return;
        let limit = table.datatable.limit;
        table.datatable.calcSize();
        if (table.datatable.limit !== limit)
            table.datatable.refresh();
    })
});
document.querySelectorAll('.hamburgerMenu').forEach(x => x.onclick = () => document.body.classList.toggle('hamburgerMenu-opened'));
document.querySelectorAll('.mainSearch').forEach(x=>new MainSearch(x));
addEventListener('click', e => {
    if (e.target.findParent(x => x.matches('body>aside') || x.matches('body>header'))) {

    } else {
        document.body.classList.remove('hamburgerMenu-opened');
    }
});
pageManager.onLoad(() => document.body.classList.remove('hamburgerMenu-opened'));
let subscribeNotificationsBtn = document.querySelector('.subscribe-notifications');
if (subscribeNotificationsBtn)
    subscribeNotificationsBtn.onclick = async e => {
        let serviceWorkerRegistration = await window.swRegistratonPromise;
        let options = {
            userVisibleOnly: true,
            applicationServerKey: 'BOpw8ocFV02co1cg8h-WZvfiwys3CemOyGT2cDHsPezM5yCFjrQrQ1Dz8vlihX-H2_THV9169oS6Y03QKJAtBnU'
        };
        let subscription = await serviceWorkerRegistration.pushManager.getSubscription();
        if (subscription === null) {
            subscription = await serviceWorkerRegistration.pushManager.subscribe(options);
        }
        Ajax.Notifications.subscribePush(subscription);
    };