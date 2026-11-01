/**
 * SAMSARA Mount Cikuray Hiking Trail - Interactive Core Module
 * Modular, maintainable, scalable JavaScript architecture
 */

document.addEventListener('DOMContentLoaded', () => {
  initNavbarScroll();
  initMobileMenu();
  initLeafletMap();
  initElevationChart();
  initSimaksiCalculator();
  initGearChecklist();
  initFaqAccordion();
  initLightboxModal();
  initWeatherClock();
});

/* --------------------------------------------------------------------------
   1. Navbar & Navigation
   -------------------------------------------------------------------------- */
function initNavbarScroll() {
  const navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

function initMobileMenu() {
  const toggleBtn = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');
  if (!toggleBtn || !navMenu) return;

  toggleBtn.addEventListener('click', () => {
    navMenu.classList.toggle('open');
    const icon = toggleBtn.querySelector('i');
    if (icon) {
      if (navMenu.classList.contains('open')) {
        icon.className = 'fas fa-times';
      } else {
        icon.className = 'fas fa-bars';
      }
    }
  });

  // Close when clicking a nav link
  navMenu.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('open');
      const icon = toggleBtn.querySelector('i');
      if (icon) icon.className = 'fas fa-bars';
    });
  });
}

/* --------------------------------------------------------------------------
   2. Interactive Leaflet GPS Trail Map
   -------------------------------------------------------------------------- */
let samsaraMap = null;
let checkpointMarkers = {};

