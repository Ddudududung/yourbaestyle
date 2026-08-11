/* ============================================================
   YOURBAESTYLE PINK THEME NOTIFICATION MANAGER (TOAST)
   ============================================================ */

class NotificationManager {
  constructor() {
    this.notifications = [];
    this.initContainer();
    this.interceptAlert();
    this.interceptFormConfirms();
  }

  initContainer() {
    let container = document.getElementById('notifications-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'notifications-container';
      container.className = 'notifications-container';
      document.body.appendChild(container);
    }
    this.container = container;
  }

  interceptAlert() {
    // Intercept native browser alert calls so all alert('...') calls render as custom pink theme toasts!
    if (typeof window !== 'undefined') {
      window.alert = (message, title = 'Notifikasi') => {
        const lower = String(message || '').toLowerCase();
        if (
          lower.includes('gagal') ||
          lower.includes('error') ||
          lower.includes('salah') ||
          lower.includes('tidak') ||
          lower.includes('tercapai') ||
          lower.includes('batal')
        ) {
          this.error(message, title || 'Perhatian!');
        } else if (lower.includes('berhasil') || lower.includes('sukses')) {
          this.success(message, title || 'Sukses!');
        } else {
          this.pink(message, title || 'Yourbaestyle');
        }
      };
    }
  }

