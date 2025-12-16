<?php
/**
 * Courses search panel partial (no <html> wrapper) intended to be embedded
 * inside another view (e.g. student dashboard).
 *
 * Expects:
 *  - $courses (array)
 *  - optional $searchTerm string
 *
 * Adjust field names if your courses table uses different columns.
 */
?>
<div class="row mb-4">
  <div class="col-md-8">
    <form id="searchForm" class="d-flex" method="get" action="<?= site_url('courses/search') ?>">
      <div class="input-group">
        <input
          type="search"
          id="searchInput"
          name="search_term"
          class="form-control"
          placeholder="Search courses..."
          aria-label="Search courses"
          value="<?= isset($searchTerm) ? esc($searchTerm) : '' ?>"
          autocomplete="off"
        />
        <button class="btn btn-outline-primary" type="submit">
          <i class="bi bi-search"></i> Search
        </button>
      </div>
    </form>
  </div>

  <div class="col-md-4 text-end align-self-center">
    <button id="clearSearch" class="btn btn-link">Clear</button>
  </div>
</div>

<div id="coursesContainer" class="row">
  <?php if (empty($courses)): ?>
    <div class="col-12">
      <div class="alert alert-info">No courses available.</div>
    </div>
  <?php else: ?>
    <?php foreach ($courses as $c):
      $id    = (int) ($c['id'] ?? $c['course_id'] ?? 0);
      $title = $c['course_name'] ?? $c['title'] ?? '';
      $code  = $c['code'] ?? '';
      $desc  = $c['course_description'] ?? $c['description'] ?? '';
      $dataTitle = strtolower($title);
      $dataDesc  = strtolower($desc);
    ?>
      <div class="col-md-4 mb-3 course-card" data-title="<?= esc($dataTitle) ?>" data-desc="<?= esc($dataDesc) ?>">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <?= esc($title) ?>
              <?= $code ? '<small class="text-muted">(' . esc($code) . ')</small>' : '' ?>
            </h5>
            <p class="card-text small"><?= esc($desc) ?></p>
          </div>
          <div class="card-footer bg-transparent">
            <a href="<?= site_url('courses/' . $id) ?>" class="btn btn-sm btn-primary">View</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div id="noResultsMessage" class="mt-3" style="display: none;">
  <div class="alert alert-info">No courses found matching your search.</div>
</div>

<script>
/*
 * Embedded client-side filtering + AJAX server search (vanilla JS).
 * This script assumes it is included once per page. If you embed multiple times, avoid duplicate execution.
 */
(function () {
  var searchInput = document.getElementById('searchInput');
  var coursesContainer = document.getElementById('coursesContainer');
  var noResultsMessage = document.getElementById('noResultsMessage');
  var searchForm = document.getElementById('searchForm');
  var clearBtn = document.getElementById('clearSearch');

  function escapeHtml(s) {
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function clientFilter(q) {
    var clips = coursesContainer.querySelectorAll('.course-card');
    if (!clips.length) {
      showNoResults(true);
      return;
    }

    q = (q || '').toLowerCase().trim();
    if (!q) {
      clips.forEach(function (el) { el.style.display = ''; });
      showNoResults(false);
      return;
    }

    var shown = 0;
    clips.forEach(function (el) {
      var title = (el.getAttribute('data-title') || '').toLowerCase();
      var desc  = (el.getAttribute('data-desc') || '').toLowerCase();
      if (title.indexOf(q) !== -1 || desc.indexOf(q) !== -1) {
        el.style.display = '';
        shown++;
      } else {
        el.style.display = 'none';
      }
    });

    showNoResults(shown === 0);
  }

  function showNoResults(show) {
    noResultsMessage.style.display = show ? '' : 'none';
  }

  function buildCardsFromJSON(arr) {
    coursesContainer.innerHTML = '';
    if (!arr || !arr.length) {
      showNoResults(true);
      return;
    }
    var frag = document.createDocumentFragment();
    arr.forEach(function (c) {
      var id    = c.id || c.course_id || 0;
      var title = c.course_name || c.title || '';
      var code  = c.code || '';
      var desc  = c.course_description || c.description || '';

      var col = document.createElement('div');
      col.className = 'col-md-4 mb-3 course-card';
      col.setAttribute('data-title', title.toLowerCase());
      col.setAttribute('data-desc', desc.toLowerCase());

      col.innerHTML = '' +
        '<div class="card h-100">' +
          '<div class="card-body">' +
            '<h5 class="card-title">' + escapeHtml(title) +
              (code ? ' <small class="text-muted">(' + escapeHtml(code) + ')</small>' : '') +
            '</h5>' +
            '<p class="card-text small">' + escapeHtml(desc) + '</p>' +
          '</div>' +
          '<div class="card-footer bg-transparent">' +
            '<a href="<?= site_url('courses') ?>/' + (parseInt(id) || 0) + '" class="btn btn-sm btn-primary">View</a>' +
          '</div>' +
        '</div>';

      frag.appendChild(col);
    });

    coursesContainer.appendChild(frag);
    showNoResults(false);
  }

  // instant client-side filtering
  searchInput.addEventListener('input', function (e) {
    clientFilter(e.target.value);
  });

  clearBtn.addEventListener('click', function (e) {
    e.preventDefault();
    searchInput.value = '';
    clientFilter('');
  });

  searchForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var q = searchInput.value.trim();
    var url = searchForm.getAttribute('action') + '?search_term=' + encodeURIComponent(q);

    fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    })
    .then(function (res) {
      if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
      return res.json();
    })
    .then(function (data) {
      buildCardsFromJSON(data);
    })
    .catch(function (err) {
      console.error('Search request failed', err);
      coursesContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Search failed. Check console for details.</div></div>';
      showNoResults(false);
    });
  });

  // initial check
  (function () {
    var initialCards = coursesContainer.querySelectorAll('.course-card');
    if (!initialCards.length) {
      showNoResults(true);
    } else {
      showNoResults(false);
    }
  })();

})();
</script>