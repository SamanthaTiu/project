<?php
/**
 * Courses listing with search bar, client-side filtering, AJAX server search fallback.
 *
 * Expects:
 *  - $courses : array of course records (each record should contain id, course_name/title, course_description/description, code optional)
 *  - Optional: $term or $searchTerm for initial input value
 *
 * Place this file at app/Views/courses/index.php
 *
 * Notes:
 *  - This view uses vanilla JS (no jQuery required).
 *  - If your layout already includes Bootstrap and icons, remove the CDN links below.
 */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Courses</title>

  <!-- Bootstrap (remove if your layout already loads bootstrap) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Small helper so the "no results" message sits nicely */
    .no-results {
      display: none;
    }
  </style>
</head>
<body>
<div class="container py-4">

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
            value="<?= isset($searchTerm) ? esc($searchTerm) : (isset($term) ? esc($term) : '') ?>"
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

  <!-- No-results placeholder inserted when client-side filtering or AJAX returns nothing -->
  <div id="noResultsMessage" class="no-results mt-3">
    <div class="alert alert-info">No courses found matching your search.</div>
  </div>

</div>

<!-- Optional: bootstrap bundle (remove if already loaded in layout) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/*
 * Client-side filtering + AJAX server search on submit (vanilla JS)
 */
(function () {
  var searchInput = document.getElementById('searchInput');
  var coursesContainer = document.getElementById('coursesContainer');
  var noResultsMessage = document.getElementById('noResultsMessage');
  var searchForm = document.getElementById('searchForm');
  var clearBtn = document.getElementById('clearSearch');

  // Helper to escape HTML when building DOM from server JSON
  function escapeHtml(s) {
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  // Client-side instant filtering (filters DOM elements already loaded)
  function clientFilter(q) {
    var clips = coursesContainer.querySelectorAll('.course-card');
    if (!clips.length) {
      // If there are no cards in DOM, show no results
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

  // show or hide the no-results placeholder
  function showNoResults(show) {
    noResultsMessage.style.display = show ? '' : 'none';
  }

  // Build course cards from JSON array returned by the server
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

  // Input event for instant client-side filtering
  searchInput.addEventListener('input', function (e) {
    clientFilter(e.target.value);
  });

  // Clear button resets the input and shows all courses
  clearBtn.addEventListener('click', function (e) {
    e.preventDefault();
    searchInput.value = '';
    clientFilter('');
  });

  // Intercept form submit to perform AJAX server-side search and replace DOM
  searchForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var q = searchInput.value.trim();

    // If you want to rely ONLY on client-side filtering, uncomment:
    // clientFilter(q); return;

    // Use GET to avoid CSRF complexity for simple search
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
      // Expecting JSON array of course objects
      buildCardsFromJSON(data);
      // After replacing cards, client-side filtering will still work because new cards have data-title/data-desc.
    })
    .catch(function (err) {
      console.error('Search request failed', err);
      // Graceful fallback: show message
      coursesContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Search failed. Check console for details.</div></div>';
      showNoResults(false);
    });
  });

  // On page load, ensure initial no-results placeholder is hidden unless nothing is present
  document.addEventListener('DOMContentLoaded', function () {
    var initialCards = coursesContainer.querySelectorAll('.course-card');
    if (!initialCards.length) {
      showNoResults(true);
    } else {
      showNoResults(false);
    }
  });

})();
</script>
</body>
</html>