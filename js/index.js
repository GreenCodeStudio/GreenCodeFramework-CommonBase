import {Ajax} from "../../Core/js/ajax";
import {TableManager} from "../../Core/js/table";
import {pageManager} from "../../Core/js/pageManager";
import {MainSearch} from "./mainSearch";
import "./FileUploader";
import {NotificationsRenderer} from "./NotificationsRenderer";
import {modal} from "../../Core/js/modal";

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

addEventListener('focus', login.check.bind(login));

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
document.querySelectorAll('.mainSearch').forEach(x => new MainSearch(x));
addEventListener('click', e => {
    if (e.target.findParent(x => x.matches('body>aside') || x.matches('body>header'))) {

    } else {
        document.body.classList.remove('hamburgerMenu-opened');
    }
});
pageManager.onLoad(() => document.body.classList.remove('hamburgerMenu-opened'));
let subscribeNotificationsBtn = document.querySelector('.subscribe-notifications');
if (subscribeNotificationsBtn) {
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
}
if ('serviceWorker' in navigator && !window.DEBUG) {
    window.swRegistratonPromise = navigator.serviceWorker.register('/dist/serviceWorker.js', {scope: '/'});
    window.swRegistratonPromise.catch(() => {
    });
}
window.addEventListener('beforeinstallprompt', (e) => {
    let btn = document.create('button.installPWA span.icon-install');
    document.querySelector('body > header')?.insertBefore(btn, document.querySelector('body > header .tasks'));
    btn.onclick = () => {
        e.prompt();
        btn.remove();
    }
});

let letters = {};
window.addEventListener('keydown', e => {
    if (e.key == 'e' && e.ctrlKey) {
        if (!document.querySelector('.mainSearch input:focus')) {
            document.querySelector('.mainSearch input').focus();
            e.preventDefault();
        }
    }
    if (e.key == 'ArrowUp' && e.altKey) {
        document.querySelector('.breadcrumb ul li:last-child').previousElementSibling?.querySelector('a').click();
    }
    if (e.key == 'F6') {
        const groups = [document.querySelector('header'), document.querySelector('aside'), document.querySelector('.breadcrumb')]
        const groupsItems = [document.querySelector('header .mainSearch input'), document.querySelector('aside a'), document.querySelector('.breadcrumb a')]
        let index = groups.indexOf(groups.find(x => x.matches(':focus-within')));
        if ((index + 1) < groups.length) {
            groupsItems[(index + 1) % groups.length].focus();
            e.preventDefault();
        } else {
            groupsItems[0].focus()
        }
    }
    if (e.key == 'Alt') {
        for (let button of document.querySelectorAll('button, .button')) {
            if(button.querySelector('.actionLetter')) continue;
            let text = button.textContent;
            for (let letter of text) {
                letter = letter.toLowerCase();
                if (/[a-z]/.test(letter) && !letters[letter]) {
                    letters[letter] = {button};

                    let text = Array.from(button.childNodes).find(x => x instanceof Text && x.textContent.toLowerCase().includes(letter))
                    if (text) {
                        let index = text.textContent.toLowerCase().indexOf(letter)
                        text.parentNode.insertBefore(document.createTextNode(text.textContent.substr(0, index)), text)
                        letters[letter].actionLetter = text.parentNode.insertBefore(document.create('span.actionLetter', {
                            text: text.textContent.substr(index, 1)
                        }), text)
                        text.textContent = text.textContent.substr(index + 1);
                    } else {
                        console.warn('letter missing')
                    }
                    break;
                }
            }
        }
    }
    if (letters[e.key] && e.altKey) {
        letters[e.key].button.click();
    }
})

window.addEventListener('keyup', e => {

    if (e.key == 'Alt') {
        for (const value of Object.values(letters)) {
            value.actionLetter.replaceWith(document.createTextNode(value.actionLetter.textContent));
        }
        letters = {};
    }
})
document.body.append(new NotificationsRenderer());

addEventListener('error', e => {

    if (new URL(e.filename).origin == document.location.origin) {
        modal("Wystąpił błąd", "error")
    }
});
addEventListener('unhandledrejection', e => {

    modal("Wystąpił błąd", "error")
});