function initLeafletMap() {
  const mapElement = document.getElementById('samsara-leaflet-map');
  if (!mapElement || typeof L === 'undefined' || typeof SAMSARA_TRAIL_DATA === 'undefined') {
    return;
  }

  // Basecamp & Puncak center
  const centerLat = -7.3275;
  const centerLng = 107.8350;

  samsaraMap = L.map('samsara-leaflet-map', {
    center: [centerLat, centerLng],
    zoom: 13,
    zoomControl: true,
    scrollWheelZoom: false
  });

  // Base Tile Layers (100% Free, No Watermark, No API Key Required)
  const baseLayers = {
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
      maxZoom: 19
    }),
    topo: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community',
      maxZoom: 19
    }),
    dark: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19
    })
  };

  // Default Layer: Satelit HD (Esri)
  let currentLayerKey = 'satellite';
  baseLayers.satellite.addTo(samsaraMap);

  // Setup Layer Switcher Buttons
  document.querySelectorAll('.map-layer-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const selected = btn.getAttribute('data-layer');
      if (selected === currentLayerKey) return;

      document.querySelectorAll('.map-layer-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Remove current layer
      samsaraMap.removeLayer(baseLayers[currentLayerKey]);

      // Handle dark mode tile class
      if (selected === 'dark') {
        mapElement.classList.add('dark-mode-tiles');
      } else {
        mapElement.classList.remove('dark-mode-tiles');
      }

      // Add new layer
      baseLayers[selected].addTo(samsaraMap);
      currentLayerKey = selected;
    });
  });

  // Draw real GPS Track Polyline
  if (SAMSARA_TRAIL_DATA.gpsTrack && SAMSARA_TRAIL_DATA.gpsTrack.length > 0) {
    const latLngs = SAMSARA_TRAIL_DATA.gpsTrack.map(coord => [coord[0], coord[1]]);
    
    // Outer glow line
    L.polyline(latLngs, {
      color: '#f59e0b',
      weight: 6,
      opacity: 0.5,
      smoothFactor: 1
    }).addTo(samsaraMap);

    // Inner bright trail line
    const trailLine = L.polyline(latLngs, {
      color: '#10b981',
      weight: 3.5,
      opacity: 0.95,
      dashArray: '6, 6',
      smoothFactor: 1
    }).addTo(samsaraMap);

    // Fit map bounds to track
    samsaraMap.fitBounds(trailLine.getBounds(), { padding: [45, 45] });
  }

  // Place custom checkpoint markers
  SAMSARA_TRAIL_DATA.checkpoints.forEach(cp => {
    const isPuncak = cp.id === 'puncak';
    const isBC = cp.id === 'basecamp';

    // Custom HTML marker icon
    const iconHtml = `
      <div style="
        background: ${isPuncak ? 'linear-gradient(135deg, #f59e0b, #d97706)' : isBC ? 'linear-gradient(135deg, #38bdf8, #0284c7)' : 'linear-gradient(135deg, #10b981, #059669)'};
        color: #0b100e;
        border: 2px solid #ffffff;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 11px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.65);
        cursor: pointer;
        transition: transform 0.2s;
      ">
        ${cp.posNumber}
      </div>
    `;

    const customIcon = L.divIcon({
      html: iconHtml,
      className: 'samsara-map-marker',
      iconSize: [34, 34],
      iconAnchor: [17, 17]
    });

    const marker = L.marker([cp.lat, cp.lng], { icon: customIcon }).addTo(samsaraMap);

    const popupContent = `
      <div style="font-family: 'Plus Jakarta Sans', sans-serif; min-width: 230px; color: #f1f5f3;">
        <img src="${cp.image}" alt="${cp.name}" style="width: 100%; height: 115px; object-fit: cover; border-radius: 6px; margin-bottom: 8px;">
        <div style="font-weight: 800; font-size: 15px; color: #fff; margin-bottom: 2px;">${cp.name}</div>
        <div style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-bottom: 6px;">Elevasi: ${cp.elevation} MDPL</div>
        <p style="font-size: 11.5px; color: #94a3b8; line-height: 1.4; margin: 0 0 8px;">${cp.tagline}</p>
        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #34d399; font-weight: 600;">
          <span>⏱ ${cp.estimatedTime}</span>
          <span>${cp.campArea ? '⛺ Camp Area' : '🚫 No Camp'}</span>
        </div>
      </div>
    `;

    marker.bindPopup(popupContent, { offset: [0, -10] });
    checkpointMarkers[cp.id] = marker;
  });

  // Setup quick focus buttons
  document.querySelectorAll('.map-quick-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      document.querySelectorAll('.map-quick-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const targetId = btn.getAttribute('data-pos');
      if (targetId === 'all') {
        const latLngs = SAMSARA_TRAIL_DATA.gpsTrack.map(coord => [coord[0], coord[1]]);
        samsaraMap.fitBounds(latLngs, { padding: [45, 45] });
      } else if (checkpointMarkers[targetId]) {
        const marker = checkpointMarkers[targetId];
        samsaraMap.setView(marker.getLatLng(), 15, { animate: true });
        marker.openPopup();
      }
    });
  });
}

// Function to focus on marker from checkpoint card
window.focusMapCheckpoint = function(posId) {
  const mapSection = document.getElementById('peta-gps') || document.getElementById('rute-peta');
  if (mapSection) {
    mapSection.scrollIntoView({ behavior: 'smooth' });
  }
  setTimeout(() => {
    if (samsaraMap && checkpointMarkers[posId]) {
      const marker = checkpointMarkers[posId];
      samsaraMap.setView(marker.getLatLng(), 15, { animate: true });
      marker.openPopup();
    }
  }, 400);
};

/* --------------------------------------------------------------------------
   3. Interactive Elevation Profile Canvas
   -------------------------------------------------------------------------- */
