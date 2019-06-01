import {Ajax} from "../../Core/js/ajax";
import {TableManager} from "../../Core/js/table";

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
