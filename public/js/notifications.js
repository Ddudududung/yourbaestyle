/* ============================================================
   YOURBAESTYLE PINK THEME NOTIFICATION MANAGER (TOAST)
   ============================================================ */

class NotificationManager {
  constructor() {
    this.notifications = [];
    this.initContainer();
    this.interceptAlert();
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
