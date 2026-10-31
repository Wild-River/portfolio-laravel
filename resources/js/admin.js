// 管理画面用のスクリプト（kadai09_auth の assets/js/row-link.js を移植）

// 一覧表の行をクリックすると、その行の編集ページへ移動する
document.querySelectorAll('tr.row-link[data-href]').forEach((row) => {
    row.addEventListener('click', (event) => {
        // 行の中のリンク（編集アイコン）はそのまま動かす
        if (event.target.closest('a')) return;
        window.location.href = row.dataset.href;
    });
});

// モバイルでハンバーガーボタンを押すとサイドバーを開き、オーバーレイを押すと閉じる
(() => {
    const toggle = document.querySelector('.menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (!toggle || !sidebar || !overlay) return;

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => setOpen(true));
    overlay.addEventListener('click', () => setOpen(false));
})();
