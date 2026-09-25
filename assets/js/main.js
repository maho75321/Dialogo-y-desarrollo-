$(function () {
    $('.navbar-toggler').click(function () {
        $('body').toggleClass('noscroll');
        $('header').toggleClass('active');
    });

    $(window).on('scroll', function () {
        if ($(window).scrollTop() >= 80) {
            $('#site-header').addClass('nav-fixed');
        } else {
            $('#site-header').removeClass('nav-fixed');
        }

        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            $('#movetop').show();
        } else {
            $('#movetop').hide();
        }
    });

    $('#movetop').on('click', function () {
        $('html, body').scrollTop(0);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('modal-media');

    if (!modal) {
        return;
    }

    var player = document.getElementById('modal-media-player');
    var titulo = document.getElementById('modal-media-titulo');
    var btnCerrar = modal.querySelector('.modal-media-cerrar');

    function cerrarModal() {
        // Al cerrar se elimina el reproductor: deja de sonar y no hay autoplay.
        player.innerHTML = '';
        modal.hidden = true;
        document.body.classList.remove('noscroll');
    }

    modal.querySelectorAll('[data-modal-cerrar]').forEach(function (el) {
        el.addEventListener('click', cerrarModal);
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && !modal.hidden) {
            cerrarModal();
        }
    });

    document.querySelectorAll('[data-modal-abrir]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modo = btn.getAttribute('data-modo') || '';
            var src = btn.getAttribute('data-src') || '';

            titulo.textContent = btn.getAttribute('data-titulo') || '';
            player.innerHTML = '';

            if (modo === 'audio' && src !== '') {
                var audio = document.createElement('audio');
                audio.controls = true;
                audio.preload = 'none';
                audio.src = src;
                player.appendChild(audio);
            } else if (modo === 'iframe' && /^https:\/\/[^"'\s]+$/.test(src)) {
                var frame = document.createElement('iframe');
                frame.src = src;
                frame.title = titulo.textContent;
                frame.setAttribute('loading', 'lazy');
                frame.setAttribute('allow', 'autoplay; encrypted-media; fullscreen; picture-in-picture');
                frame.allowFullscreen = true;
                player.appendChild(frame);
            } else {
                return;
            }

            modal.hidden = false;
            document.body.classList.add('noscroll');

            if (btnCerrar) {
                btnCerrar.focus();
            }
        });
    });
});
