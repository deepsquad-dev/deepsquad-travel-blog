document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('trip-map');
  if (!mapEl || typeof L === 'undefined') {
    return;
  }

  const pins = JSON.parse(mapEl.dataset.pins || '[]');
  const canEdit = mapEl.dataset.canEdit === '1';
  const addUrl = mapEl.dataset.addUrl || '';

  const map = L.map(mapEl, { scrollWheelZoom: true }).setView([20, 0], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap',
  }).addTo(map);

  const bounds = [];
  pins.forEach((pin) => {
    const marker = L.marker([pin.lat, pin.lng]).addTo(map);
    const mediaHtml = (pin.media || []).map((m) => {
      if (m.kind === 'video') {
        return `<video controls src="${m.url}" style="max-width:180px"></video>`;
      }
      return `<img src="${m.url}" alt="${m.name || ''}" style="max-width:180px">`;
    }).join('');
    const editHtml = pin.edit_url
      ? `<div><a href="${pin.edit_url}">Edit pin</a></div>`
      : '';
    marker.bindPopup(
      `<strong>${pin.title}</strong>` +
      (pin.place_name ? `<div>${pin.place_name}</div>` : '') +
      (pin.taken_at ? `<div>${pin.taken_at}</div>` : '') +
      (pin.body ? `<p>${pin.body}</p>` : '') +
      mediaHtml +
      editHtml
    );
    bounds.push([pin.lat, pin.lng]);
  });

  if (bounds.length === 1) {
    map.setView(bounds[0], 11);
  } else if (bounds.length > 1) {
    map.fitBounds(bounds, { padding: [36, 36] });
  }

  if (canEdit && addUrl) {
    map.on('click', (event) => {
      const { lat, lng } = event.latlng;
      const url = new URL(addUrl, window.location.origin);
      url.searchParams.set('lat', lat.toFixed(7));
      url.searchParams.set('lng', lng.toFixed(7));
      window.location.href = url.toString();
    });
  }
});
