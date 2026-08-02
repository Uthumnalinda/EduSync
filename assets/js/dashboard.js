// Main dashboard client controller and charts renderer

document.addEventListener('DOMContentLoaded', () => {
  initDashboardCharts();
  initProfileDropdown();
  initAutoDismissAlerts();
});

// Auto-dismiss alert banners after 3 seconds
function initAutoDismissAlerts() {
  setTimeout(() => {
    // 1. Target standard alert elements
    const alerts = document.querySelectorAll('.alert, .alert-success, .alert-danger, .status-alert');
    alerts.forEach(alertEl => {
      alertEl.style.transition = 'opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
      alertEl.style.opacity = '0';
      alertEl.style.transform = 'translateY(-8px)';
      setTimeout(() => {
        alertEl.style.display = 'none';
      }, 500);
    });

    // 2. Target custom green/red message banners
    document.querySelectorAll('div').forEach(el => {
      const text = el.innerText || '';
      if ((text.includes('successfully') || text.includes('recorded!') || text.includes('updated!') || text.includes('deleted!')) && el.children.length === 0) {
        const banner = el.closest('div[style*="background"]') || el;
        if (banner) {
          banner.style.transition = 'opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
          banner.style.opacity = '0';
          banner.style.transform = 'translateY(-8px)';
          setTimeout(() => {
            banner.style.display = 'none';
          }, 500);
        }
      }
    });
  }, 3000);
}

/**
 * Toggle Admin Profile Dropdown Menu
 */
function initProfileDropdown() {
  const userPill = document.getElementById('userProfilePill');
  const dropdownMenu = document.getElementById('profileDropdownMenu');
  const bellBtn = document.getElementById('navbarBellBtn');
  const notifMenu = document.getElementById('notificationDropdownMenu');

  document.addEventListener('click', (e) => {
    if (dropdownMenu && userPill && !dropdownMenu.contains(e.target) && !userPill.contains(e.target)) {
      dropdownMenu.classList.remove('show');
      dropdownMenu.style.display = 'none';
    }
    if (notifMenu && bellBtn && !notifMenu.contains(e.target) && !bellBtn.contains(e.target)) {
      notifMenu.classList.remove('show');
      notifMenu.style.display = 'none';
    }
  });
}

/**
 * Mark All Notifications as Read via AJAX API call
 */
function markAllNotificationsRead() {
  fetch('api/mark_read.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // 1. Hide red bell badge counter
      const bellBadge = document.querySelector('.bell-badge-count');
      if (bellBadge) bellBadge.style.display = 'none';

      // 2. Update notification header badge text
      const notifHeaderBadge = document.getElementById('notifBadgeCount');
      if (notifHeaderBadge) notifHeaderBadge.textContent = '0 new';

      // 3. Hide mark read button
      const markBtn = document.getElementById('markReadBtn');
      if (markBtn) markBtn.style.display = 'none';

      // 4. Clear notification items list & display empty state
      const notifBody = document.querySelector('.notif-body');
      if (notifBody) {
        notifBody.innerHTML = `
          <div class="notif-empty-box" style="padding: 24px 16px; text-align: center; color: #94a3b8;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 8px; opacity: 0.6; display: block;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span style="font-size: 13px; font-weight: 500; display: block; color: #64748b;">No new notifications</span>
          </div>
        `;
      }

      // 5. Stop bell ringing animation
      const bellBtn = document.getElementById('navbarBellBtn');
      if (bellBtn) bellBtn.classList.remove('has-unread');
    }
  })
  .catch(err => console.error('Error marking notifications as read:', err));
}

/**
 * Initialize Dashboard Charts using Chart.js
 */
function initDashboardCharts() {
  // Verify Chart.js library is loaded
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js CDN is not loaded.');
    return;
  }

  // Check active system theme
  const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDarkMode ? '#cbd5e1' : '#64748b';
  const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9';

  // Set Chart.js global defaults
  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.color = textColor;

  // 1. Enrollment Trends Area Chart
  const enrollmentCanvas = document.getElementById('enrollmentChart');
  if (enrollmentCanvas && window.dashboardData && window.dashboardData.enrollmentTrends) {
    const trends = window.dashboardData.enrollmentTrends;
    const labels = trends.map(t => t.month);
    const dataValues = trends.map(t => t.students);

    const ctx = enrollmentCanvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.35)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Enrolled Students',
          data: dataValues,
          borderColor: '#2563eb',
          borderWidth: 3,
          backgroundColor: gradient,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#2563eb',
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            padding: 12,
            backgroundColor: '#0f172a',
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 }
          }
        },
        scales: {
          x: { 
            grid: { display: false },
            ticks: { color: textColor }
          },
          y: {
            grid: { color: gridColor },
            ticks: { color: textColor },
            beginAtZero: false
          }
        }
      }
    });
  }

  // 2. Course Distribution Doughnut Chart
  const courseDistCanvas = document.getElementById('courseDistChart');
  if (courseDistCanvas && window.dashboardData && window.dashboardData.courseDist) {
    const courses = window.dashboardData.courseDist;
    const labels = courses.map(c => c.name);
    const dataValues = courses.map(c => c.value);
    const colors = courses.map(c => c.color);

    new Chart(courseDistCanvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: dataValues,
          backgroundColor: colors,
          borderWidth: 2,
          borderColor: isDarkMode ? '#1e293b' : '#ffffff',
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        cutout: '70%'
      }
    });
  }

  // 3. Grade Breakdown Bar Chart
  const gradeCanvas = document.getElementById('gradeChart');
  if (gradeCanvas && window.dashboardData && window.dashboardData.gradeBreakdown) {
    const grades = window.dashboardData.gradeBreakdown;
    const labels = grades.map(g => g.grade);
    const dataValues = grades.map(g => g.count);

    new Chart(gradeCanvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Students Count',
          data: dataValues,
          backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6'],
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { 
            grid: { display: false },
            ticks: { color: textColor }
          },
          y: { 
            grid: { color: gridColor },
            ticks: { color: textColor },
            beginAtZero: true 
          }
        }
      }
    });
  }
}