function initElevationChart() {
  const canvas = document.getElementById('elevation-canvas');
  if (!canvas || typeof SAMSARA_TRAIL_DATA === 'undefined') return;

  function renderChart() {
    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    if (rect.width === 0 || rect.height === 0) return;
    
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    // Progressive elevation milestones for clean visualization
    const milestones = [
      { name: 'BC Cigedug', pos: 'BC', elev: 1245, dist: 0 },
      { name: 'Pos 1 Tanjakan EOD', pos: 'POS 1', elev: 1424, dist: 1.5 },
      { name: 'Pos 2 Batu Gendrung', pos: 'POS 2', elev: 1682, dist: 2.8 },
      { name: 'Jembatan Rimba', pos: 'JMB', elev: 1942, dist: 3.9 },
      { name: 'Pos 3 Alam Rimba', pos: 'POS 3', elev: 2096, dist: 4.6 },
      { name: 'Pos 4 Samudra Awan', pos: 'POS 4', elev: 2257, dist: 5.4 },
      { name: 'Puncak Cikuray', pos: 'PUNCAK', elev: 2821, dist: 6.8 }
    ];

    const minElev = 1100;
    const maxElev = 2950;
    const width = rect.width;
    const height = rect.height;
    const paddingX = 45;
    const paddingY = 32;

    function getY(elev) {
      return height - paddingY - ((elev - minElev) / (maxElev - minElev)) * (height - 2 * paddingY);
    }

    function getX(dist) {
      const maxDist = milestones[milestones.length - 1].dist;
      return paddingX + (dist / maxDist) * (width - 2 * paddingX);
    }

    ctx.clearRect(0, 0, width, height);

    // Draw Grid Lines & elevation labels
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
    ctx.lineWidth = 1;
    [1250, 1600, 2000, 2400, 2821].forEach(level => {
      const y = getY(level);
      ctx.beginPath();
      ctx.moveTo(paddingX, y);
      ctx.lineTo(width - paddingX, y);
      ctx.stroke();

      ctx.fillStyle = level === 2821 ? '#f59e0b' : '#64748b';
      ctx.font = '10px "Plus Jakarta Sans", sans-serif';
      ctx.fillText(`${level}m`, 8, y + 3);
    });

    // Fill gradient under curve
    const gradient = ctx.createLinearGradient(0, getY(maxElev), 0, height);
    gradient.addColorStop(0, 'rgba(245, 158, 11, 0.45)');
    gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

    ctx.beginPath();
    ctx.moveTo(getX(milestones[0].dist), height - paddingY);
    ctx.lineTo(getX(milestones[0].dist), getY(milestones[0].elev));

    for (let i = 1; i < milestones.length; i++) {
      // Smooth bezier or direct line
      ctx.lineTo(getX(milestones[i].dist), getY(milestones[i].elev));
    }
    ctx.lineTo(getX(milestones[milestones.length - 1].dist), height - paddingY);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();

    // Draw stroke line
    ctx.beginPath();
    ctx.moveTo(getX(milestones[0].dist), getY(milestones[0].elev));
    for (let i = 1; i < milestones.length; i++) {
      ctx.lineTo(getX(milestones[i].dist), getY(milestones[i].elev));
    }
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 3;
    ctx.stroke();

    // Draw Key Milestone nodes
    milestones.forEach((m) => {
      const cx = getX(m.dist);
      const cy = getY(m.elev);

      // Outer glow
      ctx.beginPath();
      ctx.arc(cx, cy, 7, 0, Math.PI * 2);
      ctx.fillStyle = m.pos === 'PUNCAK' ? 'rgba(245, 158, 11, 0.3)' : 'rgba(16, 185, 129, 0.25)';
      ctx.fill();

      // Node point
      ctx.beginPath();
      ctx.arc(cx, cy, 4, 0, Math.PI * 2);
      ctx.fillStyle = m.pos === 'PUNCAK' ? '#f59e0b' : '#10b981';
      ctx.fill();
      ctx.lineWidth = 1.5;
      ctx.strokeStyle = '#ffffff';
      ctx.stroke();

      // Text label above
      ctx.fillStyle = '#f3f5f4';
      ctx.font = 'bold 9.5px "Outfit", sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(m.pos, cx, cy - 12);

      // Sub label below
      ctx.fillStyle = '#94a3b8';
      ctx.font = '8.5px "Plus Jakarta Sans", sans-serif';
      ctx.fillText(`${m.elev}m`, cx, cy + 16);
    });
  }

  renderChart();
  window.addEventListener('resize', renderChart);
}

