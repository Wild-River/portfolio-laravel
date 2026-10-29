// 公開ページ用のスクリプト（kadai09_auth の assets/js/lightbox.js を移植）

// ギャラリー画像をクリックで拡大表示し、前後の画像へ切り替える
(() => {
    const gallery = document.querySelector('.article-gallery');
    const lightbox = document.getElementById('lightbox');
    if (!gallery || !lightbox) return;

    const images = [...gallery.querySelectorAll('img')];
    const lightboxImg = lightbox.querySelector('.lightbox__img');
    const closeBtn = lightbox.querySelector('.lightbox__close');
    const prevBtn = lightbox.querySelector('.lightbox__nav--prev');
    const nextBtn = lightbox.querySelector('.lightbox__nav--next');
    const counter = lightbox.querySelector('.lightbox__counter');
    let current = 0;

    const show = (index) => {
        // 端まで行ったら反対側に戻る
        current = (index + images.length) % images.length;
        const img = images[current];
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
        if (counter) counter.textContent = `${current + 1} / ${images.length}`;
    };

    const open = (index) => {
        show(index);
        lightbox.hidden = false;
    };

    const close = () => {
        lightbox.hidden = true;
        lightboxImg.src = '';
    };

    const prev = () => show(current - 1);
    const next = () => show(current + 1);

    gallery.addEventListener('click', (e) => {
        const img = e.target.closest('img');
        if (!img) return;
        open(images.indexOf(img));
    });

    closeBtn.addEventListener('click', close);
    prevBtn?.addEventListener('click', prev);
    nextBtn?.addEventListener('click', next);

    lightbox.addEventListener('click', (e) => {
        // ドラッグで切り替えた直後のクリックでは閉じない
        if (dragged) {
            dragged = false;
            return;
        }
        if (e.target === lightbox) close();
    });

    document.addEventListener('keydown', (e) => {
        if (lightbox.hidden) return;
        if (e.key === 'Escape') close();
        if (images.length < 2) return;
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'ArrowRight') next();
    });

    // スマホのスワイプ・PC のマウスドラッグで左右に切り替える
    let startX = null;
    let dragged = false;

    // ブラウザ標準の画像ドラッグ（ゴースト画像）を止める
    lightboxImg.addEventListener('dragstart', (e) => e.preventDefault());

    lightbox.addEventListener('pointerdown', (e) => {
        if (images.length < 2 || e.target.closest('button')) return;
        startX = e.clientX;
        dragged = false;
    });

    lightbox.addEventListener('pointerup', (e) => {
        if (startX === null) return;
        const diff = e.clientX - startX;
        startX = null;
        if (Math.abs(diff) < 50) return;
        dragged = true;
        if (diff > 0) prev();
        else next();
    });

    lightbox.addEventListener('pointercancel', () => {
        startX = null;
    });
})();
