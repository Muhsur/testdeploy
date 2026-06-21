function halo(){
  let bel = document.getElementById("tombol").value;
  alert(bel);
}

async function loadComments() {
  const list = document.getElementById('comment-list');

  if (!list) {
    return;
  }

  list.innerHTML = '<p>Memuat komentar...</p>';

  try {
    const response = await fetch('proses_form.php');
    const result = await response.json();

    if (!result.success || !Array.isArray(result.data) || result.data.length === 0) {
      list.innerHTML = '<p>Belum ada komentar.</p>';
      return;
    }

    list.innerHTML = result.data.map((comment) => `
      <div class="comment-item">
        <strong>${escapeHtml(comment.nama)}</strong>
        <small>${escapeHtml(comment.created_at)}</small>
        <p>${escapeHtml(comment.komentar)}</p>
      </div>
    `).join('');
  } catch (error) {
    list.innerHTML = '<p>Gagal memuat komentar.</p>';
  }
}

function escapeHtml(text) {
  return String(text)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('comment-form');
  const message = document.getElementById('comment-message');

  if (form) {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formData = new FormData(form);

      try {
        const response = await fetch('proses_form.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (message) {
          message.textContent = result.message;
        }

        if (result.success) {
          form.reset();
          loadComments();
        }
      } catch (error) {
        if (message) {
          message.textContent = 'Gagal mengirim komentar.';
        }
      }
    });
  }

  loadComments();
});