/* --------------------------------------------------------------------------
   4. SIMAKSI Booking & Live Price Calculator
   -------------------------------------------------------------------------- */
function initSimaksiCalculator() {
  const climberCountInput = document.getElementById('climber-count');
  const climberTypeSelect = document.getElementById('climber-type');
  const needPorterCheck = document.getElementById('addon-porter');
  const needGuideCheck = document.getElementById('addon-guide');
  const hikeDateInput = document.getElementById('hike-date');
  const leadNameInput = document.getElementById('lead-name');
  const leadPhoneInput = document.getElementById('lead-phone');

  // Set min date to tomorrow
  if (hikeDateInput) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    hikeDateInput.min = tomorrow.toISOString().split('T')[0];
  }

  function calculateTotal() {
    const climbers = parseInt(climberCountInput?.value || 1, 10);
    const isWNA = climberTypeSelect?.value === 'wna';
    const hasPorter = needPorterCheck?.checked || false;
    const hasGuide = needGuideCheck?.checked || false;

    // Pricing
    const baseTicket = isWNA ? 100000 : 35000;
    const envInsuranceFee = 15000;
    const perPersonCost = (baseTicket + envInsuranceFee) * climbers;

    const porterFee = hasPorter ? 350000 : 0;
    const guideFee = hasGuide ? 300000 : 0;

    const grandTotal = perPersonCost + porterFee + guideFee;

    // Update UI elements
    const summaryClimbers = document.getElementById('calc-summary-climbers');
    const summarySimaksi = document.getElementById('calc-summary-simaksi');
    const summaryAddons = document.getElementById('calc-summary-addons');
    const summaryTotal = document.getElementById('calc-summary-total');

    if (summaryClimbers) summaryClimbers.textContent = `${climbers} Pendaki (${isWNA ? 'WNA' : 'WNI'})`;
    if (summarySimaksi) summarySimaksi.textContent = `Rp ${perPersonCost.toLocaleString('id-ID')}`;
    if (summaryAddons) {
      const addons = [];
      if (hasPorter) addons.push('Porter (Rp 350k)');
      if (hasGuide) addons.push('Guide (Rp 300k)');
      summaryAddons.textContent = addons.length > 0 ? addons.join(' + ') : 'Tidak Ada';
    }
    if (summaryTotal) summaryTotal.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;

    return {
      climbers,
      isWNA,
      hasPorter,
      hasGuide,
      grandTotal
    };
  }

  // Attach event listeners
  [climberCountInput, climberTypeSelect, needPorterCheck, needGuideCheck].forEach(el => {
    if (el) el.addEventListener('input', calculateTotal);
    if (el) el.addEventListener('change', calculateTotal);
  });

  // WhatsApp Registration Generator
  const waBtn = document.getElementById('btn-submit-wa');
  if (waBtn) {
    waBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const name = leadNameInput?.value.trim() || 'Pendaki SAMSARA';
      const phone = leadPhoneInput?.value.trim() || '-';
      const date = hikeDateInput?.value || 'Segera';
      const calc = calculateTotal();

      const message = 
`Salam Lestari Admin Basecamp SAMSARA Cigedug!
Saya ingin melakukan pendaftaran SIMAKSI Pendakian Gunung Cikuray Jalur SAMSARA:

📋 *DATA PENDAKIAN*
• Ketua Rombongan: ${name}
• No. WhatsApp: ${phone}
• Tanggal Pendakian: ${date}
• Jumlah Anggota: ${calc.climbers} Orang (${calc.isWNA ? 'WNA' : 'WNI'})
• Layanan Porter: ${calc.hasPorter ? 'Ya (1 Porter)' : 'Tidak'}
• Layanan Guide: ${calc.hasGuide ? 'Ya (1 Guide)' : 'Tidak'}

💵 *ESTIMASI TOTAL RETRIBUSI*: Rp ${calc.grandTotal.toLocaleString('id-ID')}

Mohon konfirmasi ketersediaan kuota pendakian. Terima kasih!
*SAMSARA - Satuan Anak Muda Sadar Alam*`;

      const encodedMsg = encodeURIComponent(message);
      // Basecamp official WhatsApp number (can be replaced with user number)
      const waUrl = `https://wa.me/6281234567890?text=${encodedMsg}`;
      window.open(waUrl, '_blank');
    });
  }

  calculateTotal();
}

