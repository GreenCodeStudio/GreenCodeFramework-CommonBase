import {Ajax} from "../../Core/js/ajax";

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