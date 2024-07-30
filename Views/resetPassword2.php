<form class="resetPasswordForm2">
    <section class="card" data-width="6">
        <header>
            <h1>Odzyskiwanie hasła</h1>
        </header>
        <div class="error hidden"></div>
        <label>
            <span>Email</span>
            <input name="username" autocomplete="username" disabled value="<?=htmlspecialchars($data['mail']??'')?>">
        </label>
        <label>
            <span>Kod</span>
            <input name="code"  <?=$data['code']>0?'disabled':''?> value="<?=htmlspecialchars($data['code']??'')?>">
        </label>
        <label>
            <span>Nowe hasło</span>
            <input name="password" type="password" autocomplete="new-password">
        </label>
        <label>
            <span>Powtórz nowe hasło</span>
            <input name="password2" type="password" autocomplete="new-password">
        </label>
        <label>
            <footer>
                <button class="button">Zmień hasło</button>
            </footer>
    </section>
</form>