/* --------------------------------------------------------------------------
   5. Interactive Gear & Safety Checklist
   -------------------------------------------------------------------------- */
function initGearChecklist() {
  const checkboxes = document.querySelectorAll('.gear-check-input');
  const progressFill = document.getElementById('checklist-progress-fill');
  const statusBadge = document.getElementById('checklist-status-badge');
  if (!checkboxes.length) return;

  // Load state from localStorage
  const savedState = JSON.parse(localStorage.getItem('samsara_gear_checklist_v2') || '{}');
  checkboxes.forEach((cb, idx) => {
    if (savedState[idx] !== undefined) {
      cb.checked = savedState[idx];
    }
    const parent = cb.closest('.gear-item-line');
    if (parent) {
      if (cb.checked) {
        parent.classList.add('checked');
      } else {
        parent.classList.remove('checked');
      }
    }

    cb.addEventListener('change', () => {
      const p = cb.closest('.gear-item-line');
      if (p) {
        if (cb.checked) {
          p.classList.add('checked');
        } else {
          p.classList.remove('checked');
        }
      }
      updateChecklistProgress();
    });
  });

  function updateChecklistProgress() {
    const total = checkboxes.length;
    let checkedCount = 0;
    const currentState = {};

    checkboxes.forEach((cb, idx) => {
      if (cb.checked) {
        checkedCount++;
        currentState[idx] = true;
      } else {
        currentState[idx] = false;
      }
    });

    localStorage.setItem('samsara_gear_checklist_v2', JSON.stringify(currentState));

    const percent = Math.round((checkedCount / total) * 100);
    if (progressFill) progressFill.style.width = `${percent}%`;
    if (statusBadge) {
      statusBadge.textContent = `${checkedCount}/${total} Siap (${percent}%)`;
      if (percent >= 80) {
        statusBadge.className = 'badge-tag badge-open';
      } else {
        statusBadge.className = 'badge-tag badge-elev';
      }
    }
  }

  updateChecklistProgress();
}

/* --------------------------------------------------------------------------
   6. FAQ Accordion
   -------------------------------------------------------------------------- */
function initFaqAccordion() {
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
      const isActive = item.classList.contains('active');
      faqItems.forEach(other => other.classList.remove('active'));
      if (!isActive) {
        item.classList.add('active');
      }
    });
  });
}

/* --------------------------------------------------------------------------
   7. Lightbox Modal
   -------------------------------------------------------------------------- */
function initLightboxModal() {
  const overlay = document.getElementById('lightbox-modal');
  const modalImg = document.getElementById('modal-img-display');
  const modalTitle = document.getElementById('modal-title-display');
  const modalDesc = document.getElementById('modal-desc-display');

  if (!overlay) return;

  window.openLightbox = function(imgSrc, title, desc) {
    if (modalImg) modalImg.src = imgSrc;
    if (modalTitle) modalTitle.textContent = title;
    if (modalDesc) modalDesc.textContent = desc;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  function closeModal() {
    overlay.style.display = 'none';
    document.body.style.overflow = '';
  }

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.style.display === 'flex') {
      closeModal();
    }
  });
}

/* --------------------------------------------------------------------------
   8. Live Time & Weather Clock
   -------------------------------------------------------------------------- */
function initWeatherClock() {
  const timeElem = document.getElementById('basecamp-live-time');
  if (!timeElem) return;

  function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    timeElem.textContent = `${timeStr} WIB`;
  }
  updateClock();
  setInterval(updateClock, 1000);
}