  show(message, type = 'info', title = '', duration = 4500) {
    if (!this.container) {
      this.initContainer();
    }

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const iconMap = {
      success: '<i class="bi bi-check-circle-fill"></i>',
      error: '<i class="bi bi-x-circle-fill"></i>',
      danger: '<i class="bi bi-x-circle-fill"></i>',
      warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
      info: '<i class="bi bi-info-circle-fill"></i>',
      pink: '<i class="bi bi-heart-fill"></i>',
    };

    const iconHtml = iconMap[type] || '<i class="bi bi-bell-fill"></i>';
    const defaultTitles = {
      success: 'Berhasil!',
      error: 'Terjadi Kesalahan!',
      danger: 'Terjadi Kesalahan!',
      warning: 'Perhatian!',
      info: 'Informasi',
      pink: 'Yourbaestyle 🌸',
    };

    const displayTitle = title || defaultTitles[type] || 'Notifikasi';

    notification.innerHTML = `
      <div class="notification-icon-wrap">
        ${iconHtml}
      </div>
      <div class="notification-message">
        <div class="notification-title">${displayTitle}</div>
        <div class="notification-text">${message}</div>
      </div>
      <button class="notification-close" aria-label="Tutup" title="Tutup">&times;</button>
      ${duration > 0 ? `<div class="notification-progress" style="animation-duration: ${duration}ms;"></div>` : ''}
    `;

    // Close button click
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      this.remove(notification);
    });

    // Dismiss on toast click
    notification.addEventListener('click', (e) => {
      if (e.target !== closeBtn && !closeBtn.contains(e.target)) {
        this.remove(notification);
      }
    });

    // Pause dismissal timer on mouse hover
    let timeoutId = null;
    let startTime = Date.now();
    let remainingTime = duration;

    const startTimer = (ms) => {
      if (ms <= 0) return;
      startTime = Date.now();
      timeoutId = setTimeout(() => this.remove(notification), ms);
    };

    if (duration > 0) {
      startTimer(remainingTime);

      const progressEl = notification.querySelector('.notification-progress');

      notification.addEventListener('mouseenter', () => {
        if (timeoutId) {
          clearTimeout(timeoutId);
          timeoutId = null;
          remainingTime -= Date.now() - startTime;
          if (progressEl) {
            progressEl.style.animationPlayState = 'paused';
          }
        }
      });

      notification.addEventListener('mouseleave', () => {
        if (remainingTime > 0) {
          if (progressEl) {
            progressEl.style.animationPlayState = 'running';
          }
          startTimer(remainingTime);
        }
      });
    }

    this.container.appendChild(notification);
    this.notifications.push(notification);

    return notification;
  }

  remove(notification) {
    if (!notification || notification.classList.contains('removing')) return;

    notification.classList.add('removing');
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
      this.notifications = this.notifications.filter((n) => n !== notification);
    }, 320);
  }

  success(message, title = 'Berhasil!', duration = 4500) {
    return this.show(message, 'success', title, duration);
  }

  error(message, title = 'Gagal!', duration = 5500) {
    return this.show(message, 'error', title, duration);
  }

  danger(message, title = 'Gagal!', duration = 5500) {
    return this.show(message, 'danger', title, duration);
  }

  warning(message, title = 'Perhatian!', duration = 5000) {
    return this.show(message, 'warning', title, duration);
  }

  info(message, title = 'Informasi', duration = 4500) {
    return this.show(message, 'info', title, duration);
  }

  pink(message, title = 'Yourbaestyle 🌸', duration = 4500) {
    return this.show(message, 'pink', title, duration);
  }

  confirm(options = {}) {
    return new Promise((resolve) => {
      const message = typeof options === 'string' ? options : (options.message || 'Apakah Anda yakin?');
      const title = options.title || 'Konfirmasi Hapus 🌸';
      const confirmText = options.confirmText || 'Ya, Hapus';
      const cancelText = options.cancelText || 'Batal';
      const icon = options.icon || '<i class="bi bi-trash3-fill"></i>';

      let modalOverlay = document.getElementById('yb-confirm-modal');
      if (!modalOverlay) {
        modalOverlay = document.createElement('div');
        modalOverlay.id = 'yb-confirm-modal';
        modalOverlay.className = 'yb-modal-overlay';
        modalOverlay.innerHTML = `
          <div class="yb-modal-card">
            <div class="yb-modal-icon-wrap" id="yb-modal-icon">${icon}</div>
            <div class="yb-modal-title" id="yb-modal-title">${title}</div>
            <div class="yb-modal-text" id="yb-modal-text">${message}</div>
            <div class="yb-modal-actions">
              <button type="button" class="yb-modal-btn yb-modal-btn-cancel" id="yb-modal-btn-cancel">${cancelText}</button>
              <button type="button" class="yb-modal-btn yb-modal-btn-confirm" id="yb-modal-btn-confirm">${confirmText}</button>
            </div>
          </div>
        `;
        document.body.appendChild(modalOverlay);
      } else {
        document.getElementById('yb-modal-icon').innerHTML = icon;
        document.getElementById('yb-modal-title').textContent = title;
        document.getElementById('yb-modal-text').textContent = message;
        document.getElementById('yb-modal-btn-cancel').textContent = cancelText;
        document.getElementById('yb-modal-btn-confirm').textContent = confirmText;
      }

      const btnConfirm = document.getElementById('yb-modal-btn-confirm');
      const btnCancel = document.getElementById('yb-modal-btn-cancel');

      const closeModal = (result) => {
        modalOverlay.classList.remove('show');
        setTimeout(() => {
          resolve(result);
        }, 220);
      };

      const newConfirm = btnConfirm.cloneNode(true);
      const newCancel = btnCancel.cloneNode(true);
      btnConfirm.parentNode.replaceChild(newConfirm, btnConfirm);
      btnCancel.parentNode.replaceChild(newCancel, btnCancel);

      newConfirm.addEventListener('click', () => closeModal(true));
      newCancel.addEventListener('click', () => closeModal(false));

      modalOverlay.onclick = (e) => {
        if (e.target === modalOverlay) closeModal(false);
      };

      requestAnimationFrame(() => {
        modalOverlay.classList.add('show');
      });
    });
  }

  interceptFormConfirms() {
    if (typeof document === 'undefined') return;

    document.addEventListener('submit', (e) => {
      const form = e.target;
      if (form.dataset.ybConfirmed === 'true') {
        delete form.dataset.ybConfirmed;
        return;
      }

      const onsubmitAttr = form.getAttribute('onsubmit');
      if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let match = onsubmitAttr.match(/confirm\s*\(\s*['"](.*?)['"]\s*\)/);
        let msg = match ? match[1] : 'Apakah Anda yakin ingin menghapus data ini?';

        this.confirm({
          title: 'Konfirmasi Hapus 🌸',
          message: msg,
          confirmText: 'Ya, Hapus',
          cancelText: 'Batal'
        }).then((confirmed) => {
          if (confirmed) {
            form.dataset.ybConfirmed = 'true';
            form.submit();
          }
        });
      }
    }, true);
  }

  clearAll() {
    [...this.notifications].forEach((n) => this.remove(n));
  }
}

// Global instance
window.notify = new NotificationManager();

// Shorthand helpers
window.notifySuccess = (msg, title) => window.notify.success(msg, title);
window.notifyError = (msg, title) => window.notify.error(msg, title);
window.notifyWarning = (msg, title) => window.notify.warning(msg, title);
window.notifyInfo = (msg, title) => window.notify.info(msg, title);
window.notifyPink = (msg, title) => window.notify.pink(msg, title);
window.ybConfirm = (options) => window.notify.confirm(options);
